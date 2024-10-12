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
from variables.constants import *
from variables.db_config import *
from variables.default import *

## Récupération de tous les utilisateurs
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
                password="N/A",
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


## Récupération d'un utilisateur via son ID
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
                password="N/A",
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
            "INSERT INTO `utilisateurs` (`username`, `password`, `creation`, `super_admin`) VALUES ('"
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
        print(req)
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


# Modification d'un mot de passe utilisateur
def change_user_mdp(user_id, password):
    CMD = (
        "UPDATE utilisateurs set password = '"
        + str(chiffrer_password(password))
        + "' where id = '"
        + str(user_id)
        + "';"
    )
    REQ = db_run(CMD, fetch=False, commit=True)
    return



# Vérification qu'un utilisateur existe ou non.
def verif_user_exist(user):
    CMD = "SELECT * FROM utilisateurs where username = '" + str(user) + "';"
    cpt = 0
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        cpt += 1

    if cpt != 0:
        return True
    return False


# Récupération du mot de passe chiffré d'un utilisateur grâce à un username
def get_encrypted_user_password(user):
    CMD = "SELECT * FROM utilisateurs where username = '" + str(user) + "';"
    cpt = 0
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        password = i[2]
    return password


# Récupération d'un user_id grâce à un username
def get_user_id(user):
    CMD = "SELECT * FROM utilisateurs where username = '" + str(user) + "';"
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        id = i[0]
    return id


# Chiffrement d'un mot de passe
def chiffrer_password(password):
    mdp = password.encode()
    mdp_sign = sha512(mdp).hexdigest()
    return mdp_sign


# Vérification de la validité d'un mot de passe
def verif_user_password(username, password):
    if get_encrypted_user_password(username) == chiffrer_password(password):
        return True
    else:
        return False


# Génération d'un chaine de caractères en gise de token
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


# Vérification si un token n'est pas présent dans la base
def verif_token_unique(token):
    cmd = "SELECT count(*) FROM api_keys where token = '" + str(token) + "';"
    req = db_run(cmd, fetch=False, commit=False)
    (count,) = req.CONTENT
    if count == 0:
        return True
    else:
        return False


# Création d'un token grâce à un username
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


# Vérification de la validité d'une requêtte
def request_is_valid(request, with_project, level_perms_min, projet_id):
    # with_project = (True, False) --> Indique s'il ont aura besoin de récupérer ou non les permissions spécifiques de l'utilisateur sur un projet donné
    # level_perms_min = (0,1,2) -> Indique le niveau de permission minimum dont un utilisateur a besoin pour requêtter une route (fonctionne obligatoirement avec with_project à True ).
    # --> Dans le cas où "with_project est à False", avec un utilisateur non super-admin, on répondra TRUE car l'utilisateur est bien authentifié.
    try:
        token = get_token(request)
    except:
        statut = False
        error_code = 1
        error_msg = "Vous n'avez pas spécifié de bearer en Header"
        return statut, error_code, error_msg

    # Récupération de l'état du token
    statut, error_code, error_msg = verif_token_valid(token)
    if statut != True:
        return statut, error_code, error_msg
    else:
        # Le Token est valide, nous vérifions maintenant les permissions de l'utilisateur
        user_id = get_user_id_from_token(token)

        if user_is_super_admin(user_id) == True:
            # L'utilsateur étant super_admin celui-ci a tous les droits !
            statut = True
            error_code = 0
            error_msg = "N/A"
            return statut, error_code, error_msg
        else:
            if with_project == True:
                if get_user_permission(user_id, projet_id) >= level_perms_min:
                    # L'utilsateur a le niveau de perms suffisant.
                    statut = True
                    error_code = 0
                    error_msg = "N/A"
                    return statut, error_code, error_msg
                else:
                    # L'utilisateur n'a pas le niveau de perms suffisant.
                    statut = False
                    error_code = 3
                    error_msg = "Permissions manquantes !"
                    return statut, error_code, error_msg
            elif with_project == False:
                # L'utilisateur est bien authentifié mais nous ne procédons pas à une vérification supplémentaire car celle-ci n'est pas demmandée.
                statut = True
                error_code = 0
                error_msg = "N/A"
                return statut, error_code, error_msg
    return verif_token_valid(token)




# Récupération de l'attribut super_admin grâce à un user_id
def super_admin(user_id):
    CMD = "SELECT super_admin FROM utilisateurs where id = '" + str(user_id) + "';"
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        super_admin = i[0]
    return super_admin


# Simplification de la vérification du super_admin grâce à un user_id.
def user_is_super_admin(user_id):
    super_admin = super_admin(user_id)
    if super_admin == 1:
        return True
    else:
        return False


# Récupération d'un id d'utilisateur grâce à son tocket
def get_user_id_from_token(token):
    CMD = "SELECT user_id FROM api_keys where token = '" + str(token) + "';"
    REQ = db_run(CMD, fetch=True, commit=False)
    for i in REQ.CONTENT:
        id = i[0]
    return id


# Récupération des permissions d'un utilisateur sur un projet.
def get_user_permission(user_id, projet_id):
    CMD = f"SELECT permissions FROM utilisateurs_permissions where utilisateur_id = '{user_id}' and projet_id = '{projet_id}';"
    REQ = db_run(CMD, fetch=True, commit=False)
    permission = -1
    for i in REQ.CONTENT:
        permission = i[0]
    return permission


# Récupération de la liste des tokens dans un tableau
def get_token_list(request, type):
    # TYPE :
    #  1 -> FULL LIST
    #  2 -> MY FULL LIST

    user_id = get_user_id_from_token(get_token(request))
    # GET PERMS
    admin = super_admin(user_id)
    # GET FULL LIST
    if type == 1:
        if admin == 0:
            data = {
                "error": True,
                "error_message": "Vous ne pouvez pas lister tous les tokens sans être admin !",
            }
        else:
            CMD = "select api_keys.id, api_keys.user_id, utilisateurs.username, api_keys.token, api_keys.date, api_keys.heure, api_keys.type from api_keys inner join utilisateurs on api_keys.user_id = utilisateurs.id where type = 1;"

    # GET ONLY MINE
    elif type == 2:
        CMD = f"select api_keys.id, api_keys.user_id, utilisateurs.username, api_keys.token, api_keys.date, api_keys.heure, api_keys.type from api_keys inner join utilisateurs on api_keys.user_id = utilisateurs.id where user_id = {user_id} and type = 1;"

    # GET LIST
    TOKENS = []
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


# Récupération du token dans le header "Authorization"
def get_token(request):
    bearer = request.headers.get("Authorization")
    token = bearer.split(" ")[1]
    return token

