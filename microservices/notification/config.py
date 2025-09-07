import os
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

RABBITMQ_CONFIG = {
    'host': 'hospital_rabbitmq',
    'port': 5672,
    'user': "guest",
    'password': "guest"
}

EMAIL_CONFIG = {
    'smtp_server': 'smtp.gmail.com',
    'smtp_port':  587,
    'username': "hospitalnotificationudpt@gmail.com",  # Replace with your test Gmail
    'password': "luai udty rmga lfef",  # Replace with your App Password
    'use_tls': True,  # Gmail requires TLS
    'from_name': 'Hospital Notification System'  # Friendly sender name
}
