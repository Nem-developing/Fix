<?php

    /*  
     *  =========================================================================
     *  Veuillez spécifiez les informations de connection à votre base de donnée.
     * 
     *  Veuillez vous référer à la documentation !
     *  =========================================================================
     */ 

    /*  
     *  =========================================================================
     *  Décommentez les variables liés à la base de donnée ! 
     *  =========================================================================
     */ 


    //$hotedeconnexion = "127.0.0.1"; // 127.0.0.1 = Localhost
    //$basededonnee = "Fix";
    //$utilisateur = "Fix-user";
    //$motdepasse = "Fix-MDP";
    $versiondefix = 2.5;        // NE PAS TOUCHER

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

?>
