<?php

    include './config/config.php';  
    $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
    $req = 'SELECT * FROM options;';
    $res = $cnx->query($req);
    $cpt = 0;
        
    if (!$res){
        header('Location: ./pages/parametres.php?erreur=licencemanquante');
        exit();
    } else {
        while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
            $licenceactuelle = $ligne->licence;
            $cpt = $cpt + 1;
        }   
    }
    
    
    
    // Vérification si à jour
    $response = file_get_contents("https://api.nehemiebarkia.fr/fix/?verify=$licenceactuelle");
    $obj = json_decode($response);
    $licence_statut = $obj->{'Statut'};
    if ($obj->{'Statut'} != "Active") {
        header('Location: ./pages/parametres.php?erreur=licencemanquante');
        exit();
    }
