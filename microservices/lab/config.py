import os
from dotenv import load_dotenv

# Load environment variables from .env file
# load_dotenv()

RABBITMQ_CONFIG = {
    'host': 'hospital_rabbitmq',  # ✅ Use container name
    'port':  5672,
    'user': 'guest',
    'password':'guest',
}