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

# Liste les catégories et le nombre 
def count_ticket_by_cat(projet_id):
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
    
    all_tickets = get_all_tickets(projet_id)
    CATEGORIES = {}
    for i in all_tickets["Tickets"]:
        tmp = i["categorie"]
        if tmp not in CATEGORIES:
            CATEGORIES[tmp] = 0
        CATEGORIES[tmp] += 1
    return {"categories" : CATEGORIES}


def count_ticket_by_statut(projet_id, statut):
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return data
    
    result = 0
    all_tickets = get_all_tickets(projet_id)
    
    for i in all_tickets["Tickets"]:
        if i["statut"] == statut:
            result+=1
    return result



# Récupérer les statistiques des projets
def get_projets_stats():
    data = {"error": False}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))
    try:
        ALL_PROJECTS = get_all_projets()
        if ALL_PROJECTS["error"] != False:
            return data
        
        data = []


        for i in ALL_PROJECTS["Projets"]:
            data.append(get_a_projet_stats(i["id"]))

        return {"number of projects" : len(ALL_PROJECTS["Projets"]),
                "stats" : ALL_PROJECTS}
    except:
        pass
    return data


# Récupérer les statistiques d'un projet
def get_a_projet_stats(projet_id):
    data = {"error": True}
    if check_if_everything_is_ok() != True:
        data = {"error": True}
        return web.json_response(json.loads(json.dumps(data, indent=4)))

    all_tickets = get_all_tickets(projet_id)
    all_cats = count_ticket_by_cat(projet_id)
    all_new = count_ticket_by_statut(projet_id, statut=0)
    all_open = count_ticket_by_statut(projet_id, statut=1) + count_ticket_by_statut(projet_id, statut=2) + count_ticket_by_statut(projet_id, statut=3) + count_ticket_by_statut(projet_id, statut=4) 
    all_closed = count_ticket_by_statut(projet_id, statut=5)
    return {"tickets" : len(all_tickets["Tickets"]),
            "new" : all_new,
            "open" : all_open,
            "closed" : all_closed,
            "categories" : all_cats}
    return data
