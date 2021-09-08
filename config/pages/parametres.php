<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
}
?> 
<!DOCTYPE html>
<!--
Projet réalisé par Nem-developing, tout droits réservés.
-->
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link href="../css/nouveau-ticket.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    </head>
    <body>

        <?php
        include '../includes/menu.php?type=niveau-enfants';
        ?>
        
        
        <!--
              Liste des utilisateurs
        -->
        <div class="bg-dark">
        <table class="table table-dark table-striped">
            <thead>
          <tr>
            <th scope="col">Utilisateur</th>
            <th scope="col">Privilèges</th>
            <th scope="col">Date de création</th>
            <th scope="col">Changer le mot de passe</th>
            <th scope="col">Supprimer l'utilisateur</th>
          </tr>
        </thead>
        
        <tbody>
             <?php
             
        function levelperm($idperm)
        {
            // Changement de l'INT en texte.
                switch ($idperm) {
                    case 0:
                        $perm = "Lecture / Écriture";
                        break;
                    case 1:
                        $perm = "Lecture / Écriture / P.E.C / Archivage";
                        break;
                    case 2:
                        $perm = "Administrateur";
                        break;
                }
            return $perm;
        }
             
             
             
        include '../config/config.php';  // Import des informations de connexion à la base de données.
        // Établissement de la connexion au serveur mysql.
        $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
        // Requette SQL.
        $req = 'SELECT * FROM `connexion`';
        // Envoie au serveur la commande via le biais des informations de connexion.
        $res = $cnx->query($req);
        
        
         while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
             
             echo"
            <tr>
                <th scope='row'>$ligne->utilisateur</th>
                <td>"; echo levelperm($ligne->permissions);
             echo "</td>
                 
                <td>$ligne->creation</td>";
             
             
             // Si superAdmin et pas moi
             if (($_SESSION['permissions'] == 2) && ($_SESSION['utilisateur'] != $ligne->utilisateur)){
                 echo"<td><button type='button' class='btn btn-outline-success'>Modifier le mot de passe</button></td>";
                 echo"<td><button type='button' class='btn btn-outline-danger'>Supprimer l'utilisateur</button></td>";
             } else if (($_SESSION['permissions'] == 2) && ($_SESSION['utilisateur'] == $ligne->utilisateur)){
                 // Si superadmin et moi
                 echo"<td><button type='button' class='btn btn-outline-success'>Modifier mon mot de passe</button></td>";
                 echo"<td><p class='text-warning'>Vous ne pouvez pas supprimer le compte administrateur.</p></td>";                 
             } 
             // Si pas superadmin et moi
             if (($_SESSION['permissions'] != 2) && ($_SESSION['utilisateur'] == $ligne->utilisateur)) {
                 echo"<td><button type='button' class='btn btn-outline-success'>Modifier mon mot de passe</button></td>";
                 echo"<td><button type='button' class='btn btn btn-outline-danger'>Supprimer mon compte</button></td>"; 
             } 
             // Si pas superadmin et pas moi
             if (($_SESSION['permissions'] != 2) && ($_SESSION['utilisateur'] != $ligne->utilisateur)){
                 echo"<td></td>";
                 echo"<td></td>";
             }
             
             
             echo"</tr>";
             
         }
         ?>  
          </tbody>
</table>
        
        
        
        <!--
              Création d'un nouvel utilisateur
        -->
       <button type="button" class="btn btn-primary btn-lg btn-block">Créer un nouvel utilisateur</button>
        
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
