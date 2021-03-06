<?php

session_start();

include 'connex.inc.php';

try {

    if (isset($_GET['id']) AND $_GET['id'] > 0) {
        $getid = intval($_GET['id']);
        $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
        $requser->execute(array($getid));
        $userinfo = $requser->fetch();
        if (!isset($_SESSION['id'])) 
            header('LOCATION:formulaire_chambre.php');
        else if ($userinfo['id'] != $_SESSION['id'])
            header('LOCATION:formulaire_chambre.php?id='.$_SESSION['id']);
    }
    else if (isset($_SESSION['id'])) {
        header('LOCATION:formulaire_chambre.php?id='.$_SESSION['id']);
    }

    if (isset ($_POST['id_chambre'])) {
        $test = $pdo->query("SELECT id FROM chambre");
        while ($query = $test->fetch()) {
            $chambre=$query['id'];
            if (isset ($_POST['reserver'.$chambre])) {
                $_SESSION['id_chambre']=$chambre;
		$email_to = "ouchtitiwalid@gmail.com";
        	$email_subject = "Réservation Chambre";
        	$email_message = "message";
        	@mail($email_to, $email_subject, $email_message);
            }
        }
    }
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Projet Web</title>

    <link rel="icon shortcut" href="../images/favicon.png">

    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <link href="../css/formulaire_chambre.css" rel="stylesheet" type="text/css">

</head>

<body>

<header>

    <div id="logo">

        <div class="inner">

            <a href="index_white.php">

                <img src="../images/favicon.png" alt="logo">

            </a>

        </div>

        <div id="profil">
        <?php 
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) {
        ?>
            <h2> Bienvenue <?php echo $userinfo['prenom'].' '.$userinfo['nom']; ?></h2>
            <a href="deconnexion.php"> Se déconnecter </a>
        <?php
                }
                else {
        ?>
            <h2> Espace administrateur - <?php echo ' '.$userinfo['prenom'].' '.$userinfo['nom']; ?></h2>
            <a href="deconnexion.php"> Se déconnecter </a>
        <?php
                }
            }
        }
        ?>
        </div>

    </div>

    <div id="mainmenu-container">

        <ul id="mainmenu">

            <?php 
            if (isset($_GET['id']) AND $_GET['id'] > 0) {
                if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
            ?>

                <?php echo '<li><a href="index.php?id='.$_GET['id'].'">Accueil</a></li>'; ?>

                <li><a href="<?php echo 'hebergement.php?id='.$_GET['id'].''; ?>">Hebergement</a></li>

                <li><a href="<?php echo 'contact.php?id='.$_GET['id'].''; ?>">Contact</a></li>

                <li><a href="<?php echo 'transport.php?id='.$_GET['id'].''; ?>">Transport</a></li>
            <?php
                }
            }

            else {
            ?>

                <li><a href="index.php">Accueil</a></li>

                <li><a href="hebergement.php">Hebergement</a></li>

                <li><a href="contact.php">Contact</a></li>

                <li><a href="transport.php">Transport</a></li>

            <?php
            }

            if (isset($_GET['id']) AND $_GET['id'] > 0) {
                if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                    if ($userinfo['admin'] == 0) {
            ?>
                    <li><a href="#">Mon profil</a>

                        <ul>

                        <?php 

                        echo '<li><a href="modifier.php?id='.$_GET['id'].'">Modifier mes informations</a>';

                        $reqreservetion = $pdo->prepare('SELECT * FROM reservation WHERE id_client = ?');
                        $reqreservetion->execute(array($_SESSION['id']));
                        $reservation = $reqreservetion->fetch();
                        $nbreservation2 = $reqreservetion->rowCount();

                        $reqreservetion1 = $pdo->prepare('SELECT * FROM transfert_aeroport WHERE id_client = ?');
                        $reqreservetion1->execute(array($_SESSION['id']));
                        $reservation1 = $reqreservetion1->fetch();
                        $nbreservation1 = $reqreservetion1->rowCount();

                        $nbreservation = $nbreservation1 + $nbreservation2;

                        if ($nbreservation > 0) {
                            echo '<li><a href="reservations.php?id='.$_GET['id'].'">Mes réservations - ('.$nbreservation.')</a>';
                        }

                        ?>

                        <?php echo '<li><a href="deconnexion.php">Deconnexion</a>'; ?>

                        </ul>

                    </li>

                <?php
                    }
                    else {
                ?>
                    <li><a href="#">Utilisateurs</a>

                        <ul>

                        <?php echo '<li><a href="modifier.php?id='.$_GET['id'].'">Mon profil</a>'; ?>

                        <?php echo '<li><a href="utilisateurs.php?id='.$_GET['id'].'">Utilisateurs</a>'; 

                        $reqreservetion = $pdo->prepare('SELECT * FROM reservation');
                        $reqreservetion->execute(array($_SESSION['id']));
                        $reservation = $reqreservetion->fetch();
                        $nbreservation2 = $reqreservetion->rowCount();

                        $reqreservetion1 = $pdo->prepare('SELECT * FROM transfert_aeroport');
                        $reqreservetion1->execute(array($_SESSION['id']));
                        $reservation1 = $reqreservetion1->fetch();
                        $nbreservation1 = $reqreservetion1->rowCount();

                        $nbreservation = $nbreservation1 + $nbreservation2;

                        if ($nbreservation > 0) {
                            echo '<li><a href="reservations.php?id='.$_GET['id'].'">Voir les réservations - ('.$nbreservation.')</a>';
                        }

                        ?>

                        <?php echo '<li><a href="deconnexion.php">Deconnexion</a>'; ?>

                        </ul>

                    </li>
            <?php
                    }
                }
            }
            else {
                echo '<li><a href="inscription.php">Inscription - Connexion</a></li>';
            }
            ?>

        </ul>

    </div>

    <div id="menu-btn"></div>

</header>

    <?php 
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 1) {
                    echo '<div id="erreur">';
                    echo '<br><h2> <font color="red">Vous ne pouvez pas effectuer des réservations depuis un compte administrateur</font> </h2>';
                    echo '<br><h2> <font color="red">Revenir à la page d\'accueil</font> </h2><br>';
                    echo '<a href="index.php?id='.$_SESSION['id'].'" style="font-size: 28px;"> Par ici !!! </a>';
                    echo '</div>';
                }
            }
        }
        if (!isset($_GET['id'])) {
            echo '<div id="erreur">';
            echo '<br><h2> <font color="red">Veuillez vous connecter ou vous inscrire pour continuer</font> </h2><br>';
            echo '<a href="inscription.php"> Par ici !!! </a>';
            echo '</div>';
        }
        else if (!isset($_SESSION['id_chambre'])) {
            if ($userinfo['admin'] == 0) {
                echo '<div id="erreur2">';
                echo '<br><h2> <font color="red">Veuillez choisir une chambre avant de continuer</font> </h2><br>';
                echo '<a href="hebergement.php?id='.$_GET['id'].'"> Par ici !!! </a>';
            }
        }
        else {
    ?>

<div id="form">

<form role="form" class="bookform form-inline row" action="envoyer_formulaire_chambre.php?id=<?php echo $_SESSION['id'] ?>" method="POST">
    <br>
    

        <label> Chambre choisie : </label>
        <?php
            if (isset($_SESSION['id_chambre'])) {
                $id_chambre=$_SESSION['id_chambre'];
                $affiche = $pdo->query("SELECT * FROM chambre WHERE id =".$id_chambre."");
            }
            while ($chambre = $affiche->fetch()) {
                echo $chambre['type'];
        ?>
                <input type="hidden" name="id_chambre" value="<?php echo $_SESSION['id_chambre'] ?>" >
                <input type="hidden" name="disponnibilite" value="<?php echo $chambre['disponnibilite'] ?>" >
        <?php
        ?>
        <br><br>

        <label> Prix : </label> 

        <?php
            echo $chambre['prix'].' €';
        ?>
        <br><br>

        <div id="date">
            <label> Date de réservation : </label> 
            <br>
            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar bigger-110"></i></span>
                    <input class="form-control datepicker1" required type="text" name="date1" />
                </div>
            </div>
        </div>

        <?php
            }
            $affiche->closeCursor();
        ?>

    <br><br>
    <span></span>
    <input type="submit" onClick="return confirm('Voulez vous vraiment valider votre réservation ?');" name="submit" value="Confirmer">
    <?php 
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                    echo '<input type="reset" name="reset" value="Annuler" onclick=\'location.href="hebergement.php?id='.$_GET['id'].'"\'>';
            }
        }
        else {
            echo '<input type="reset" name="reset" value="Annuler" onclick=\'location.href="hebergement.php"\'>';
        }
    ?>
    
</form>
</div>
<?php
    }
?>




<footer>

    <div class="container">

        <div class="row">

            <div class="col-md-4">

                <h3>Termes d'utilisation</h3>

                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.

            </div>

            <div class="col-md-4 col-md-offset-4">

                <h3>Nous Contacter</h3>

                <address>


                    <span><strong>Tel:</strong>00 66 666 666 666</span>

                    <span><strong>Email:</strong><a href="mailto:ouchtitiwalid@gmail.com">ouchtitiwalid@gmail.com</a></span>

                </address>



                <div class="social-icons">

                    <a href="#">

                        <img src="../images/social-icons/facebook.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/twitter.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/dribbble.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/blogger.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/youtube.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/vimeo.png" alt=""></a>

                </div>

            </div>

        </div>

    </div>



    <div class="subfooter">

        <div class="container">

            <div class="row">

                <div class="col-md-6">

                    &copy; Siteweb développé par <span class="text-muted text-danger">Walid OUCHTITI et Mohammed LYAHYAOUI</span>

                </div>

            </div>

        </div>

    </div>



</footer>

<input type="hidden" min="0" max="1" value="0" id="inout">

<script src="../js/calendrier.js"></script>

</body>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>


