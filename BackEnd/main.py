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
# RECAP DB
########################

# =========================================================================
#                               TICKETS
# Statut :
#  0 : Non-Traité
#  1 : En-Cours
#  2 : Bloqué
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
# -1 -> Accès Interdit
#  0 -> Lecture seulement
#  1 -> Prise en charge de tickets et leurs archivage.
#  2 -> Archivage de nimporte quel tickets.
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
# Fonctions de gestion
########################

# Fonction d'affichage de la page d'accueil de l'api
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

###################################################
################   FONCTIONS WEB   ################
###################################################


# WEB : (GET) Affichage de la page d'accueil
async def web_index(request):
    return web.json_response(json.loads(display()))


########################
# PROJETS ADMINISTRATION
########################
# WEB : (GET) Récupération des informations de tous les projets
async def web_get_all_projets(request):
    return web.json_response(json.loads(json.dumps(get_all_projets())))


# WEB : (GET) Récupération des informations d'un projet
async def web_get_a_projet(request):
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_a_projet(id))))


# WEB : (POST) Création d'un nouveau projet
async def web_create_projet(request):
    # GET POST DATA
    post_data = await request.json()
    titre = post_data.get("titre")
    description = post_data.get("description")

    return web.json_response(json.loads(json.dumps(create_projet(titre, description))))


########################
# TICKETS ADMINISTRATION
########################
# WEB : (GET) Récupération de tous les tickets d'un projet
async def web_get_all_tikets(request):
    projet_id = int(request.match_info["projet_id"])
    request_is_valid(request, with_project=True, projet_id=projet_id, level_perms_min=0)
    return web.json_response(json.loads(json.dumps(get_all_tickets(projet_id))))


# WEB : (GET) Récupération d'un ticket
async def web_get_a_tiket(request):
    projet_id = int(request.match_info["projet_id"])
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_a_ticket(id, projet_id))))


# WEB : (GET) Récupération du statut d'un ticket
async def web_get_a_tiket_statut(request):
    projet_id = int(request.match_info["projet_id"])
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_ticket_statut(id, projet_id))))


# WEB : (POST) Création d'un nouveau ticket
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


# WEB : (POST) d'un nouveau statut
async def web_post_ticket_statut(request):
    projet_id = int(request.match_info["projet_id"])
    id = int(request.match_info["id"])
    # GET POST DATA
    post_data = await request.json()
    new_statut = post_data.get("statut")

    data = {"error": True}
    current_statut = get_ticket_statut(id, projet_id)

    if current_statut["error"] != True:
        if new_statut != 0 and new_statut < current_statut["statut"]:
            data = {
                "error": True,
                "msg": "Vous ne pouvez pas décrémenter le statut du ticket."
                + " Statut actuel = "
                + str(current_statut["statut"])
                + ".",
            }
        else:
            data_try = change_ticket_statut(id, projet_id, new_statut)
            if data_try["error"] == False:
                return json.loads(
                    json.dumps(change_ticket_statut(id, projet_id, new_statut))
                )

    return web.json_response(json.loads(json.dumps(data)))


# WEB : (GET) récupération d'un commentaire d'un ticket
async def web_get_tiket_commentaires(request):
    projet_id = int(request.match_info["projet_id"])
    ticket_id = int(request.match_info["id"])
    return web.json_response(
        json.loads(json.dumps(get_all_ticket_commentaire(ticket_id, projet_id)))
    )


# WEB : (GET) Récupération d'un commentaire d'un ticket
async def web_get_a_tiket_commentaires(request):
    projet_id = int(request.match_info["projet_id"])
    ticket_id = int(request.match_info["ticket_id"])
    id = int(request.match_info["id"])
    return web.json_response(
        json.loads(json.dumps(get_a_ticket_commentaire(projet_id, ticket_id, id)))
    )


# WEB : (POST) Création d'un commentaire sur un ticket
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


# WEB : PUT Modification d'un commentaire sur un ticket
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
# WEB : (GET) Liste des utilisateurs
async def web_get_users(request):
    return web.json_response(json.loads(json.dumps(get_all_users())))


# WEB : (GET) Liste d'un utilisateur
async def web_get_a_users(request):
    id = int(request.match_info["id"])
    return web.json_response(json.loads(json.dumps(get_a_users(id))))


# WEB : (POST) Création d'un utilisateur
async def web_post_users(request):
    # GET POST DATA
    post_data = await request.json()
    username = post_data.get("username")
    password = post_data.get("password")
    super_admin = post_data.get("super_admin")
    return web.json_response(
        json.loads(json.dumps(create_user(username, password, super_admin)))
    )


# WEB : (POST) Changement d'un mot de passe d'un utilisateur
async def web_post_user_mdp(request):
    id = int(request.match_info["id"])
    # GET POST DATA
    post_data = await request.json()
    password = post_data.get("password")

    # CHANGEMENT
    change_user_mdp(id, password)
    return web.json_response(json.loads(json.dumps(get_a_users(id))))


########################
# TOKEN ADMINISTRATION
########################


# WEB : (GET) Récupération de la liste des tokens à vie pour tous les utilisateurs
async def web_get_tokens(request):
    statut, error_code, error_msg = request_is_valid(request)
    if statut == False:
        data = {"error": statut, "error_code": error_code, "error_msg": error_msg}
        return web.json_response(json.loads(json.dumps(data)))

    return web.json_response(json.loads(json.dumps(get_token_list(request, 1))))


# WEB : (GET) Récupération de la liste de mes tokens à vie
async def web_get_my_tokens(request):
    statut, error_code, error_msg = request_is_valid(request)
    if statut == False:
        data = {"error": statut, "error_code": error_code, "error_msg": error_msg}
        return web.json_response(json.loads(json.dumps(data)))

    return web.json_response(json.loads(json.dumps(get_token_list(request, 2))))


# WEB : (POST) Création d'un token
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
app.router.add_post("/projets/{projet_id}/tickets/{id}/statut", web_post_ticket_statut)
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
app.router.add_get("/tokens/my", web_get_my_tokens)
app.router.add_post("/tokens", web_post_tokens)

prepare()

web.run_app(app)

exit(0)
