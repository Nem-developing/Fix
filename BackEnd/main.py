from dataclasses import dataclass
import json
from random import randint
from typing import Optional
from aiohttp import web
import mysql.connector

# CONST
API_VERSION = "1.0"

DB_HOST = "127.0.0.1"  # Ex : 127.0.0.1
DB_NAME = "exampledb"  # Ex : Fix
DB_USER = "exampleuser"  # Ex : nehemie
DB_PASSORD = "examplepass"  # Ex : B@rki@

HEADERS = {"Content-Type": "application/json"}

# VARS

status = "Green"  # Green = Tout est fonctionnel  Red = Database innacessible

########################
# OBJETS
########################


@dataclass
class body_sql:
    CONTENT: str
    ERROR: bool
    ERROR_MSG: Optional[str] = "None"


@dataclass
class ticket:
    id: int
    serveur: str
    objet: str
    description: str
    date: str
    heure: str
    utilisateur_emmeteur_du_ticket: str
    date_pec: str
    heure_pec: str
    date_fin: str
    heure_fin: str
    urgence: int
    etat: int
    technicien_affecte: str
    technicien_qui_archive: str


########################
# Fonctions de gestion
########################


def db_run(CMD):
    ERROR = False
    DATA = {}
    try:
        # établir une connexion à la base de données
        conn = mysql.connector.connect(
            host=DB_HOST, user=DB_USER, password=DB_PASSORD, database=DB_NAME
        )

        # créer un curseur pour exécuter les requêtes
        cursor = conn.cursor()

        cursor.execute(str(CMD))
        DATA = cursor.fetchall()
        cursor.close()
        conn.close()
    except mysql.connector.errors.ProgrammingError as error:
        MSG = "Erreur d'authentification MySQL: " + str(error)
        return body_sql(CONTENT=DATA, ERROR=True, ERROR_MSG=MSG)

    except mysql.connector.errors.DatabaseError as error:
        MSG = "Erreur de connexion MySQL " + str(error)
        return body_sql(CONTENT=DATA, ERROR=True, ERROR_MSG=MSG)
    return body_sql(CONTENT=DATA, ERROR=False)


def db_ok():
    OK = False
    try:
        REQ = db_run("SELECT * from tickets LIMIT 0")
        db_status = "Green"
        db_error = "None"

        if REQ.ERROR != False:
            db_status = "Red"
            db_error = REQ.ERROR_MSG
    except:
        db_status = "Red"
        db_error = "Cannot establish connexion with the database"

    return db_status, db_error


def display():
    db_status, db_error = db_ok()
    data = {
        "api_version": API_VERSION,
        "status": "running",
        "database": db_status,
        "database_error": db_error,
        "created_by": "Néhémie Barkia",
        "documentation": "https://github.com/Nem-developing/Fix/wiki",
    }
    return json.dumps(data, indent=4)


def check_if_everything_is_ok():
    db_status, db_error = db_ok()
    if db_error == True:
        return False
    return True


################
# Fonctions WEB
################


async def index(request):
    return web.json_response(json.loads(display()))


async def web_get_all_tikets(request):
    data = {"Error": False}
    if check_if_everything_is_ok() != True:
        data = {"Error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req = db_run(
            "SELECT id, serveur, objet, description, date, heure, utilisateur_emmeteur_du_ticket, date_pec, heure_pec, date_fin, heure_fin, urgence, etat, technicien_affecte, technicien_qui_archive from tickets"
        )
        tickets = []

        for i in req.CONTENT:
            ticket_temp = ticket(
                id=i[0],
                serveur=i[1],
                objet=i[2],
                description=i[3],
                date=i[4],
                heure=i[5],
                utilisateur_emmeteur_du_ticket=i[6],
                date_pec=i[7],
                heure_pec=i[8],
                date_fin=i[9],
                heure_fin=i[10],
                urgence=i[11],
                etat=i[12],
                technicien_affecte=i[13],
                technicien_qui_archive=i[14],
            )

            TEMP = {
                "id": ticket_temp.id,
                "serveur": ticket_temp.serveur,
                "objet": ticket_temp.objet,
                "description": ticket_temp.description,
                "date": ticket_temp.date,
                "heure": ticket_temp.heure,
                "utilisateur_emmeteur_du_ticket": ticket_temp.utilisateur_emmeteur_du_ticket,
                "date_pec": ticket_temp.date_pec,
                "heure_pec": ticket_temp.heure_pec,
                "date_fin": ticket_temp.date_fin,
                "heure_fin": ticket_temp.heure_fin,
                "urgence": ticket_temp.urgence,
                "etat": ticket_temp.etat,
                "technicien_affecte": ticket_temp.technicien_affecte,
                "technicien_qui_archive": ticket_temp.technicien_qui_archive,
            }

            tickets.append(TEMP)

        data = {"Error": False, "Tickets": tickets}

    except:
        data = {"Error": True, "DB error": True, "DB Message": req.ERROR_MSG}
    return web.json_response(json.loads(json.dumps(data)))


async def web_get_a_tiket(request):
    data = {"Error": False}
    id = int(request.match_info["id"])
    if (check_if_everything_is_ok() != True) or (id < 0):
        data = {"Error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req_str = (
            "SELECT id, serveur, objet, description, date, heure, utilisateur_emmeteur_du_ticket, date_pec, heure_pec, date_fin, heure_fin, urgence, etat, technicien_affecte, technicien_qui_archive from tickets WHERE id ="
            + str(id)
            + ""
        )
        req = db_run(req_str)

        for i in req.CONTENT:
            print(i)
            ticket_temp = ticket(
                id=i[0],
                serveur=i[1],
                objet=i[2],
                description=i[3],
                date=i[4],
                heure=i[5],
                utilisateur_emmeteur_du_ticket=i[6],
                date_pec=i[7],
                heure_pec=i[8],
                date_fin=i[9],
                heure_fin=i[10],
                urgence=i[11],
                etat=i[12],
                technicien_affecte=i[13],
                technicien_qui_archive=i[14],
            )

            TEMP = {
                "id": ticket_temp.id,
                "serveur": ticket_temp.serveur,
                "objet": ticket_temp.objet,
                "description": ticket_temp.description,
                "date": ticket_temp.date,
                "heure": ticket_temp.heure,
                "utilisateur_emmeteur_du_ticket": ticket_temp.utilisateur_emmeteur_du_ticket,
                "date_pec": ticket_temp.date_pec,
                "heure_pec": ticket_temp.heure_pec,
                "date_fin": ticket_temp.date_fin,
                "heure_fin": ticket_temp.heure_fin,
                "urgence": ticket_temp.urgence,
                "etat": ticket_temp.etat,
                "technicien_affecte": ticket_temp.technicien_affecte,
                "technicien_qui_archive": ticket_temp.technicien_qui_archive,
            }

        data = {"Error": False, "Ticket": TEMP}

    except:
        data = {"Error": True, "DB error": True, "DB Message": req.ERROR_MSG}
    return web.json_response(json.loads(json.dumps(data)))


async def web_create_tiket(request):
    data = {"Error": False}
    return web.json_response(json.loads(json.dumps(data)))


async def web_edit_tiket(request):
    data = {"Error": False}
    return web.json_response(json.loads(json.dumps(data)))


# Définition des routes
app = web.Application()
app.router.add_get("/", index)

# GET
app.router.add_get("/tickets", web_get_all_tikets)
app.router.add_post("/tickets", web_create_tiket)

# CRUD
app.router.add_get("/tickets/{id}", web_get_a_tiket)
app.router.add_post("/tickets/{id}", web_edit_tiket)


web.run_app(app)

exit(0)
