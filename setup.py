import argparse
import subprocess
from pathlib import Path

import psycopg2
from psycopg2 import OperationalError

curr_dir = Path(__file__).parent
microservices_dir = curr_dir / "microservices"

microservices = [
    "appointment",
    "auth",
    "lab",
    "medical_record",
    "notification",
    "patient",
    "pharmacy",
    "staff",
]

db_owner = "anphong"
db_owner_password = "123123"
sql_script_path = curr_dir / "helper" / "setup_hospital_db.sql"


def conn_to_postgres(password):
    try:
        conn = psycopg2.connect(
            dbname="postgres",
            user="postgres",
            password=password,
            host="localhost",
            port="5432",
        )
        print("✅ Connection to postgres successful.")
        return conn
    except OperationalError as e:
        print("❌ Connection to postgres failed.")
        print("Details:", e)
        return None


def setup_envs(clean_install):
    for service in microservices:
        env_path = microservices_dir / service / ".env"

        if not clean_install and env_path.exists():
            print(f"Skipping existing .env for {service}")
            continue

        with open(env_path, "w") as env:
            env.write("POSTGRES_HOST=host.docker.internal")

            if service == "notification":
                continue
            #                 env.write(
            #                     """
            # RABBITMQ_HOST=localhost
            # RABBITMQ_PORT=5672
            # RABBITMQ_USER=guest
            # RABBITMQ_PASSWORD=guest

            # EMAIL_SMTP_SERVER=smtp.example.com
            # EMAIL_SMTP_PORT=587
            # EMAIL_USERNAME=your_email@example.com
            # EMAIL_PASSWORD=your_password
            # """
            #                 )

            print(f"Created .env for {service}")


def setup_user(conn, clean_install):
    conn.autocommit = True
    cur = conn.cursor()

    new_user = db_owner
    new_password = db_owner_password

    try:
        cur.execute("SELECT 1 FROM pg_roles WHERE rolname = %s;", (new_user,))
        exists = cur.fetchone() is not None

        if not clean_install and exists:
            print(f"✅ User '{new_user}' exists. Skipping creation.")
            return

        cur.execute(f"DROP USER IF EXISTS {new_user};")
        print(f"🗑️ User '{new_user}' dropped (if existed).")

        cur.execute(f"CREATE USER {new_user} WITH PASSWORD %s;", (new_password,))
        print(f"✅ User '{new_user}' created successfully.")
    except Exception as e:
        print("Error creating user:", e)

    try:
        cur.execute(f"ALTER USER {new_user} WITH SUPERUSER;")
        print(f"🔑 User '{new_user}' is now a superuser.")
    except Exception as e:
        print("Error granting privilege to user:", e)

    cur.close()


def run_sql_script_with_psql(sql_path, user, password, host, db, port):
    import os

    env = os.environ.copy()
    env["PGPASSWORD"] = password
    cmd = [
        "psql",
        "-U",
        user,
        "-h",
        host,
        "-p",
        str(port),
        "-d",
        db,
        "-f",
        str(sql_path),
    ]
    try:
        subprocess.run(cmd, check=True, env=env)
        print(f"✅ Ran SQL script with psql: {sql_path}")
    except subprocess.CalledProcessError as e:
        print(f"❌ Error running SQL script with psql: {e}")


def setup_dbs(conn, clean_install):
    conn.autocommit = True
    cur = conn.cursor()

    for service in microservices:
        if service == "notification":
            continue

        db_name = f"{service}_service"

        try:
            cur.execute("SELECT 1 FROM pg_database WHERE datname = %s;", (db_name,))
            exists = cur.fetchone() is not None

            if not clean_install and exists:
                print(f"✅ Database '{db_name}' exists. Skipping creation.")
                continue

            cur.execute(f"DROP DATABASE IF EXISTS {db_name};")
            print(f"🗑️ Database '{db_name}' dropped (if existed).")

            cur.execute(f"CREATE DATABASE {db_name} OWNER {db_owner};")
            print(f"📦 Database '{db_name}' created and owned by '{db_owner}'.")
        except Exception as e:
            print(f"Error creating database '{db_name}':", e)

    cur.close()


def cleanup_postgres(conn):
    conn.autocommit = True
    cur = conn.cursor()

    for service in microservices:
        db_name = f"{service}_service"

        try:
            cur.execute(
                """
                SELECT pg_terminate_backend(pid)
                FROM pg_stat_activity
                WHERE datname = %s AND pid <> pg_backend_pid();
                """,
                (db_name,),
            )

            cur.execute(f"DROP DATABASE IF EXISTS {db_name};")
            print(f"🗑️ Database '{db_name}' dropped (if existed).")
        except Exception as e:
            print(f"Error dropping database '{db_name}':", e)

    try:
        cur.execute(f"DROP USER IF EXISTS {db_owner};")
        print(f"🗑️ User '{db_owner}' dropped (if existed).")
    except Exception as e:
        print("Error dropping user:", e)

    cur.close()


parser = argparse.ArgumentParser(description="Options for setup.")

parser.add_argument("postgres_password", help="Password for the `postgres` user.")
parser.add_argument("--option", default="install", help="Install or uninstall.")
parser.add_argument(
    "--clean_install",
    type=lambda v: v.lower() in ("true", "1", "yes"),
    default=True,
    help="Clean install will delete and remake everything (dbs and envs). Set to False to only create if not exists.",
)

args = parser.parse_args()

postgres_conn = conn_to_postgres(args.postgres_password)

while postgres_conn == None:
    args.postgres_password = input(
        "Please provide a valid password for the `postgres` user: "
    )
    postgres_conn = conn_to_postgres(args.postgres_password)


if args.option == "uninstall":
    print(f"---- Executing uninstallation.")
    cleanup_postgres(postgres_conn)
else:
    print(f"---- Executing {'clean' if args.clean_install else 'normal'} installation.")

    setup_envs(args.clean_install)
    setup_user(postgres_conn, args.clean_install)
    # setup_dbs(postgres_conn, args.clean_install)

    run_sql_script_with_psql(
        sql_script_path,
        user="anphong",
        password=db_owner_password,
        host="localhost",
        db="postgres",
        port=5432,
    )

postgres_conn.close()
