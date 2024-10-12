# Imports 
from dataclasses import dataclass
from typing import Optional

########################
# OBJETS
########################


@dataclass
class utilisateur:
    id: int
    username: str
    password: str
    super_admin: bool
    creation: str