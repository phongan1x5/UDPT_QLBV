FROM python:3.11-slim

RUN apt-get update && apt-get install -y gcc supervisor procps && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Create proper supervisor directories
RUN mkdir -p /var/log/supervisor /var/run/supervisor

# Create complete supervisor config
RUN echo '[unix_http_server]\n\
file=/var/run/supervisor/supervisor.sock\n\
chmod=0700\n\
\n\
[supervisord]\n\
nodaemon=true\n\
logfile=/var/log/supervisor/supervisord.log\n\
pidfile=/var/run/supervisor/supervisord.pid\n\
childlogdir=/var/log/supervisor\n\
loglevel=info\n\
\n\
[rpcinterface:supervisor]\n\
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface\n\
\n\
[supervisorctl]\n\
serverurl=unix:///var/run/supervisor/supervisor.sock\n\
\n\
[program:worker]\n\
command=python3 worker.py\n\
directory=/app\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
\n\
[program:api]\n\
command=uvicorn main:app --host 0.0.0.0 --port 6008\n\
directory=/app\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0' > /etc/supervisor/supervisord.conf

EXPOSE 6008

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]