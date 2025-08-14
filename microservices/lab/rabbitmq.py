import pika
from config import RABBITMQ_CONFIG
import json

def test_connection():
    """Test RabbitMQ connection without publishing"""
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
        
        # ✅ FIX: Create the exchange instead of checking passively
        channel.exchange_declare(exchange='events', exchange_type='fanout', durable=True)
        
        connection.close()
        return True, "Connection successful - exchange created/verified"
    except Exception as e:
        return False, str(e)
    
def simple_connection_test():
    """Simple RabbitMQ connection test without exchange operations"""
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
        
        # Just test connection, don't do any operations
        channel = connection.channel()
        connection.close()
        
        return True, "Basic RabbitMQ connection successful"
    except Exception as e:
        return False, str(e)

def publish_event(event_name, payload):
    """Enhanced publish_event with better error handling and logging"""
    try:
        print(f"🔄 Attempting to publish event: {event_name}")
        print(f"📝 Payload: {payload}")
        print(f"🔧 RabbitMQ Config: {RABBITMQ_CONFIG}")
        
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

        # ✅ FIX: Match the notification service exchange settings
        channel.exchange_declare(
            exchange='events', 
            exchange_type='fanout',
            durable=True  # ✅ Changed to True to match notification service
        )

        # Publish the event
        message = f"{event_name}:{payload}"
        channel.basic_publish(
            exchange='events', 
            routing_key='', 
            body=message,
            properties=pika.BasicProperties(
                delivery_mode=2,  # ✅ Keep persistent for durable exchange
            )
        )
        
        print(f"✅ Event published successfully: {event_name}")
        connection.close()
        return True
        
    except Exception as e:
        print(f"❌ Failed to publish event: {e}")
        print(f"❌ Error type: {type(e).__name__}")
        raise e

def get_rabbitmq_status():
    """Get detailed RabbitMQ connection status"""
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
        
        # Get queue info
        method = channel.queue_declare(queue='', exclusive=True, passive=False)
        queue_name = method.method.queue
        
        connection.close()
        
        return {
            "connected": True,
            "host": RABBITMQ_CONFIG['host'],
            "port": RABBITMQ_CONFIG['port'],
            "user": RABBITMQ_CONFIG['user'],
            "test_queue": queue_name
        }
    except Exception as e:
        return {
            "connected": False,
            "error": str(e),
            "config": RABBITMQ_CONFIG
        }