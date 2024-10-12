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