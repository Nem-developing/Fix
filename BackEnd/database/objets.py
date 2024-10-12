# Imports 
from dataclasses import dataclass
from typing import Optional

########################
# OBJETS
########################


@dataclass
class body_sql:
    CONTENT: str
    ERROR: bool
    ERROR_MSG: Optional[str] = "None"

