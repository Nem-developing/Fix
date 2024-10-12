# Imports 
from dataclasses import dataclass
from typing import Optional

########################
# OBJETS
########################


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
