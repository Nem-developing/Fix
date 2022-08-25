<?php

/*

La table LOGS Commprendra
Un ID INT
Un utilisateur VARCHAR 16
Une action INT
Une date VARCHAR 10
Une heure VARCHAR 5
Une cible (ticket, utilisateur ou licence en question) VARCHAR 256

Liste des différentes actions possibles :
    # LES TICKETS 
    - 1 : Créer un ticket           --> INFO
    - 2 : Afficher les détails d'un ticket           --> INFO
    - 3 : Prendre en charge un ticket           --> INFO
    - 4 : Archiver un ticket           --> INFO
    - 5 : Désarchiver un ticket           --> INFO
    # Paramettres
    - 6 : Créer un utilisateur           --> Warning 
    - 7 : Supprimer un utilisateur           --> Warning
    - 8 : Changer le mdp d'un utilisateur           --> Warning
    - 9 : Enregistrer une licence           --> INFO


Options à enregistrer : 
    # LES TICKETS 
    - 1 : Quel ticket
    - 2 : Quel ticket
    - 3 : Quel ticket
    - 4 : Quel ticket
    - 5 : Quel ticket
    # Paramettres
    - 6 : Quel utilisateur
    - 7 : Quel utilisateur
    - 8 : Quel utilisateur
    - 9 : Quelle licence

*/

function SEND_LOGS($hotedeconnexion,$utilisateur,$motdepasse,$basededonnee,$action,$cible)
{
    // On récupére les variables
    $user = $_SESSION['utilisateur'];
    $date = strftime("%d/%m/%Y");
    $heure = strftime("%H:%M:%S");
   
    // Envoie à la DB
    $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
    $req = "INSERT INTO `logs` (`utilisateur`, `action`, `date`, `heure`, `cible`) VALUES ('$user', '$action', '$date', '$heure', '$cible');";
    $cnx->query($req);
    return;
}
