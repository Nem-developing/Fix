import os
we_exit=False
## Récupération des variables et surcharges
if os.getenv("DB_HOSTNAME") is None:
    print("DB_HOSTNAME not found in environment variables, using default value")
    we_exit=True
if os.getenv("DB_NAME") is None:
    print("DB_NAME not found in environment variables, using default value")
    we_exit=True
if os.getenv("DB_USER") is None:
    print("DB_USER not found in environment variables, using default value")
    we_exit=True
if os.getenv("DB_PASSWORD") is None:
    print("DB_PASSWORD not found in environment variables, using default value")
    we_exit=True

if (we_exit):
    exit(1)

## Affectation des variables
DB_HOST = os.getenv("DB_HOSTNAME")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASSORD = os.getenv("DB_PASSWORD")
