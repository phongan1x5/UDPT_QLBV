import os
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

RABBITMQ_CONFIG = {
    'host': os.getenv('RABBITMQ_HOST', 'localhost'),
    'port': int(os.getenv('RABBITMQ_PORT', 5672)),
    'user': os.getenv('RABBITMQ_USER', 'guest'),
    'password': os.getenv('RABBITMQ_PASSWORD', 'guest'),
}

EMAIL_CONFIG = {
    'smtp_server': os.getenv('EMAIL_SMTP_SERVER', 'smtp.example.com'),
    'smtp_port': int(os.getenv('EMAIL_SMTP_PORT', 587)),
    'username': os.getenv('EMAIL_USERNAME', 'your_email@example.com'),
    'password': os.getenv('EMAIL_PASSWORD', 'your_password'),
}