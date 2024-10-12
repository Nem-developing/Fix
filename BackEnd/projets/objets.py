# Imports 
from dataclasses import dataclass
from typing import Optional

########################
# OBJETS
########################


@dataclass
class projet:
    id: int
    titre: str
    description: str
    date: str
    statut: int