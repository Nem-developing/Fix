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


# Commande permetant l'envoie d'une requette SQL avec les options fetch et commit.
def db_run(CMD, fetch=True, commit=False):
    ERROR = False
    DATA = {}
    try:
        
        # établir une connexion à la base de donnéess
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


# Vérification de l'état de la base de données.
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


# Vérifiaction de la base de données avec un retour booléen.
def check_if_everything_is_ok():
    db_statut, db_error = db_ok()
    if db_error == True:
        return False
    return True




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



# Vérification et création d'une database si besoin
def check_and_create_db_if_required(db_name, req_to_create_db):
    if verif_table(str(db_name)) == False:
        print("--> [" + str(db_name) + "] La table est en cours de création ...", end="")
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
                "INSERT INTO `utilisateurs` (`username`, `password`, `creation`, `super_admin`) VALUES ('admin', '"
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


# Création des tables si necessaires
def prepare():
    print("Initialisation de l'API de FIX " + str(VERSION))

    # Accès à la DB
    try:
        if acces_db() == True:
            print("--> [OK] : Connexion à la base de donnéess réussie !\n")
        else:
            print("--> [KO] : Connexion à la base de donnéess échouée !\n")
            exit(1)
    except:
        print("--> [KO] : Connexion à la base de donnéess échouée !\n")
        exit(1)

    # Tables présentes
    print("Vérifcation des tables :")
    try:
        # Les requettes de créations de tables
        req_create_projets = "CREATE TABLE `projets` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `titre` varchar(50), `description` varchar(1024), `date` varchar(10) NOT NULL, `statut` int NOT NULL);"
        req_create_tickets = "CREATE TABLE `tickets` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `projet_id` INT NOT NULL , `serveur` varchar(50) NOT NULL, `objet` varchar(50) NOT NULL, `description` longtext NOT NULL, `date` varchar(10) NOT NULL, `heure` varchar(10) NOT NULL, `utilisateur_emmeteur_du_ticket` varchar(25) NOT NULL, `date_pec` varchar(10) NOT NULL, `heure_pec` varchar(10) NOT NULL, `date_fin` varchar(10) NOT NULL, `heure_fin` varchar(10) NOT NULL, `urgence` INT NOT NULL, `statut` INT NOT NULL, `technicien_affecte` varchar(25) NOT NULL, `technicien_qui_archive` varchar(25) NOT NULL, FOREIGN KEY (projet_id) REFERENCES projets(id) );"
        req_create_utilisateurs = "CREATE TABLE `utilisateurs` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `username` varchar(16) NOT NULL, `password` varchar(512) NOT NULL, `super_admin` INT NOT NULL, `creation` varchar(10) NOT NULL );"
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
            "--> [KO] : Une erreur est survenue ! Nous avons pas pû correctement vérifier la base de données.\n"
        )
    return