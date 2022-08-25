<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link href="../css/index.css" rel="stylesheet" type="text/css"/>
        <link href="../css/nouvel-utilisateur.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    </head>
    <body>




    <?php
        include '../includes/menu-enfants.php';
        include '../includes/verif_licence_enfants.php';  
    ?>

               
        
        
        
        
        
        <!--
              Formulaire pour la création d'un nouvel utilisateur .
        -->
        
        
        
    <div id="page">
            
        <form id="formulaire" action="../actions/nouvel-utilisateur.php" method="post">
        
        <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-2 col-form-label text-primary bold">Nom d'utilisateur</label>
          <div class="col-sm-10">
              <input type="text" class="form-control bg-dark text-white" placeholder="Jean" name="utilisateur" pattern="[0-9a-zA-Z]{4,16}" title="Veuillez entrer entre quatre et seize caractères. Seulement des lettres et/ou des chifres !" required >
          </div>
        </div>
        <div class="row mb-3">
          <label for="inputPassword3" class="col-sm-2 col-form-label text-success" >Mot de passe</label>
          <div class="col-sm-10">
              <input type="password" class="form-control bg-dark text-white bold" name="motdepasse" placeholder="Valje@n1815" pattern="[0-9a-zA-Z!%&@#$^*?_~-|]{4,128}" required>
          </div>
        </div>
        <fieldset class="row mb-3">
          <legend class="col-form-label col-sm-2 pt-0 text-danger" id="privileges">Privilèges :</legend>
          <div class="col-sm-10">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="choix" value="Élevés" >
              <label class="form-check-label" for="choix">
                Élevés
              </label>
              <div class="text-muted fst-italic">Un utilisateur possédant les privilèges élevés peut créer, prendre en charge et archiver n'importe quel ticket. Il ou elle peut également modifier le mot de passe des utilisateurs.</div>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="choix" value="Normaux" checked> 
              <label class="form-check-label" for="choix">
                Normaux
              </label>
              <div class="text-muted fst-italic">Un utilisateur possédant les privilèges normaux peut créer de nouveaux tickets et prendre en charge d'autre tickets. Mais il ou elle ne peux pas archiver d'autres tickets que les siens.</div>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="choix" value="Faibles">
              <label class="form-check-label" for="choix">
                Faibles
              </label>
              <div class="text-muted fst-italic">Un utilisateur possédant les privilèges faibles peut créer de nouveaux tickets et consulter tous les tickets. Cependant, il ne pourra pas prendre en charge de tickets.</div>
            </div>
          </div>
        </fieldset>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Créer l'utilisateur</button>
      </form>

            
    </div>
       
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
