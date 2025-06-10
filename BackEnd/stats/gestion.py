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
from tickets.objets import commentaire, ticket
from projets.gestion import create_projet, get_a_projet, get_all_projets
from tickets.gestion import change_ticket_statut, create_ticket, get_a_ticket, get_a_ticket_commentaire, get_all_ticket_commentaire, get_all_tickets, get_ticket_statut, post_commentaire, put_commentaire
from database.gestion import check_if_everything_is_ok, db_run


########################
# Fonctions de gestion
########################

# Récupérer les statistiques des projets
def get_projets_stats():
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        pass
    except:
        pass
    return data

# Récupérer les statistiques d'un projet
def get_a_projet_stats():
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        pass
    except:
        pass
    return data
