# Use an official Python image as the base
FROM python:3.11-slim

# Set the working directory inside the container
WORKDIR /app

# Copy the requirements file and install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy the rest of the application code
COPY . .

# Expose the port the service will run on
EXPOSE 6004

# Command to run the service
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "6004"]