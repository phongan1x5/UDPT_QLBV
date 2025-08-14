#!/usr/bin/env python3
import pika
import time
import sys
from notifications import send_email
from config import RABBITMQ_CONFIG
from sqlalchemy.orm import sessionmaker
from database import engine
from models import Notification

# Create database session
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

def store_notification(user_id, source_system, message):
    """Store notification in database"""
    db = SessionLocal()
    try:
        notification = Notification(
            userId=user_id,
            sourceSystem=source_system,
            message=message
        )
        db.add(notification)
        db.commit()
        print(f"✅ Stored notification for user {user_id}")
        return notification
    except Exception as e:
        print(f"❌ Database error: {e}")
        db.rollback()
    finally:
        db.close()

def process_message(ch, method, properties, body):
    """Process incoming messages"""
    try:
        message = body.decode('utf-8')
        print(f"📥 Received: {message}")
        
        # Parse message: "EventType:UserID:123, UserEmail:user@email.com, SourceSystem:Lab, Message:Your results..."
        if ':' not in message:
            print("❌ Invalid message format")
            ch.basic_ack(delivery_tag=method.delivery_tag)
            return
            
        event_type, payload = message.split(':', 1)
        print(f"🎯 Event: {event_type}")
        print(f"📄 Payload: {payload}")
        
        # Parse payload
        params = {}
        for param in payload.split(', '):
            if ':' in param:
                key, value = param.split(':', 1)
                params[key] = value
        
        user_id = params.get('UserID')
        user_email = params.get('UserEmail')
        source_system = params.get('SourceSystem', 'System')
        msg_content = params.get('Message')
        
        if user_id and user_email and msg_content:
            # Send email
            subject = f"Notification from {source_system}"
            send_email(user_email, subject, msg_content)
            
            # Store in database
            store_notification(user_id, source_system, msg_content)
            
            print(f"✅ Processed notification for {user_email}")
        else:
            print(f"❌ Missing required fields: UserID={user_id}, UserEmail={user_email}, Message={msg_content}")
        
        # Acknowledge message
        ch.basic_ack(delivery_tag=method.delivery_tag)
        
    except Exception as e:
        print(f"❌ Processing error: {e}")
        ch.basic_ack(delivery_tag=method.delivery_tag)

def main():
    """Main worker function"""
    print("🚀 Starting Notification Worker...")
    print(f"🔧 Connecting to RabbitMQ: {RABBITMQ_CONFIG['host']}:{RABBITMQ_CONFIG['port']}")
    
    # Wait for RabbitMQ to be ready
    max_retries = 10
    for attempt in range(max_retries):
        try:
            connection = pika.BlockingConnection(
                pika.ConnectionParameters(
                    host=RABBITMQ_CONFIG['host'],
                    port=RABBITMQ_CONFIG['port'],
                    credentials=pika.PlainCredentials(
                        RABBITMQ_CONFIG['user'],
                        RABBITMQ_CONFIG['password']
                    )
                )
            )
            channel = connection.channel()
            
            # Declare exchange
            channel.exchange_declare(
                exchange='events',
                exchange_type='fanout',
                durable=True
            )
            
            # Create queue
            result = channel.queue_declare(queue='', exclusive=True)
            queue_name = result.method.queue
            
            # Bind queue to exchange
            channel.queue_bind(exchange='events', queue=queue_name)
            
            print("✅ Connected to RabbitMQ successfully!")
            print(f"📥 Listening on queue: {queue_name}")
            print("👂 Waiting for messages... Press CTRL+C to exit")
            
            # Start consuming
            channel.basic_consume(
                queue=queue_name,
                on_message_callback=process_message
            )
            
            channel.start_consuming()
            break
            
        except pika.exceptions.AMQPConnectionError:
            print(f"⏳ Connection attempt {attempt + 1}/{max_retries} failed, retrying...")
            time.sleep(5)
        except KeyboardInterrupt:
            print("🛑 Worker stopped by user")
            if 'channel' in locals():
                channel.stop_consuming()
            if 'connection' in locals():
                connection.close()
            break
        except Exception as e:
            print(f"❌ Unexpected error: {e}")
            time.sleep(5)
    
    print("🔚 Worker shutdown complete")

if __name__ == "__main__":
    main()