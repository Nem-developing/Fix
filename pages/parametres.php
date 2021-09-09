<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
}




// Données GRAPH TICKETS
include '../config/config.php';  // Import des informations de connexion à la base de données.
// Établissement de la connexion au serveur mysql.
$conn = new mysqli($hotedeconnexion, $utilisateur, $motdepasse, $basededonnee);

// Données GRAPH UTILISATEURS
$sql4 = "SELECT * FROM `connexion` where `permissions` = '0';";
if ($result=mysqli_query($conn,$sql4)) {
     $utilisateursfaibles=mysqli_num_rows($result);
}


$sql5 = "SELECT * FROM `connexion` where `permissions` = '1';";
if ($result=mysqli_query($conn,$sql5)) {
    $utilisateursnormaux=mysqli_num_rows($result);
}


$sql6 = "SELECT * FROM `connexion` where `permissions` = '2';";
if ($result=mysqli_query($conn,$sql6)) {
    $utilisateurseleves=mysqli_num_rows($result);
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
        <link href="../css/index.css" rel="stylesheet" type="text/css"/>
        <link href="../css/nouveau-ticket.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
        <!--
        Graphiques 
        -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
          google.charts.load('current', {'packages':['corechart']});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {

            
            var datautilisateurs = google.visualization.arrayToDataTable([
              ['Task', 'Utilisateurs'],
              ['Privilèges Élevés',     <?php echo $utilisateurseleves;?>],
              ['Privilèges Normaux',      <?php echo $utilisateursnormaux;?>],
              ['Privilèges Faible',    <?php echo $utilisateursfaibles;?>]
            ]);


              
            
                    
            var optionsutilisateurs = {
              title: 'États des utilisateurs',
              backgroundColor: '#343a40',
              titleTextStyle: { color: "white", fontSize: 16},
              legend: {textStyle: {color: 'white'}}

              
            };

            var chartutilisateurs = new google.visualization.PieChart(document.getElementById('piechart-utilisateurs'));

            chartutilisateurs.draw(datautilisateurs, optionsutilisateurs);
          }
        </script>
    </body>
    </head>
    <body>
        
        <?php
        
        include '../includes/menu-enfants.html';
        
        //   Différentes erreurs :
        //
        //   N°1 : On ne peut pas créer un utilisateur déjà existant
        //
        
        
        
        //   Affichage des erreurs
        if (!isset($_GET['erreur'])) {} else {
            if ($_GET['erreur'] === "1"){
                      
                  echo "<center><a class='h3 alertefix text-danger'>Vous ne pouvez pas créer d'utilisateur déjà existant !</a></center>"; 
            }
        }

        
                    
        
        ?>
      
        
        
        
        
        
        
        
        
        <!--
              Liste des utilisateurs
        -->
        <div class="bg-dark">
            
        
        <div style="align-content: center; border: solid; border-color: grey; height: 250px; background-color: #343a40 !important;" >
            <div id="piechart-utilisateurs" style="width: 50%; height: 100%; float: left;"></div>

            <div style="width: 50%; height: 100%; float: left; background-color: #343a40 !important;"></div>
        </div>
            
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
                        $perm = "Basiques";
                        break;
                    case 1:
                        $perm = "Normaux";
                        break;
                    case 2:
                        $perm = "Élevés";
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
             if (($_SESSION['permissions'] == 2) && ($_SESSION['utilisateurid'] != $ligne->id)){
                 echo"<td><button type='button' class='btn btn-outline-success'><a href='./nouveau-mdp.php?compte=$ligne->utilisateur'>Modifier le mot de passe</a></button></td>";
                 echo"<td><button type='button' class='btn btn-outline-danger'><a href='./confirmer-la-suppression.php?compte=$ligne->utilisateur'>Supprimer l'utilisateur</a></button></td>";
             } else if (($_SESSION['permissions'] == 2) && ($_SESSION['utilisateurid'] == $ligne->id)){
                 // Si superadmin et moi
                 echo"<td><button type='button' class='btn btn-outline-success'><a href='./nouveau-mdp.php?compte=$ligne->utilisateur'>Modifier mon mot de passe</a></button></td>";
                 echo"<td><p class='text-warning'>Vous ne pouvez pas vous supprimer vous-même.</p></td>";                 
             } 
             // Si pas superadmin et moi
             if (($_SESSION['permissions'] != 2) && ($_SESSION['utilisateurid'] == $ligne->id)) {
                 echo"<td><button type='button' class='btn btn-outline-success'><a href='./nouveau-mdp.php?compte=$ligne->utilisateur'>Modifier mon mot de passe</a></button></td>";
                 echo"<td><button type='button' class='btn btn btn-outline-danger'><a href='./confirmer-la-suppression.php?compte=$ligne->utilisateur'>Supprimer mon compte</a></button></td>"; 
             } 
             // Si pas superadmin et pas moi
             if (($_SESSION['permissions'] != 2) && ($_SESSION['utilisateurid'] != $ligne->id)){
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
        <a href="nouvel-utilisateur.php"><button type="button" class="btn btn-primary btn-lg btn-block">Créer un nouvel utilisateur</button></a>
        
       

       
       
        
       
       
       
       
       
       
       
       
       
       
       
       
                </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        
</html>
