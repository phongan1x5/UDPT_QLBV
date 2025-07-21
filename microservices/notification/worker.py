import pika
from notification import send_push_notification
from config import RABBITMQ_CONFIG

def handle_lab_result_available(payload):
    # Parse the payload into used_service_id and result
    payload_parts = payload.split(", ")
    if len(payload_parts) != 2:
        print("Invalid payload format for LabResultAvailable")
        return

    used_service_id = payload_parts[0].split(":")[1]  # Extract UsedServiceID
    result = payload_parts[1].split(":")[1]  # Extract Result

    send_push_notification("device123", f"Lab result is available for Used Service ID {used_service_id}: {result}")

def handle_appointment_reminder(payload):
    # Parse the payload into phone number, device ID, and message
    payload_parts = payload.split(", ")
    if len(payload_parts) != 3:
        print("Invalid payload format for AppointmentReminder")
        return

    phone_number = payload_parts[0].split(":")[1]  # Extract PatientPhoneNumber
    device_id = payload_parts[1].split(":")[1]  # Extract PatientDeviceID
    message = payload_parts[2].split(":")[1]  # Extract Message

    send_push_notification(device_id, message)
    print(f"Reminder sent to device {device_id}: {message}")

def callback(ch, method, properties, body):
    print(f"Raw message received: {body.decode()}")  # Debugging statement

    # Split the message into event_name and payload
    parts = body.decode().split(":", 1)  # Split into two parts: event_name and the rest of the payload
    if len(parts) != 2:
        print("Invalid message format")
        ch.basic_ack(delivery_tag=method.delivery_tag)
        return

    event_name, payload = parts
    print(f"Event Name: {event_name}, Payload: {payload}")

    # Handle events based on event_name
    if event_name == "LabResultAvailable":
        handle_lab_result_available(payload)
    elif event_name == "AppointmentReminder":
        handle_appointment_reminder(payload)
    else:
        print(f"Unhandled event: {event_name}")

    ch.basic_ack(delivery_tag=method.delivery_tag)

def start_worker():
    print("Connecting to RabbitMQ with the following config:")
    print(RABBITMQ_CONFIG)

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

    channel.exchange_declare(exchange='events', exchange_type='fanout')
    queue_name = channel.queue_declare(queue='', exclusive=True).method.queue
    channel.queue_bind(exchange='events', queue=queue_name)

    channel.basic_consume(queue=queue_name, on_message_callback=callback)
    print("Waiting for events...")
    channel.start_consuming()

if __name__ == "__main__":
    start_worker()