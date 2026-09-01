import os, django
os.environ.setdefault("DJANGO_SETTINGS_MODULE", "config.settings")
django.setup()
from django.db import connection
with connection.cursor() as cursor:
    cursor.execute("SELECT name FROM django_migrations WHERE app='vouchers' ORDER BY id DESC LIMIT 5")
    print(cursor.fetchall())
