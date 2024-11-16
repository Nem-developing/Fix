<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
}
include '../includes/verif_licence_enfants.php';  

?> 
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link href="../css/index.css" rel="stylesheet" type="text/css"/>
        <link href="../css/nouveau-mdp.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    </head>
    <body>




    <?php
        include '../includes/menu-enfants.php';
        
        // Récupération du compte a supprimer
        $compte = $_GET['compte'];
    ?>

        
        
    <div id="page">
            
        
        
            
       
        <h1>Modification du mot de passe de l'utilisateur <a class="text-warning"><?php echo"$compte";?></a> :</h1>   

        <form id="formulaire" action="../actions/nouveau-mdp.php?compte=<?php echo "$compte";?>" method="post">
          <div class="col-sm-10">
              <label class="form-label text-primary bold">Nouveau mot de passe</label>
              <input type="password" id="motdepasse" class="form-control bg-dark text-white bold" name="motdepasse" placeholder="Mon mot de passe" pattern="[0-9a-zA-Z!%&@#$^*?_~-|]{4,128}" required>
          </div>

          <div class="col-sm-10">
              <label class="form-label text-success bold">Confirmer le mot de passe</label>
              <input type="password" id="motdepasseconfirmation" class="form-control bg-dark text-white bold" name="motdepasseconfirm" placeholder="Confirmation" pattern="[0-9a-zA-Z!%&@#$^*?_~-|]{4,128}" required>
          </div>
            <div id="boutons">
                <br>
                <div class="col boutons"><a href="parametres.php"><button type="button" class="btn btn-primary btn-lg btn-block">Annuler</button></a></div>
                <div class="col boutons"><button type="submit" class="btn btn-danger btn-lg btn-block" onclick="return verificationdeschamps()">Modifier le mot de passe</button></div>
            </div>
      </form>
            
    </div>
       
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    
    <script type="text/javascript">
    function verificationdeschamps() {
        var motdepasse = document.getElementById("motdepasse").value;
        var motdepasseconfirmation = document.getElementById("motdepasseconfirmation").value;
        if (motdepasse === motdepasseconfirmation) {
            return true;
        } else {
            alert("Les mots de passes ne concordent pas !");
            return false;
        }
        
    }
</script>
    
    
    </body>
</html>
