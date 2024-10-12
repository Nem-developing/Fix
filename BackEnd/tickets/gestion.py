########################
# Fonctions de gestion
########################


# Récupérer tous les tickets et retourner un tableau.
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


# Récupérer un seul ticket grâce à son id et son numéro de projet.
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


# Récupération de l'état d'un ticket grâce à son id et son projet_id.
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




# Modifier l'état d'un ticket.
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


## Créer un ticket
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


## Récupérer tous les commentaires d'un ticket
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


## Récupérer le commentaire d'un ticket grâce à son id, l'id de tiket et l'id de projet.
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






## Création d'un commentaire
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


## modification d'un commentaire
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