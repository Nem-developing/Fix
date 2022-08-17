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
        <link href="../css/index.css" rel="stylesheet" type="text/css"/>
        <link href="../css/parametres.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    </head>
    <body>
        
        <?php
        include '../includes/menu-enfants.php';
        include '../config/config.php';  // Import des informations de connexion à la base de données.
        ?>
        
       
        
        <ul style="padding-bottom: 1px;" class="nav nav-tabs bg-dark text-white" >
          <li class="nav-item">
            <a class="nav-link active text-white bg-success" aria-current="page" href="#">À Propos</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="gestion-utilisateurs.php">Gestion des Utilisateurs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">Statistiques</a>
          </li>
        </ul>
        
   
        
        <?php
                // Vérification si à jour
        $response = file_get_contents('https://api.nehemiebarkia.fr/fix/?latest=ask');
        $obj = json_decode($response);
        $newversion = $obj->{'Version'};
        if ($obj->{'Version'} > $versiondefix) {
            echo "<div class='alert alert-success text-center bg-success' href='https://github.com/Nem-developing/API/releases/latest' role='alert'>
                    FIX $newversion EST DISPONIBLE ! Mettez à jour votre application pour profiter des mises à jours !
                  </div>";
        } else {
            echo "<div class='alert alert-info text-center bg-info' href='https://github.com/Nem-developing/API/releases/latest' role='alert'>
                    Félicitation, vous possédez la dernière version de FIX ! 
                  </div>";
        }
        
        
        // Affichage erreur :
        if ($_GET['erreur'] === "licencemanquante"){
            echo "<div class='alert alert-danger text-center bg-danger' role='alert'>
            Vous avez été redirigés ici car vous n'avez pas enregisté de licence !
        </div>";
        }
        
        
        
        // Vérification licence
        
        include "../config/config.php"; 
        $mysqli = new mysqli("$hotedeconnexion", "$utilisateur", "$motdepasse", "$basededonnee");
        if (!$mysqli->query("CREATE TABLE IF NOT EXISTS `options` (`id` INT, `licence` varchar(22) NOT NULL);")) {
                echo "<div class='alert alert-danger' role='alert'> Echec lors de la création de la table options ! </div>";    // Affichage de l'erreur.
                echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
        }   
       
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
        
        
        if ($cpt >= 2){
            echo "<div class='alert alert-danger text-danger' role='alert'> Erreur : Deux lignes d'options dans la base de donnée ! </div>";    // Affichage de l'erreur.
        }
        
        
            
        // Vérification si à jour
        $response = file_get_contents("https://api.nehemiebarkia.fr/fix/?verify=$licenceactuelle");
        $obj = json_decode($response);

        $licence_statut = $obj->{'Statut'};
        $licence_date_exp = $obj->{'Date_Expiration'};
        $licence_heure_exp = $obj->{'Heure_Expiration'};
        $licence_type = $obj->{'Type_de_Licence'};
        $licence_date_crea = $obj->{'Date_de_Création'};
        $licence_heure_crea = $obj->{'Heure_de_Création'};    
        
        
        if ($licence_statut === "Active") {
            $colorbg = "bg-warning text-dark";
        } else {
            $colorbg = "bg-danger text-white";
        }
        
        
        if(isset($_POST['trial'])){
            // Récupération d'une trial
            // Vérification si à jour
            $response = file_get_contents("https://api.nehemiebarkia.fr/fix/?trial=ask");
            $obj = json_decode($response);
            $licence_trial = $obj->{'key'};
            insertlicence($hotedeconnexion,$utilisateur,$motdepasse,$basededonnee,$licence_trial);
            header("Refresh:0; url=parametres.php");
        }
        if(isset($_POST['recordlicence'])){
            insertlicence($hotedeconnexion,$utilisateur,$motdepasse,$basededonnee,$_POST['licenceaenregistrer']);
            header("Refresh:0; url=parametres.php");
            
        }
        
        // INSSERTION D'UNE LICENCE DANS LA DB
        function insertlicence($hotedeconnexion,$utilisateur,$motdepasse,$basededonnee,$licence){
            // Établissement de la connexion au serveur mysql.
            $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
            // Commande récupérant l'utilisateur connecté.
            $req = 'SELECT * FROM options;';
            // Envoie au serveur la commande via le biais des informations de connexion.
            $res = $cnx->query($req);
            $cpt = 0;
            
            // Boucle tant qu'il y a de lignes corespondantes à la requette une.
            while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                $cpt = $cpt + 1;
            }
            
            
            $mysqli = new mysqli("$hotedeconnexion", "$utilisateur", "$motdepasse", "$basededonnee");

            if ($cpt === 0) {
                if (!$mysqli->query("INSERT INTO options (id, licence) VALUES (1, '$licence');")) {
                    echo "<div class='alert alert-danger' role='alert'> Echec lors de l'ajout de la licence dans la base de données ! </div>";    // Affichage de l'erreur.
                    echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                }  
            } else {
                if (!$mysqli->query("UPDATE `options` SET `id` = '1', `licence` = '$licence' WHERE `id` = '1' limit 1;")) {
                    echo "<div class='alert alert-danger' role='alert'> Echec lors de l'ajout de la licence dans la base de données ! </div>";    // Affichage de l'erreur.
                    echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                }  
            }
        }
        ?>
        
        
        

        

        <div id="formulaire" class="text-white">
            <div class="p-3 mb-2 <?php echo $colorbg; ?>" id="titre">Licence du logiciel</div>
            <form method="post">
                <div class="form-group">
                    <label>Licence : <?php echo $licenceactuelle; ?></label>
                </div>
                <div class="form-group">
                    <label class="text-success">Statut : <?php echo $licence_statut; ?></label>
                </div>
                
                <div class="form-group">
                    <label class="text-primary">Enregistrée le : <?php echo "$licence_date_crea à $licence_heure_crea"; ?></label>
                </div>
                <div class="form-group">
                    <label class="text-warning">Expire le : <?php echo "$licence_date_exp à $licence_heure_exp"; ?></label>
                </div>
                <div class="form-group">
                    <label>Version de Fix : <?php echo $versiondefix; ?></label>
                </div>
            </form>
            <form method="post">
                <?php
                if ($licence_statut != "Active") { 
                    echo "<div class='form-group'>
                                <label class='text-white'>Veuillez spécifier votre clef de licence :</label>
                               <input type='text' name='licenceaenregistrer' class='form-control text-white' id='exampleInputEmail1' placeholder='Exemple : 12345-XXXXXXXXXX-12345' pattern='[0-9]{5}-[A-Z]{10}-[0-9]{5}' required>
                               <button type='submit' name='recordlicence' class='btn bg-success btn-primary btn-lg btn-block'>Enregistrer ma licence</button>
                           </div> 
                        </form>
                        <form method='post'>
                           <div class='form-group'>
                               <button type='submit' name='trial' class='btn bg-warning text-primary bold btn-primary btn-lg btn-block'>Profiter de 30 jours gratuit !</button>
                           </div>";
                }
                ?>
            </form>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</html>
