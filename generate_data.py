import requests
import random
import string

# Configuration
BASE_URL = "http://localhost:8000"
USERNAME = "admin"
PASSWORD = "admin"

ID_PROJET_EXISTANT = 1
NB_TICKETS_EXISTANT = 50      # Tickets à créer pour le projet ID 1
NB_NOUVEAUX_PROJETS = 2       # Nombre de projets à créer
NB_TICKETS_PAR_PROJET = 30    # Tickets à créer par nouveau projet

TITRE_PROJET = "SRV"
DESCRIPTION_PROJET = "OBJ"
STATUTS_POSSIBLES = [1, 2, 3, 4, 5]


def get_token(username: str, password: str) -> str:
    url = f"{BASE_URL}/tokens"
    payload = {"username": username, "password": password, "type": 1}
    headers = {"Content-Type": "application/json"}
    response = requests.post(url, json=payload, headers=headers)
    response.raise_for_status()
    return response.json()['token']['token']


def create_project(token: str, titre: str, description: str) -> int:
    url = f"{BASE_URL}/projets"
    headers = {"Content-Type": "application/json", "Authorization": f"Bearer {token}"}
    payload = {"titre": titre, "description": description}
    response = requests.post(url, json=payload, headers=headers)
    response.raise_for_status()
    projet_id = response.json()["Ticket"]["Projet"]["id"]
    print(f"Projet créé (ID: {projet_id}) - Titre: {titre}")
    return projet_id


def create_ticket(token: str, projet_id: int, index: int = 0) -> int:
    url = f"{BASE_URL}/projets/{projet_id}/tickets"
    headers = {"Content-Type": "application/json", "Authorization": f"Bearer {token}"}

    categorie = random.choice([
        "bug", "feature", "support", "question",
        "maintenance", "sécurité", "performance", "accessibilité",
        "intégration", "UX", "mise à jour", "backup", "API", "régression"
    ])
    description = ''.join(random.choices(string.ascii_lowercase + string.digits, k=50))
    urgence = random.randint(0, 2)

    payload = {"categorie": categorie, "description": description, "urgence": urgence}

    response = requests.post(url, json=payload, headers=headers)
    response.raise_for_status()
    ticket_data = response.json()
    ticket_id = ticket_data.get("Ticket", {}).get("Ticket", {}).get("id")
    print(f"  → Ticket [{index}] créé (ID: {ticket_id}) - Catégorie: {categorie} | Urgence: {urgence}")
    return ticket_id


def change_statut(token: str, projet_id: int, ticket_id: int):
    url = f"{BASE_URL}/projets/{projet_id}/tickets/{ticket_id}/statut"
    headers = {"Content-Type": "application/json", "Authorization": f"Bearer {token}"}
    nouveau_statut = random.choice(STATUTS_POSSIBLES)
    payload = {"statut": nouveau_statut}
    response = requests.post(url, json=payload, headers=headers)
    response.raise_for_status()
    print(f"    ↳ Statut du ticket {ticket_id} changé à {nouveau_statut}")


def batch_create_tickets(token: str, projet_id: int, nb_tickets: int):
    for i in range(nb_tickets):
        ticket_id = create_ticket(token, projet_id, index=i+1)
        if ticket_id and i % 3 == 0:
            change_statut(token, projet_id, ticket_id)


def main():
    token = get_token(USERNAME, PASSWORD)
    print("✅ Token obtenu:", token)

    # Utiliser le projet existant (ID = 1)
    print(f"\n📁 Utilisation du projet existant ID={ID_PROJET_EXISTANT}")
    batch_create_tickets(token, ID_PROJET_EXISTANT, NB_TICKETS_EXISTANT)

    # Créer de nouveaux projets et leurs tickets
    for i in range(NB_NOUVEAUX_PROJETS):
        titre = f"{TITRE_PROJET}-{i+1}" if NB_NOUVEAUX_PROJETS > 1 else TITRE_PROJET
        projet_id = create_project(token, titre, DESCRIPTION_PROJET)
        batch_create_tickets(token, projet_id, NB_TICKETS_PAR_PROJET)


if __name__ == "__main__":
    main()
