import pika
from config import RABBITMQ_CONFIG

def publish_event(event_name, payload):
    connection = pika.BlockingConnection(
        pika.ConnectionParameters(
            host=RABBITMQ_CONFIG['host'],
            port=RABBITMQ_CONFIG['port'],
            credentials=pika.PlainCredentials(
                RABBITMQ_CONFIG['user'], RABBITMQ_CONFIG['password']
            )
        )
    )
    channel = connection.channel()

    # Declare the exchange
    channel.exchange_declare(exchange='events', exchange_type='fanout')

    # Publish the event
    channel.basic_publish(exchange='events', routing_key='', body=f"{event_name}:{payload}")
    connection.close()