import pika
from config import RABBITMQ_CONFIG

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
        
        # ✅ Create the exchange to match lab service
        channel.exchange_declare(exchange='events', exchange_type='fanout', durable=True)
        
        connection.close()
        return True, "Connection successful - exchange created/verified"
    except Exception as e:
        return False, str(e)

def publish_event(event_name, payload):
    """Enhanced publish_event matching lab service pattern"""
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

        # ✅ Match the notification service exchange settings
        channel.exchange_declare(
            exchange='events', 
            exchange_type='fanout',
            durable=True  # ✅ Same as lab service
        )

        # Publish the event
        message = f"{event_name}:{payload}"
        channel.basic_publish(
            exchange='events', 
            routing_key='', 
            body=message,
            properties=pika.BasicProperties(
                delivery_mode=2,  # ✅ Persistent messages
            )
        )
        
        print(f"✅ Event published successfully: {event_name}")
        connection.close()
        return True
        
    except Exception as e:
        print(f"❌ Failed to publish event: {e}")
        print(f"❌ Error type: {type(e).__name__}")
        raise e