<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
}
include '../includes/verif_licence_enfants.php';  
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
        <link href="../css/index.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Logs</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    </body>
    </head>
    <body>
        
        <?php
        
        include '../includes/menu-enfants.php';
               
        ?>
        
        
        <ul style="padding-bottom: 1px;" class="nav nav-tabs bg-dark text-white" >
          <li class="nav-item">
              <a class="nav-link text-white" aria-current="page" href="parametres.php">À Propos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="gestion-utilisateurs.php">Gestion des Utilisateurs</a>
          </li>
          <li class="nav-item">
              <a class="nav-link active text-white bg-success" href="#">Logs</a>
          </li>
        </ul>
        
       
        <!--
              Liste des logs
        -->
        
        
        <?php
        
        if (!$_GET['offset']){
            $offset = 0;
        } else {
            $offset = $_GET['offset'];
        }
        
        if ($_GET['offset']){
            $offsettemp= $offset-50;
            echo "<a href='?offset=$offsettemp'><button type='button' class='btn btn-warning btn-lg btn-block'>Afficher les Journeaux plus récents</button></a>";
        }
        
        
        ?>
        
        <div class="bg-dark">
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Journal</th>
                            <th scope="col">Type</th>
                            <th scope="col">Date</th>
                            <th scope="col">Heure</th>                            
                            <th scope="col">Utilisateur</th>
                            <th scope="col">Action Réalisée</th>
                            <th scope="col">Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../config/config.php';  // Import des informations de connexion à la base de données.
                        $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
                        
                        $cpt =0;
                        $req = "SELECT * FROM `logs` ORDER BY id DESC LIMIT 50 OFFSET $offset;";
                        $res = $cnx->query($req);
                        while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            
                            
                            switch ($ligne->action) {
                                case 1:
                                    // Ticket créé
                                    $message1 = "<td><a class='text-success'>INFO</a></td>";
                                    $message2 = "<td><a href = './ticket.php?id=$ligne->cible' class='text-success'>A créé un ticket</a></td><td><a href = './ticket.php?id=$ligne->cible' class='text-success'>Ticket N°$ligne->cible</a></td></tr>";
                                    break;
                                case 2:
                                    // Afficher les détails d'un ticket
                                    $message1 = "<td><a class='text-success'>INFO</a></td>";
                                    $message2 = "<td><a href = './ticket.php?id=$ligne->cible' class='text-success'>A affiché les détails d'un ticket</a></td><td><a href = './ticket.php?id=$ligne->cible' class='text-success'>Ticket N°$ligne->cible</a></td></tr>";
                                    break;
                                case 3:
                                    // Prendre en charge un ticket
                                    $message1 = "<td><a class='text-success'>INFO</a></td>";
                                    $message2 = "<td><a href = './ticket.php?id=$ligne->cible' class='text-success'>A pris en charge un ticket</a></td><td><a href = './ticket.php?id=$ligne->cible' class='text-success'>Ticket N°$ligne->cible</a></td></tr>";
                                    break;
                                case 4:
                                    // Archiver un ticket
                                    $message1 = "<td><a class='text-success'>INFO</a></td>";
                                    $message2 = "<td><a href = './ticket.php?id=$ligne->cible' class='text-success'>A archivé un ticket</a></td><td><a href = './ticket.php?id=$ligne->cible' class='text-success'>Ticket N°$ligne->cible</a></td></tr>";
                                    break;
                                case 5:
                                    // Désarchiver un ticket
                                    $message1 = "<td><a class='text-success'>INFO</a></td>";
                                    $message2 = "<td><a href = './ticket.php?id=$ligne->cible' class='text-success'>A désarchivé un ticket</a></td><td><a href = './ticket.php?id=$ligne->cible' class='text-success'>Ticket N°$ligne->cible</a></td></tr>";
                                    break;
                                case 6:
                                    // Créer un utilisateur 
                                    $message1 = "<td><a class='text-warning'>Prudence</a></td>";
                                    $message2 = "<td><a href = './gestion-utilisateurs.php' class='text-warning'>A créé un utilisateur</a></td><td><a href = './ticket.php?id=$ligne->cible' class='text-success'>Utilisateur : $ligne->cible</a></td></tr>";

                                    break;
                                case 7:
                                    // Supprimer un utilisateur 
                                    $message1 = "<td><a class='text-warning'>Prudence</a></td>";
                                    $message2 = "<td><a href = './gestion-utilisateurs.php' class='text-warning'>A supprimé un utilisateur</td><td><a href = './parametres.php' class='text-warning'>Utilisateur : $ligne->cible</a></td></tr>";
                                    break;
                                case 8:
                                    // Changer le mdp d'un utilisateur 
                                    $message1 = "<td><a class='text-warning'>Prudence</a></td>";
                                    $message2 = "<td><a href = './gestion-utilisateurs.php' class='text-warning'>A changé le mot de passe d'un utilisateur</a></td><td><a href = './parametres.php' class='text-warning'>Utilisateur : $ligne->cible</a></td></tr>";

                                    break;
                                case 9:
                                    // Enregistrer une licence
                                    $message1 = "<td><a class='text-success'>INFO</a></td>";
                                    $message2 = "<td><a href = './parametres.php' class='text-success'>A enregistré une nouvelle licence</a></td><td><a href = './parametres.php' class='text-success'>Licence N°$ligne->cible</a></td></tr>";
                                    break;
                            }
                            echo "<tr class='bg-white text-white'>
                                    <th scope='row'>N°$ligne->id</th>
                                    $message1
                                    <td><a class='text-white-50'>$ligne->date</a></td>
                                    <td><a class='text-white-50'>$ligne->heure</a></td>
                                    <td><a class='text-white'>$ligne->utilisateur</a></td>
                                    $message2";
                            $cpt+=1;
                                    
                        }  
                        
                        
                        
                                           
 echo "                   </tbody>
                </table>
            </div>
        </div>";
 
                        $offset += 50;
                        if ($cpt === 50){
                            echo "<a href='?offset=$offset'><button type='button' class='btn btn-primary btn-lg btn-block'>Afficher les Journeaux plus anciens</button></a>";
                        }
?>     
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        
</html>
