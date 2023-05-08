from dataclasses import dataclass
import datetime
import json
from random import randint
import random
import string
from typing import Optional
from aiohttp import web
import mysql.connector
from datetime import datetime, timedelta
from hashlib import sha512

# CONST
VERSION = "3.0"

DB_HOST = "127.0.0.1"  # Ex : 127.0.0.1
DB_NAME = "exampledb"  # Ex : Fix
DB_USER = "exampleuser"  # Ex : nehemie
DB_PASSORD = "examplepass"  # Ex : B@rki@

HEADERS = {"Content-Type": "application/json"}

# VARS

statut = "Green"  # Green = Tout est fonctionnel  Red = Database innacessible


########################
# RECAP DB
########################

# =========================================================================
#                               TICKETS
# Statut :
#  0 : Non-Traité
#  1 : En-Cours
#  2 : Bloqués
#  3 : En-Tests
#  4 : En-Revue
#  5 : Archivé
#
# Niveaux d'urgence :
# 0 : Faible
# 1 : Normale
# 2 : Urgent
# =========================================================================


# =========================================================================
#                               USERS
# Niveau de permission :
#
# 0 -> Lecture seulement
# 1 -> Prise en charge de tickets et leurs archivage.
# 2 -> Archivage de nimporte quel tickets.
# =========================================================================


# =========================================================================
#                               API_KEYS
# Type :
#
# 0 -> Expire au bout d'une heure
# 1 -> N'expire pas
#
# =========================================================================


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
    statut: int
    technicien_affecte: str
    technicien_qui_archive: str
    projet_id: int


@dataclass
class projet:
    id: int
    titre: str
    description: str
    date: str
    statut: int


@dataclass
class utilisateur:
    id: int
    username: str
    motdepasse: str
    super_admin: bool
    creation: str


@dataclass
class commentaire:
    id: int
    user_id: int
    ticket_id: int
    projet_id: int
    commentaire: str
    statut: int
    date: str
    heure: str
    updated: bool


########################
# Fonctions de gestion
########################


def db_run(CMD, fetch=True, commit=False):
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
        if fetch == True:
            DATA = cursor.fetchall()
        else:
            DATA = cursor.fetchone()

        if commit == True:
            conn.commit()

        cursor.close()
        conn.close()
    except mysql.connector.errors.Programmingerror as error:
        MSG = "Erreur d'authentification MySQL: " + str(error)
        return body_sql(CONTENT=DATA, ERROR=True, ERROR_MSG=MSG)

    except mysql.connector.errors.Databaseerror as error:
        MSG = "Erreur de connexion MySQL " + str(error)
        return body_sql(CONTENT=DATA, ERROR=True, ERROR_MSG=MSG)
    return body_sql(CONTENT=DATA, ERROR=False)


def db_ok():
    OK = False
    try:
        REQ = db_run("SELECT * from tickets LIMIT 0")
        db_statut = "Green"
        db_error = "None"

        if REQ.ERROR != False:
            db_statut = "Red"
            db_error = REQ.ERROR_MSG
    except:
        db_statut = "Red"
        db_error = "Cannot establish connexion with the database"

    return db_statut, db_error


def display():
    db_statut, db_error = db_ok()
    data = {
        "version": VERSION,
        "statut": "running",
        "database": db_statut,
        "database_error": db_error,
        "created_by": "Néhémie Barkia",
        "documentation": "https://github.com/Nem-developing/Fix/wiki",
    }
    return json.dumps(data, indent=4)


def check_if_everything_is_ok():
    db_statut, db_error = db_ok()
    if db_error == True:
        return False
    return True


def get_all_tickets(projet_id):
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req = db_run(
            "SELECT id, serveur, objet, description, date, heure, utilisateur_emmeteur_du_ticket, date_pec, heure_pec, date_fin, heure_fin, urgence, statut, technicien_affecte, technicien_qui_archive,projet_id from tickets WHERE projet_id = "
            + str(projet_id)
            + " ;"
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
                statut=i[12],
                technicien_affecte=i[13],
                technicien_qui_archive=i[14],
                projet_id=i[15],
            )

            TEMP = {
                "id": ticket_temp.id,
                "projet_id": ticket_temp.projet_id,
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
                "statut": ticket_temp.statut,
                "technicien_affecte": ticket_temp.technicien_affecte,
                "technicien_qui_archive": ticket_temp.technicien_qui_archive,
            }

            tickets.append(TEMP)

        data = {"error": False, "total": len(tickets), "Tickets": tickets}
    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


def get_a_ticket(id, projet_id):
    data = {"error": False}
    id = int(id)
    if (check_if_everything_is_ok() != True) or (id < 0):
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req_str = (
            "SELECT id, serveur, objet, description, date, heure, utilisateur_emmeteur_du_ticket, date_pec, heure_pec, date_fin, heure_fin, urgence, statut, technicien_affecte, technicien_qui_archive, projet_id from tickets WHERE id ="
            + str(id)
            + " and projet_id = "
            + str(projet_id)
            + ";"
        )
        req = db_run(req_str)
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
                statut=i[12],
                technicien_affecte=i[13],
                technicien_qui_archive=i[14],
                projet_id=i[15],
            )

            TEMP = {
                "id": ticket_temp.id,
                "projet_id": ticket_temp.projet_id,
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
                "statut": ticket_temp.statut,
                "technicien_affecte": ticket_temp.technicien_affecte,
                "technicien_qui_archive": ticket_temp.technicien_qui_archive,
            }

        data = {"error": False, "Ticket": TEMP}
        return data

    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


def get_ticket_statut(id, projet_id):
    data = {"error": False}
    id = int(id)
    id = int(projet_id)
    if (check_if_everything_is_ok() != True) or (id < 0):
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        data_temp = get_a_ticket(id, projet_id)
        if data_temp["error"] != False:
            data = {"error": True}
            return data
        else:
            return {"error": False, "statut": data_temp["Ticket"]["statut"]}
    except:
        pass
    return data


def change_ticket_statut(id, projet_id, statut):
    try:
        data_temp = get_a_ticket(id, projet_id)
        if data_temp["error"] != False:
            return {"error": True}
        else:
            ## Génération date et heure
            maintenant = datetime.now()
            date = maintenant.strftime("%d/%m/%Y")
            heure = maintenant.strftime("%H:%M:%S")
            var_values = ""
            if statut == 0:
                var_values = ",`date_pec` = 'N/A', `heure_pec` = 'N/A', `date_fin` = 'N/A', `heure_fin`= 'N/A'"
            elif statut == 1:
                var_values = (
                    ", `date_pec` = '" + date + "', `heure_pec` = '" + str(heure) + "'"
                )
            elif statut == 2:
                var_values = (
                    ", `date_fin` = '" + date + "', `heure_fin` = '" + str(heure) + "'"
                )

            req_str = (
                "UPDATE `tickets` SET `statut` = '"
                + str(statut)
                + "'"
                + str(var_values)
                + " WHERE `id` = '"
                + str(id)
                + "' and `projet_id` = '"
                + str(projet_id)
                + "';"
            )
            req = db_run(req_str, fetch=False, commit=True)
            return {
                "error": False,
                "statut": get_ticket_statut(id, projet_id)["statut"],
            }
    except:
        return {"error": False}


def create_ticket(serveur, objet, description, urgence, user_create, projet_id):
    ## Génération date et heure
    maintenant = datetime.now()
    date = maintenant.strftime("%d/%m/%Y")
    heure = maintenant.strftime("%H:%M:%S")
    ## Default Values
    date_pec = "N/A"
    heure_pec = "N/A"
    date_fin = "N/A"
    heure_fin = "N/A"
    statut = 0
    technicien_affecte = "N/A"
    technicien_qui_archive = "N/A"

    try:
        STR = (
            "INSERT INTO `tickets` (`serveur`, `objet`, `description`, `date`, `heure`, `utilisateur_emmeteur_du_ticket`, `date_pec`, `heure_pec`, `date_fin`, `heure_fin`, `urgence`, `statut`, `technicien_affecte`, `technicien_qui_archive`, `projet_id`) VALUES ('"
            + str(serveur)
            + "', '"
            + str(objet)
            + "', '"
            + str(description)
            + "', '"
            + str(date)
            + "', '"
            + str(heure)
            + "', '"
            + str(user_create)
            + "', '"
            + str(date_pec)
            + "', '"
            + str(heure_pec)
            + "', '"
            + str(date_fin)
            + "', '"
            + str(heure_fin)
            + "', '"
            + str(urgence)
            + "', '"
            + str(statut)
            + "', '"
            + str(technicien_affecte)
            + "', '"
            + str(technicien_qui_archive)
            + "', '"
            + str(projet_id)
            + "');"
        )
        req = db_run(STR, commit=True, fetch=False)
        req = db_run("SELECT MAX(id) FROM tickets")
        (ID,) = req.CONTENT[0]
        data = {"Ticket": get_a_ticket(ID, projet_id), "created": True, "error": False}
    except:
        data = {
            "error": True,
            "DB_error": True,
            "error_message": "Body is missing",
            "created": False,
        }
        return data
    return data


def ticket_statut(id, projet_id, new_status):
    data = {"error": False}
    current_statut = get_ticket_statut(id, projet_id)

    if current_statut["error"] != True:
        if new_status != 0 and new_status < current_statut["statut"]:
            data = {
                "error": True,
                "msg": "Vous ne pouvez pas décrémenter le statut du ticket."
                + " Statut actuel = "
                + str(current_statut["statut"])
                + ".",
            }
        else:
            data_try = change_ticket_statut(id, projet_id, new_status)
            if data_try["error"] == False:
                return get_a_ticket(id, projet_id)

    return data


def get_all_ticket_commentaire(id, projet_id):
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req_str = (
            "SELECT id, user_id, ticket_id, projet_id, commentaire, statut, date, heure, updated FROM tickets_commentaires WHERE projet_id = "
            + str(projet_id)
            + " and ticket_id = "
            + str(id)
            + " ;"
        )
        req = db_run(req_str)

        commentaires = []
        if req.CONTENT != None:
            for i in req.CONTENT:
                commentaire_temp = commentaire(
                    id=i[0],
                    user_id=i[1],
                    ticket_id=i[2],
                    projet_id=i[3],
                    commentaire=i[4],
                    statut=i[5],
                    date=i[6],
                    heure=i[7],
                    updated=i[8],
                )

                TEMP = {
                    "id": commentaire_temp.id,
                    "user_id": commentaire_temp.user_id,
                    "ticket_id": commentaire_temp.ticket_id,
                    "projet_id": commentaire_temp.projet_id,
                    "commentaire": commentaire_temp.commentaire,
                    "statut": commentaire_temp.statut,
                    "date": commentaire_temp.date,
                    "heure": commentaire_temp.heure,
                    "updated": commentaire_temp.updated,
                }
                commentaires.append(TEMP)

        data = {
            "error": False,
            "total": len(commentaires),
            "Commentaires": commentaires,
        }
    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


def get_a_ticket_commentaire(projet_id, ticket_id, id):
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req_str = (
            "SELECT id, user_id, ticket_id, projet_id, commentaire, statut, date, heure, updated FROM tickets_commentaires WHERE projet_id = "
            + str(projet_id)
            + " and ticket_id = "
            + str(ticket_id)
            + " and id = "
            + str(id)
            + ";"
        )
        req = db_run(req_str, fetch=True)
        TMP = []
        if req.CONTENT != None:
            for i in req.CONTENT:
                commentaire_temp = commentaire(
                    id=i[0],
                    user_id=i[1],
                    ticket_id=i[2],
                    projet_id=i[3],
                    commentaire=i[4],
                    statut=i[5],
                    date=i[6],
                    heure=i[7],
                    updated=i[8],
                )

                TEMP = {
                    "id": commentaire_temp.id,
                    "user_id": commentaire_temp.user_id,
                    "ticket_id": commentaire_temp.ticket_id,
                    "projet_id": commentaire_temp.projet_id,
                    "commentaire": commentaire_temp.commentaire,
                    "statut": commentaire_temp.statut,
                    "date": commentaire_temp.date,
                    "heure": commentaire_temp.heure,
                    "updated": commentaire_temp.updated,
                }
                TMP.append(TEMP)
        if len(TMP) == 0 or len(TMP) == 1:
            data = {"error": False, "Commentaire": TEMP}
        else:
            data = {"error": True}
    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


# PROJETS :
## Récupérer un projet
def get_a_projet(id):
    data = {"error": False}
    id = int(id)
    if (check_if_everything_is_ok() != True) or (id < 0):
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req_str = (
            "SELECT id, titre, description, date, statut from projets WHERE id ="
            + str(id)
            + ";"
        )
        req = db_run(req_str)
        for i in req.CONTENT:
            projet_temp = projet(
                id=i[0], titre=i[1], description=i[2], date=i[3], statut=i[4]
            )

            TEMP = {
                "id": projet_temp.id,
                "titre": projet_temp.titre,
                "description": projet_temp.description,
                "date": projet_temp.date,
                "statut": projet_temp.statut,
            }

        data = {"error": False, "Projet": TEMP}
        return data

    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


## Récupération de tous les projets
def get_all_projets():
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req = db_run("SELECT id, titre, description, date, statut from projets;")
        projets = []

        for i in req.CONTENT:
            projet_temp = projet(
                id=i[0], titre=i[1], description=i[2], date=i[3], statut=i[4]
            )

            TEMP = {
                "id": projet_temp.id,
                "titre": projet_temp.titre,
                "description": projet_temp.description,
                "date": projet_temp.date,
                "statut": projet_temp.statut,
            }

            projets.append(TEMP)

        data = {"error": False, "total": len(projets), "Projets": projets}
    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


## Création d'un projet
def create_projet(titre, description):
    ## Génération de la date
    maintenant = datetime.now()
    date = maintenant.strftime("%d/%m/%Y")
    # Defaut
    statut = 0

    try:
        STR = (
            "INSERT INTO `projets` (`titre`, `description`, `date`, `statut`) VALUES ('"
            + str(titre)
            + "', '"
            + str(description)
            + "', '"
            + str(date)
            + "', '"
            + str(statut)
            + "');"
        )
        req = db_run(STR, commit=True, fetch=False)
        req = db_run("SELECT MAX(id) FROM projets")
        (ID,) = req.CONTENT[0]
        data = {"Ticket": get_a_projet(ID), "created": True, "error": False}
    except:
        data = {
            "error": True,
            "DB_error": True,
            "error_message": "Body is missing",
            "created": False,
        }
        return data
    return data


def post_commentaire(user_id, ticket_id, projet_id, message):
    ## Génération de la date
    maintenant = datetime.now()
    date = maintenant.strftime("%d/%m/%Y")
    heure = maintenant.strftime("%H:%M:%S")
    # Defaut
    statut = 0

    try:
        db_str = (
            "INSERT INTO `tickets_commentaires` (`user_id`, `ticket_id`, `projet_id`, `commentaire`, `statut`, `date`, `heure`) VALUES ('"
            + str(user_id)
            + "', '"
            + str(ticket_id)
            + "', '"
            + str(projet_id)
            + "', '"
            + str(message)
            + "', 0,'"
            + str(date)
            + "', '"
            + str(heure)
            + "');"
        )
        db_run(db_str, commit=True, fetch=False)

        req = db_run("SELECT MAX(id) FROM tickets_commentaires")

        (ID,) = req.CONTENT[0]

        data = {
            "Commentaire": get_a_ticket_commentaire(projet_id, ticket_id, ID),
            "created": True,
            "error": False,
        }
    except:
        data = {
            "error": True,
            "DB_error": True,
        }
        return data
    return data


def put_commentaire(ticket_id, projet_id, id, message, statut):
    try:
        db_str = f"UPDATE `tickets_commentaires` SET `commentaire` = '{message}', `statut` = '{statut}', `updated` = '1' WHERE `id` = '{id}' AND `projet_id` = '{projet_id}';"
        db_run(db_str, commit=True, fetch=False)
        data = {
            "Commentaire": get_a_ticket_commentaire(projet_id, ticket_id, id),
            "created": True,
            "error": False,
        }
    except:
        data = {
            "error": True,
            "DB_error": True,
        }
        return data
    return data


# USERS


def get_all_users():
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req = db_run("SELECT id, username, super_admin, creation from utilisateurs;")
        utilisateurs = []

        for i in req.CONTENT:
            utilisateur_temp = utilisateur(
                id=i[0],
                username=i[1],
                motdepasse="N/A",
                super_admin=i[2],
                creation=i[3],
            )

            TEMP = {
                "id": utilisateur_temp.id,
                "username": utilisateur_temp.username,
                "super_admin": utilisateur_temp.super_admin,
                "creation": utilisateur_temp.creation,
            }

            utilisateurs.append(TEMP)

        data = {
            "error": False,
            "total": len(utilisateurs),
            "Utilisateurs": utilisateurs,
        }
    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


def get_a_users(id):
    data = {"error": False}
    id = int(id)
    if (check_if_everything_is_ok() != True) or (id < 0):
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        req_str = (
            "SELECT id, username, super_admin, creation from utilisateurs WHERE id ="
            + str(id)
            + ";"
        )
        req = db_run(req_str)
        for i in req.CONTENT:
            utilisateur_temp = utilisateur(
                id=i[0],
                username=i[1],
                motdepasse="N/A",
                super_admin=i[2],
                creation=i[3],
            )

            TEMP = {
                "id": utilisateur_temp.id,
                "username": utilisateur_temp.username,
                "super_admin": utilisateur_temp.super_admin,
                "creation": utilisateur_temp.creation,
            }

        data = {"error": False, "Utilisateur": TEMP}
        return data

    except:
        data = {"error": True, "DB_error": True, "error_message": req.ERROR_MSG}
    return data


## Création d'un utilisateur
def create_user(username, mot_de_passe, is_super_admin):
    ## Génération de la date
    maintenant = datetime.now()
    date = maintenant.strftime("%d/%m/%Y")
    # Defaut
    if verif_user_exist(username) == True:
        data = {
            "error_message": "L'utilisateur existe déjà !",
            "created": False,
            "error": True,
        }
        return data
    try:
        STR = (
            "INSERT INTO `utilisateurs` (`username`, `motdepasse`, `creation`, `super_admin`) VALUES ('"
            + str(username)
            + "', '"
            + str(chiffrer_password(mot_de_passe))
            + "', '"
            + str(date)
            + "', '"
            + str(is_super_admin)
            + "');"
        )
        req = db_run(STR, commit=True, fetch=False)
        req = db_run("SELECT MAX(id) FROM utilisateurs")
        (ID,) = req.CONTENT[0]
        data = {"Utilisateur": get_a_users(ID), "created": True, "error": False}
    except:
        data = {
            "error": True,
            "DB_error": True,
            "created": False,
        }
        return data
    return data


# Change password
def change_user_mdp(user_id, motdepasse):
    CMD = (
        "UPDATE utilisateurs set motdepasse = '"
        + str(chiffrer_password(motdepasse))
        + "' where id = '"
        + str(user_id)
        + "';"
    )
    REQ = db_run(CMD, fetch=False, commit=True)
    return


# Fonction qui vérifie si une table existe ou non
def verif_table(table):
    cnx = mysql.connector.connect(
        user=DB_USER,
        password=DB_PASSORD,
        host=DB_HOST,
        database=DB_NAME,
    )
    cursor = cnx.cursor()

    cursor.execute("SHOW TABLES LIKE '{}'".format(table))
    result = cursor.fetchone()

    if result:
        return True
    else:
        return False


# Retourne Vrais si on arrive à nous connecter à la DB
def acces_db():
    try:
        cnx = mysql.connector.connect(
            user=DB_USER,
            password=DB_PASSORD,
            host=DB_HOST,
            database=DB_NAME,
        )
        cursor = cnx.cursor()
        return True
    except:
        return False


def check_and_create_db_if_required(db_name, req_to_create_db):
    if verif_table(str(db_name)) == False:
        print("--> [" + str(db_name) + "] La table en cours de création ...", end="")
        db_run(req_to_create_db, fetch=False, commit=True)
        print(" OK !")

        if str(db_name) == "projets":
            maintenant = datetime.now()
            date = maintenant.strftime("%d/%m/%Y")
            print("\t=> Ajout du projet initial.", end="")
            req_str = (
                "INSERT INTO `projets` (`titre`, `description`, `date`, `statut`) VALUES ('Projet Initial', 'Voici un projet', '"
                + str(date)
                + "', '0');"
            )
            db_run(req_str, fetch=False, commit=True)
            print(" OK !")

        # Exception pour la table ticket, en plus de la créer, on va y insérer un ticket de bienvenue.
        elif str(db_name) == "tickets":
            maintenant = datetime.now()
            date = maintenant.strftime("%d/%m/%Y")
            heure = maintenant.strftime("%H:%M:%S")
            print("\t=> Ajout du ticket de bienvenue.", end="")
            req_str = (
                "INSERT INTO `tickets` (`serveur`, `objet`, `description`, `date`, `heure`, `utilisateur_emmeteur_du_ticket`, `date_pec`, `heure_pec`,  `date_fin`, `heure_fin`,  `urgence`, `statut`, `technicien_affecte`, `technicien_qui_archive`, `projet_id`) VALUES ('nehemiebarkia.fr', 'Bienvenue sur Fix "
                + str(VERSION)
                + " !', 'Crée un ticket pour commencer ! Tu peux également afficher les détails de ce ticket en cliquant sur le bouton tout à droite !', '"
                + str(date)
                + "', '"
                + str(heure)
                + "', 'Néhémie Barkia',  'N/A','N/A','N/A','N/A', 0, 0,  'N/A', 'N/A', 1);"
            )
            db_run(req_str, fetch=False, commit=True)
            print(" OK !")
        elif str(db_name) == "utilisateurs":
            print("\t=> Ajout de l'utilisateur admin.", end="")
            maintenant = datetime.now()
            date = maintenant.strftime("%d/%m/%Y")
            heure = maintenant.strftime("%H:%M:%S")

            req_str = (
                "INSERT INTO `utilisateurs` (`username`, `motdepasse`, `creation`, `super_admin`) VALUES ('admin', '"
                + str(chiffrer_password("admin"))
                + "','"
                + str(date)
                + "', TRUE);"
            )
            db_run(req_str, fetch=False, commit=True)
            print(" OK !")

        elif str(db_name) == "utilisateurs_permissions":
            print("\t=> Ajout des permissions de l'utilisateur admin.", end="")
            req_str = "INSERT INTO `utilisateurs_permissions` (`utilisateur_id`, `projet_id`, `permissions`) VALUES ('1', '1', '2');"
            db_run(req_str, fetch=False, commit=True)
            print(" OK !")

    else:
        print("--> [" + str(db_name) + "] Table présente !")
    return


# Fonction qui va créer toutes les tables en cas de besoin
def prepare():
    print("Initialisation de l'API de FIX " + str(VERSION))

    # Accès à la DB
    try:
        if acces_db() == True:
            print("--> [OK] : Connexion à la base de données réussie !\n")
        else:
            print("--> [KO] : Connexion à la base de données échouée !\n")
    except:
        print("--> [KO] : Connexion à la base de données échouée !\n")
        exit(1)

    # Tables présentes
    print("Vérifcation des tables :")
    try:
        # Les requettes de créations de tables
        req_create_projets = "CREATE TABLE `projets` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `titre` varchar(50), `description` varchar(1024), `date` varchar(10) NOT NULL, `statut` int NOT NULL);"
        req_create_tickets = "CREATE TABLE `tickets` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `projet_id` INT NOT NULL , `serveur` varchar(50) NOT NULL, `objet` varchar(50) NOT NULL, `description` longtext NOT NULL, `date` varchar(10) NOT NULL, `heure` varchar(10) NOT NULL, `utilisateur_emmeteur_du_ticket` varchar(25) NOT NULL, `date_pec` varchar(10) NOT NULL, `heure_pec` varchar(10) NOT NULL, `date_fin` varchar(10) NOT NULL, `heure_fin` varchar(10) NOT NULL, `urgence` INT NOT NULL, `statut` INT NOT NULL, `technicien_affecte` varchar(25) NOT NULL, `technicien_qui_archive` varchar(25) NOT NULL, FOREIGN KEY (projet_id) REFERENCES projets(id) );"
        req_create_utilisateurs = "CREATE TABLE `utilisateurs` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `username` varchar(16) NOT NULL, `motdepasse` varchar(512) NOT NULL, `super_admin` INT NOT NULL, `creation` varchar(10) NOT NULL );"
        req_create_utilisateurs_permissions = "CREATE TABLE `utilisateurs_permissions` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `utilisateur_id` INT NOT NULL, `projet_id` INT NOT NULL , `permissions` INT NOT NULL, FOREIGN KEY (projet_id) REFERENCES projets(id),  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id));"
        req_create_logs = "CREATE TABLE `logs` ( `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL, `utilisateur` VARCHAR(16) NOT NULL, `action` INT NOT NULL, `date` VARCHAR(10) NOT NULL, `heure` VARCHAR(8) NOT NULL, `cible` VARCHAR(256) NOT NULL );"
        req_create_commentaires = "CREATE TABLE tickets_commentaires ( id INT AUTO_INCREMENT, user_id INT, ticket_id INT, projet_id INT NOT NULL , commentaire LONGTEXT NOT NULL, `statut` INT NOT NULL, `date` varchar(10) NOT NULL, `heure` varchar(10) NOT NULL, `updated` BOOLEAN DEFAULT FALSE, PRIMARY KEY (id), FOREIGN KEY (user_id) REFERENCES utilisateurs(id), FOREIGN KEY (ticket_id) REFERENCES tickets(id),  FOREIGN KEY (projet_id) REFERENCES projets(id) );"
        req_create_api_keys = "CREATE TABLE `api_keys` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `user_id` INT NOT NULL, `token` varchar(62) NOT NULL, `date` varchar(10) NOT NULL, `heure` varchar(10) NOT NULL, `type` INT NOT NULL, FOREIGN KEY (`user_id`) REFERENCES `utilisateurs`(`id`) );"

        # Création si besoin :
        check_and_create_db_if_required("projets", req_create_projets)
        check_and_create_db_if_required("tickets", req_create_tickets)
        check_and_create_db_if_required("utilisateurs", req_create_utilisateurs)
        check_and_create_db_if_required(
            "utilisateurs_permissions", req_create_utilisateurs_permissions
        )
        check_and_create_db_if_required("logs", req_create_logs)
        check_and_create_db_if_required("tickets_commentaires", req_create_commentaires)
        check_and_create_db_if_required("api_keys", req_create_api_keys)
    except:
        print(
            "--> [KO] : Une erreur est survenue ! Nous avons pas pû correctement vérifier la base de donnée.\n"
        )
    return


def verif_user_exist(user):
    CMD = "SELECT * FROM utilisateurs where username = '" + str(user) + "';"
    cpt = 0
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        cpt += 1

    if cpt != 0:
        return True
    return False


def get_encrypted_user_password(user):
    CMD = "SELECT * FROM utilisateurs where username = '" + str(user) + "';"
    cpt = 0
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        password = i[2]
    return password


def get_user_id(user):
    CMD = "SELECT * FROM utilisateurs where username = '" + str(user) + "';"
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        id = i[0]
    return id


def chiffrer_password(password):
    mdp = password.encode()
    mdp_sign = sha512(mdp).hexdigest()
    return mdp_sign


def verif_user_password(username, password):
    if get_encrypted_user_password(username) == chiffrer_password(password):
        return True
    else:
        return False


def generate_token_value():
    letters = string.ascii_uppercase
    numbers = string.digits
    possible_choice = letters + numbers
    token = ""

    token += "".join(random.choice(possible_choice) for i in range(20))
    token += "-"
    token += "".join(random.choice(possible_choice) for i in range(20))
    token += "-"
    token += "".join(random.choice(possible_choice) for i in range(20))
    return token


def verif_token_unique(token):
    cmd = "SELECT count(*) FROM api_keys where token = '" + str(token) + "';"
    req = db_run(cmd, fetch=False, commit=False)
    (count,) = req.CONTENT
    if count == 0:
        return True
    else:
        return False


def token_create(username, type):
    token = generate_token_value()
    while verif_token_unique(token) == False:
        token = generate_token_value()

    maintenant = datetime.now()
    date = maintenant.strftime("%d/%m/%Y")
    heure = maintenant.strftime("%H:%M:%S")
    user_id = get_user_id(username)
    STR = (
        "INSERT INTO `api_keys` (`user_id`, `token`, `date`, `heure`, `type`)"
        + " VALUES ('"
        + str(user_id)
        + "', '"
        + str(token)
        + "', '"
        + str(date)
        + "', '"
        + str(heure)
        + "', '"
        + str(type)
        + "');"
    )

    req = db_run(STR, commit=True, fetch=False)
    req = db_run("SELECT id FROM api_keys where token = '" + str(token) + "';")
    (ID,) = req.CONTENT[0]
    data = {"id": ID, "token": str(token), "type": type, "date": date, "heure": heure}
    return data


def verif_token_valid(token):
    cmd = "SELECT type,date,heure FROM api_keys where token = '" + str(token) + "';"
    req = db_run(cmd, fetch=False, commit=False)

    if req.CONTENT is None:
        return False, 1, "Ce token n'existe pas."
    else:
        type, date, heure = req.CONTENT

        if type == 0:
            now = datetime.now()
            token_timestamp = datetime.strptime(date + " " + heure, "%d/%m/%Y %H:%M:%S")
            if now > token_timestamp + timedelta(hours=1):
                # Token plus valide
                return False, 2, "Token expiré"
            else:
                # Token toujours valide
                return True, 0, ""
        elif type == 1:
            # Illimité
            return True, 0, ""
        else:
            # Autre type ?
            return False, 3, "Le type de token spécifié n'est pas pris en compte."


def request_is_valid(request):
    statut = False
    error_code = 0
    error_msg = "None"

    try:
        bearer = request.headers.get("Authorization")
        token = bearer.split(" ")[1]
    except:
        statut = False
        error_code = 1
        error_msg = "Vous n'avez pas spécifié de bearer en Header"
        return statut, error_code, error_msg

    valid, code, msg = verif_token_valid(token)
    statut = valid
    error_code = code
    error_msg = msg

    return statut, error_code, error_msg


def get_token(request):
    bearer = request.headers.get("Authorization")
    token = bearer.split(" ")[1]
    return token


def get_user_permissions(user_id):
    CMD = "SELECT permissions FROM utilisateurs where id = '" + str(user_id) + "';"
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        id = i[0]
    return id


def get_user_id_from_token(token):
    CMD = "SELECT user_id FROM api_keys where token = '" + str(token) + "';"
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        id = i[0]
    return id


def get_tocken_dict(user_perms_level, user_id):
    data = {}

    if (user_perms_level == 0) or (user_perms_level == 1):
        TOKENS = []
        CMD = (
            "select api_keys.id, api_keys.user_id, utilisateurs.username, api_keys.token, api_keys.date, api_keys.heure, api_keys.type "
            + "from api_keys "
            + "inner join utilisateurs on api_keys.user_id = utilisateurs.id "
            + "werre utilisateurs.id = "
            + str(user_id)
            + ";"
        )
        REQ = db_run(CMD, fetch=True, commit=False)
        for i in REQ.CONTENT:
            TMP = {
                "id": i[0],
                "user_id": i[1],
                "username": i[2],
                "token": i[3],
                "date": i[4],
                "heure": i[5],
                "type": i[6],
            }
            TOKENS.append(TMP)

        data = {
            "error": False,
            "total": len(TOKENS),
            "Tokens": TOKENS,
        }

        return data

    # Un utilisateur admin peut accéder à tous les tokens.
    elif user_perms_level == 2:
        TOKENS = []
        CMD = "select api_keys.id, api_keys.user_id, utilisateurs.username, api_keys.token, api_keys.date, api_keys.heure, api_keys.type from api_keys inner join utilisateurs on api_keys.user_id = utilisateurs.id;"
        REQ = db_run(CMD, fetch=True, commit=False)
        for i in REQ.CONTENT:
            TMP = {
                "id": i[0],
                "user_id": i[1],
                "username": i[2],
                "token": i[3],
                "date": i[4],
                "heure": i[5],
                "type": i[6],
            }
            TOKENS.append(TMP)

        data = {
            "error": False,
            "total": len(TOKENS),
            "Tokens": TOKENS,
        }

        return data


###################################################
################   FONCTIONS WEB   ################
###################################################


########################
# TOKEN ADMINISTRATION
########################
async def web_index(request):
    return web.json_response(json.loads(display()))


########################
# PROJETS ADMINISTRATION
########################
async def web_get_all_projets(request):
    return web.json_response(json.loads(json.dumps(get_all_projets())))


async def web_get_a_projet(request):
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_a_projet(id))))


async def web_create_projet(request):
    # GET POST DATA
    post_data = await request.json()
    titre = post_data.get("titre")
    description = post_data.get("description")

    return web.json_response(json.loads(json.dumps(create_projet(titre, description))))


########################
# TICKETS ADMINISTRATION
########################
async def web_get_all_tikets(request):
    projet_id = int(request.match_info["projet_id"])
    return web.json_response(json.loads(json.dumps(get_all_tickets(projet_id))))


async def web_get_a_tiket(request):
    projet_id = int(request.match_info["projet_id"])
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_a_ticket(id, projet_id))))


async def web_get_a_tiket_statut(request):
    projet_id = int(request.match_info["projet_id"])
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_ticket_statut(id, projet_id))))


async def web_create_tiket(request):
    projet_id = int(request.match_info["projet_id"])
    # GET POST DATA
    post_data = await request.json()
    serveur = post_data.get("serveur")
    data_objet = post_data.get("objet")
    description = post_data.get("description")
    urgence = post_data.get("urgence")
    user_create = post_data.get("user_create")
    return web.json_response(
        json.loads(
            json.dumps(
                create_ticket(
                    serveur, data_objet, description, urgence, user_create, projet_id
                )
            )
        )
    )


async def web_ticket_statut(request):
    projet_id = int(request.match_info["projet_id"])
    id = int(request.match_info["id"])
    # GET POST DATA
    post_data = await request.json()
    new_statut = post_data.get("statut")
    return web.json_response(
        json.loads(json.dumps(ticket_statut(id, projet_id, new_statut)))
    )


async def web_get_tiket_commentaires(request):
    projet_id = int(request.match_info["projet_id"])
    ticket_id = int(request.match_info["id"])
    return web.json_response(
        json.loads(json.dumps(get_all_ticket_commentaire(ticket_id, projet_id)))
    )


async def web_get_a_tiket_commentaires(request):
    projet_id = int(request.match_info["projet_id"])
    ticket_id = int(request.match_info["ticket_id"])
    id = int(request.match_info["id"])
    return web.json_response(
        json.loads(json.dumps(get_a_ticket_commentaire(projet_id, ticket_id, id)))
    )


async def web_post_tiket_commentaires(request):
    projet_id = int(request.match_info["projet_id"])
    ticket_id = int(request.match_info["id"])
    # GET POST DATA
    post_data = await request.json()
    message = post_data.get("message")
    user_id = get_user_id_from_token(get_token(request))
    return web.json_response(
        json.loads(json.dumps(post_commentaire(user_id, ticket_id, projet_id, message)))
    )


async def web_put_tiket_commentaire(request):
    projet_id = int(request.match_info["projet_id"])
    ticket_id = int(request.match_info["ticket_id"])
    id = int(request.match_info["id"])
    # GET POST DATA
    post_data = await request.json()
    message = post_data.get("message")
    statut = post_data.get("statut")
    return web.json_response(
        json.loads(
            json.dumps(put_commentaire(ticket_id, projet_id, id, message, statut))
        )
    )


########################
# USERS ADMINISTRATION
########################
async def web_get_users(request):
    return web.json_response(json.loads(json.dumps(get_all_users())))


async def web_get_a_users(request):
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_a_users(id))))


async def web_post_users(request):
    # GET POST DATA
    post_data = await request.json()
    username = post_data.get("username")
    motdepasse = post_data.get("motdepasse")
    super_admin = post_data.get("super_admin")
    return web.json_response(
        json.loads(json.dumps(create_user(username, motdepasse, super_admin)))
    )


async def web_post_user_mdp(request):
    id = int(request.match_info["id"])
    # GET POST DATA
    post_data = await request.json()
    motdepasse = post_data.get("motdepasse")

    # CHANGEMENT
    change_user_mdp(id, motdepasse)
    return web.json_response(json.loads(json.dumps(get_a_users(id))))


########################
# TOKEN ADMINISTRATION
########################


async def web_get_tokens(request):
    statut, error_code, error_msg = request_is_valid(request)
    if statut == False:
        data = {"error": statut, "error_code": error_code, "error_msg": error_msg}
        return web.json_response(json.loads(json.dumps(data)))

    return web.json_response(
        json.loads(
            json.dumps(
                get_tocken_dict(
                    get_user_permissions(get_user_id_from_token(get_token(request))),
                    get_user_id_from_token(get_token(request)),
                )
            )
        )
    )


# Get a token
async def web_post_tokens(request):
    # VARS
    EX = {"username": "admin", "password": "admin", "type": 1}
    ERROR_1 = "L'utilisateur n'existe pas."
    ERROR_2 = "Mot de passe incorrect."
    ERROR_3 = "Merci de renseigner les informations suivantes : " + str(EX)
    ERROR_4 = "Données incorectes"

    data = {}
    # GET POST DATA
    post_data = await request.json()
    username = post_data.get("username")
    password = post_data.get("password")
    type = post_data.get("type")

    if (username is None) or (password is None) or (type is None):
        return web.json_response(
            json.loads(json.dumps({"error": True, "msg": ERROR_3, "error_code": 3}))
        )

    if verif_user_exist(username) == False:
        return web.json_response(
            json.loads(json.dumps({"error": True, "msg": ERROR_1, "error_code": 1}))
        )

    if verif_user_password(username, password) == False:
        return web.json_response(
            json.loads(json.dumps({"error": True, "msg": ERROR_2, "error_code": 2}))
        )

    if (type != 0) and (type != 1):
        return web.json_response(
            json.loads(json.dumps({"error": True, "msg": ERROR_4, "error_code": 4}))
        )

    # Création du token
    token = token_create(username, type)

    return web.json_response(json.loads(json.dumps({"error": False, "token": token})))


# Définition des routes
app = web.Application()
app.router.add_get("/", web_index)

# GESTION DES PROJETS
app.router.add_get("/projets", web_get_all_projets)
app.router.add_get("/projets/{id}", web_get_a_projet)
app.router.add_post("/projets", web_create_projet)

# GET ALL & CREATE ONE
app.router.add_get("/projets/{projet_id}/tickets", web_get_all_tikets)
app.router.add_post("/projets/{projet_id}/tickets", web_create_tiket)

# TICKET
app.router.add_get("/projets/{projet_id}/tickets/{id}", web_get_a_tiket)
app.router.add_get("/projets/{projet_id}/tickets/{id}/statut", web_get_a_tiket_statut)
app.router.add_post("/projets/{projet_id}/tickets/{id}/statut", web_ticket_statut)
# TICKET COMMENTAIRES
app.router.add_get(
    "/projets/{projet_id}/tickets/{id}/commentaires", web_get_tiket_commentaires
)
app.router.add_get(
    "/projets/{projet_id}/tickets/{ticket_id}/commentaires/{id}",
    web_get_a_tiket_commentaires,
)
app.router.add_post(
    "/projets/{projet_id}/tickets/{id}/commentaires", web_post_tiket_commentaires
)
app.router.add_put(
    "/projets/{projet_id}/tickets/{ticket_id}/commentaires/{id}",
    web_put_tiket_commentaire,
)
# USERS
app.router.add_get("/utilisateurs", web_get_users)
app.router.add_get("/utilisateurs/{id}", web_get_a_users)
app.router.add_post("/utilisateurs", web_post_users)
app.router.add_post("/utilisateurs/{id}/password", web_post_user_mdp)

# API TOKEN
app.router.add_get("/tokens", web_get_tokens)
app.router.add_post("/tokens", web_post_tokens)

prepare()

web.run_app(app)

exit(0)
