<?php

session_start();

include 'connex.inc.php';
try {

    if (isset ($_POST['id_voiture'])) {

        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            $getid = intval($_GET['id']);
            $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
            $requser->execute(array($getid));
            $userinfo = $requser->fetch();
            if (!isset($_SESSION['id'])) 
                header('LOCATION:index.php');
            else if ($userinfo['id'] != $_SESSION['id'])
                header('LOCATION:operation_reussi.php?id='.$_SESSION['id']);
        }
        else if (isset($_SESSION['id'])) {
            header('LOCATION:operation_reussi.php?id='.$_SESSION['id']);
        }

        $test = $pdo->query("SELECT id FROM voiture");
        while ($query = $test->fetch()) {
            $voiture=$query['id'];
            if (isset ($_POST['reserver'.$voiture])) {
                $_SESSION['id_voiture']=$voiture;
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
            $getid = intval($_GET['id']);
            $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
            $requser->execute(array($getid));
            $userinfo = $requser->fetch();

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
        if (isset($_GET['id']) && isset($_SESSION['page'])) {
            echo '<div id="erreur">';
            echo '<br><h2> <font color="green">Votre réservation a été effectuée avec succès</font> </h2><br>';
            echo '<h4>Vous pouvez consulter vos réservations en cliquant</h4> <a href="reservations.php?id='.$_GET['id'].'" style="font-size: 28px;"> ici </a> <h4> ou retourner à la page d\'accueil en cliquant </h4><a href="index.php?id='.$_GET['id'].'" style="font-size: 28px;"> ici </a>';
            echo '</div>';
            /*
            $header="MIME-Version: 1.0\r\n";
            $header.='From:"Walid OUCHTITI et Mohammed LYAHYAOUI"<ouchtitiwalid@gmail.com>'."\n";
            $header.='Content-Type:text/html; charset=utf-8"'."\n";
            $header.='Content-Transfer-Encoding: 8bit';

            $message='

            ';

            mail ("ouchtitiwalid@gmail.com", "Confirmation", $message, $header);
            */
        }
        if (! isset($_SESSION['page']) AND isset($_GET['id'])) {
            header('LOCATION:index.php?id='.$_GET['id']);
        }
        if (! isset($_SESSION['page']) AND ! isset($_GET['id'])) {
            header('LOCATION:index.php');
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

</body>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}

?>

</html>


