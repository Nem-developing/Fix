<?php

    /*  
     *  =========================================================================
     *  Veuillez spécifiez les informations de connection à votre base de donnée.
     * 
     *  Veuillez vous référer à la documentation !
     *  =========================================================================
     */ 

    $hotedeconnexion = "127.0.0.1"; // 127.0.0.1 = Localhost
    $basededonnee = "tickets_nemixcraft";
    $utilisateur = "tickets_nemixcraft_user";
    $motdepasse = "ENKi9S5iLrrAmB6yWQSI";
    $versiondefix = 2.2;        // NE PAS TOUCHER

    /* =========================================================================
     * Voici comment fonctionne les tickets dans la base de donnée : 
     * 
     *                      Différentes options du statut :
     * 
     *  0 : Non-Traité
     *  1 : En-Cour
     *  2 : Archivé
     * 
     *                      Différentes options de niveau d'urgence :
     *  
     * 0 : Faible
     * 1 : Normale
     * 2 : Urgent
     * 
     * 
     * =========================================================================
     */

    
    /* =========================================================================
     * Voici comment fonctionne les utilisateurs dans la base de donnée : 
     * 
     * Niveau de permission :
     * 
     * 0 -> Lecture seulement
     * 1 -> Prise en charge de tickets et leurs archivage.
     * 2 -> Archivage de nimporte quel tickets.
     * 
     * 
     * =========================================================================
     */



    // Tentative de connexion au seveur de bases de données.
    $connexion = mysqli_connect($hotedeconnexion, $utilisateur, $motdepasse, $basededonnee);
    
    // Affichage d'un message d'erreur si il y a un soucis avec l'établissement du lien entre PHP et MYSQL - MARIADB
    if(!$connexion) {
        echo("Problème de connexion à la base de données ! Vérifiez les informations saisies dans la configuration.");
    }
?>
