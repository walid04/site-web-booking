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
            header('LOCATION:index.php');
        else if ($userinfo['id'] != $_SESSION['id'])
            header('LOCATION:reservations.php?id='.$_SESSION['id']);
    }
    else if (isset($_SESSION['id'])) {
        header('LOCATION:reservations.php?id='.$_SESSION['id']);
    }

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Projet Web</title>

    <link rel="icon shortcut" href="../images/favicon.png">

    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <link href="../css/utilisateurs.css" rel="stylesheet" type="text/css">

</head>

<body>

<header>

    <div id="logo">

        <div class="inner">

            <a href="index.php">

                <img src="../images/favicon.png" alt="logo">

            </a>

        </div>

        <div id="profil">
            <?php 
            if (isset($_GET['id']) AND $_GET['id'] > 0) {
                $_SESSION['id']=$_GET['id'];
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
    if (isset($_GET['id']) AND $_GET['id'] == 'nv') {
    ?>
        <br><h2 align="center"> <font color="red">Connectez-vous pour continuer</font> </h2></font><br>
    <?php
    }
?>

<div id="pere">

    <div id="utilisateurs" style="text-align : center;">
        <br>

        <?php
        $val=0;
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) {
                    $insert = $pdo->prepare("SELECT * FROM reservation WHERE id_client = ?");
                    $insert->execute(array($_GET['id']));
                }
                if ($userinfo['admin'] == 1) {
                    $insert = $pdo->prepare("SELECT * FROM reservation");
                    $insert->execute();
                }
                    while ($reservation = $insert->fetch()) {
                        if ($insert->rowCount() > 0 AND $reservation['type_article'] == 'chambre') {
                            $requete = $pdo->prepare("SELECT * FROM chambre WHERE id = ?");
                            $requete->execute(array($reservation['id_article']));
                            while ($chambre = $requete->fetch()) {
                                $val++;
                                if ($val == 1) {
                                    if ($userinfo['admin'] == 0) {
                                        $nb = $pdo->prepare("SELECT * FROM reservation WHERE type_article = ? AND id_client = ?");
                                        $nb->execute(array('chambre', $_GET['id']));
                                        echo '<h2> Chambres - ('.$nb->rowCount().') </h2>';
                                    }
                                    if ($userinfo['admin'] == 1) {
                                        $nb = $pdo->prepare("SELECT * FROM reservation WHERE type_article = ?");
                                        $nb->execute(array('chambre'));
                                        echo '<h2> Chambres - ('.$nb->rowCount().') </h2>';
                                    }
                                }

        ?>

                <br>
                <label for="nom"> Type :  </label>
                <?php echo $chambre['type'] ?>
                <br><br>
                
                <label for="prenom"> Localisation : </label> 
                <?php echo $chambre['localisation'] ?>
                <br><br>
                
                <label for="email"> Prix : </label>  
                <?php echo $chambre['prix'] ?> €
                <br><br>

                <?php 
                    if ($userinfo['admin'] == 1) {
                        $requete = $pdo->prepare("SELECT * FROM user WHERE id = ?");
                        $requete->execute(array($reservation['id_client']));
                        while ($client = $requete->fetch()) {
                ?>
                
                <label for="email"> Client : </label>  
                <?php echo $client['prenom'].' '.$client['nom'] ?> 
                <br><br>

                <label for="email"> E-mail : </label>  
                <?php echo $client['mail']?> 
                <br><br>

                <?php
                        }
                    }
                ?>

        <?php
                                if ($insert->rowCount() > 1) {
                                    echo '<hr>';
                                }
                            }
                            $requete->closeCursor();
                        }
                    }
                    $insert->closeCursor();
            }
        }
        ?>
    </div>

    <div id="admin" style="text-align : center; ">
        <br>

        <?php
        $val=0;
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) {
                    $insert = $pdo->prepare("SELECT * FROM reservation WHERE id_client = ?");
                    $insert->execute(array($_GET['id']));
                }
                if ($userinfo['admin'] == 1) {
                    $insert = $pdo->prepare("SELECT * FROM reservation");
                    $insert->execute();
                }
                    while ($reservation = $insert->fetch()) {
                        if ($insert->rowCount() > 0 AND $reservation['type_article'] == 'voiture') {
                            $requete = $pdo->prepare("SELECT * FROM voiture WHERE id = ?");
                            $requete->execute(array($reservation['id_article']));
                            while ($voiture = $requete->fetch()) {
                                $val++;
                                if ($val == 1) {
                                    if ($userinfo['admin'] == 0) {
                                        $nb = $pdo->prepare("SELECT * FROM reservation WHERE type_article = ? AND id_client = ?");
                                        $nb->execute(array('voiture', $_GET['id']));
                                        echo '<h2> Voitures - ('.$nb->rowCount().') </h2>';
                                    }
                                    if ($userinfo['admin'] == 1) {
                                        $nb = $pdo->prepare("SELECT * FROM reservation WHERE type_article = ?");
                                        $nb->execute(array('voiture'));
                                        echo '<h2> Voitures - ('.$nb->rowCount().') </h2>';
                                    }
                                }
        ?>

                <br>
                <label for="nom"> Type :  </label>
                <?php echo $voiture['type'] ?>
                <br><br>
                
                <label for="prenom"> Voiture : </label> 
                <?php echo $voiture['marque'].' '.$voiture['modele'] ?>
                <br><br>
                
                <label for="email"> Prix : </label>  
                <?php echo $voiture['prix'] ?> €
                <br><br>

                <?php 
                    if ($userinfo['admin'] == 1) {
                        $requete = $pdo->prepare("SELECT * FROM user WHERE id = ?");
                        $requete->execute(array($reservation['id_client']));
                        while ($client = $requete->fetch()) {
                ?>
                
                <label for="email"> Client : </label>  
                <?php echo $client['prenom'].' '.$client['nom'] ?> 
                <br><br>

                <label for="email"> E-mail : </label>  
                <?php echo $client['mail']?> 
                <br><br>

                <?php
                        }
                    }
                ?>

        <?php
                                if ($insert->rowCount() > 1) {
                                    echo '<hr>';
                                }
                            }
                            $requete->closeCursor();
                        }
                    }
                    $insert->closeCursor();
            }
        }
        ?>
    </div>

    <div id="transfert" style="float : right; text-align: center;margin-right : 70px;">
        <br>

        <?php
        $val=0;
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) {
                    $insert = $pdo->prepare("SELECT * FROM transfert_aeroport WHERE id_client = ?");
                    $insert->execute(array($_GET['id']));
                }
                if ($userinfo['admin'] == 1) {
                    $insert = $pdo->prepare("SELECT * FROM transfert_aeroport");
                    $insert->execute();
                }
                    while ($transfert_aeroport = $insert->fetch()) {
                        if ($insert->rowCount() > 0) {
                            $val++;
                            if ($val == 1) {
                                if ($userinfo['admin'] == 0) {
                                    $nb = $pdo->prepare("SELECT * FROM transfert_aeroport WHERE id_client = ?");
                                    $nb->execute(array($_GET['id']));
                                    echo '<h2> Transfert aéroport - ('.$nb->rowCount().') </h2>';
                                }
                                if ($userinfo['admin'] == 1) {
                                    $nb = $pdo->prepare("SELECT * FROM transfert_aeroport");
                                    $nb->execute();
                                    echo '<h2> Transfert aéroport - ('.$nb->rowCount().') </h2>';
                                }
                            }
        ?>

                <br>
                <label for="nom"> Date de réservation :  </label>
                <?php echo $transfert_aeroport['date_reservation'] ?>
                <br><br>
                
                <label for="prenom"> Heure de réservation : </label> 
                <?php echo $transfert_aeroport['heure_reservation'] ?>
                <br><br>
                
                <label for="email"> Langue parlée : </label>  
                <?php echo $transfert_aeroport['langue'] ?> 
                <br><br>

                <?php 
                    if ($userinfo['admin'] == 1) {
                        $requete = $pdo->prepare("SELECT * FROM user WHERE id = ?");
                        $requete->execute(array($transfert_aeroport['id_client']));
                        while ($client = $requete->fetch()) {
                ?>
                
                <label for="email"> Client : </label>  
                <?php echo $client['prenom'].' '.$client['nom'] ?> 
                <br><br>

                <label for="email"> E-mail : </label>  
                <?php echo $client['mail']?> 
                <br><br>

                <?php
                        }
                    }
                ?>

        <?php
                            if ($insert->rowCount() >= 1) {
                                        echo '<hr style="width: 15%;">';
                            }
                        }
                    }
                    $insert->closeCursor();
                }
            }
        ?>
    </div>
</div>

<div class="clearfix"></div>

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

</body>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>

