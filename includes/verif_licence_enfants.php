<?php
    include '../config/config.php';  // Import des informations de connexion à la base de données.
    // Établissement de la connexion au serveur mysql.
    $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
    // Commande récupérant l'utilisateur connecté.
    $req = 'SELECT * FROM options;';
    // Envoie au serveur la commande via le biais des informations de connexion.
    $res = $cnx->query($req);
    $cpt = 0;

    // Boucle tant qu'il y a de lignes corespondantes à la requette une.
    while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
        $licenceactuelle = $ligne->licence;
        $cpt = $cpt + 1;
    }

    if (!$licenceactuelle) {
        header('Location: ../pages/parametres.php?erreur=licencemanquante');
        exit();
    }
    
    // Vérification si à jour
    $response = file_get_contents("https://api.nehemiebarkia.fr/fix/?verify=$licenceactuelle");
    $obj = json_decode($response);
    $licence_statut = $obj->{'Statut'};
    if ($obj->{'Statut'} != "Active") {
        header('Location: ../pages/parametres.php?erreur=licencemanquante');
        exit();
    }
    
    