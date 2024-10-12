# Imports 
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

# Imports locaux
from database.gestion import *
from database.objets import *
from projets.gestion import *
from projets.objets import *
from tickets.gestion import *
from tickets.objets import *
from utilisateurs.gestion import *
from utilisateurs.objets import *
from variables.constants import *
from variables.db_config import *
from variables.default import *

########################
# Fonctions de gestion
########################


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