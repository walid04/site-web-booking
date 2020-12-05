<?php

session_start();

include 'connex.inc.php';
try {

if (isset($_SESSION['id'])) {
    $requser = $pdo->prepare("SELECT * FROM user WHERE id = ?");
    $requser->execute(array($_SESSION['id']));
    $user = $requser->fetch();

    if (isset($_POST['newnom']) AND !empty($_POST['newnom']) AND $_POST['newnom'] != $user['nom']) {
        $newnom = htmlspecialchars($_POST['newnom']);
        $insertnom = $pdo->prepare("UPDATE user SET nom = ? WHERE id = ?");
        $insertnom->execute(array($newnom, $_SESSION['id']));
        $msg = "Vos informations ont été modifié avec succès !";
    }

    if (isset($_POST['newprenom']) AND !empty($_POST['newprenom']) AND $_POST['newprenom'] != $user['prenom']) {
        $newprenom = htmlspecialchars($_POST['newprenom']);
        $insertnom = $pdo->prepare("UPDATE user SET prenom = ? WHERE id = ?");
        $insertnom->execute(array($newprenom, $_SESSION['id']));
        $msg = "Vos informations ont été modifié avec succès !";
    }

    if (isset($_POST['newemail']) AND !empty($_POST['newemail']) AND isset($_POST['confnewemail']) AND !empty($_POST['confnewemail']) AND $_POST['newemail'] != $user['mail']) {
        $email1 = htmlspecialchars($_POST['newemail']);
        $email2 = htmlspecialchars($_POST['confnewemail']);
        if ($email1 == $email2) {
            if (filter_var($email1, FILTER_VALIDATE_EMAIL)) {
                $reqmail = $pdo->prepare("SELECT * FROM user WHERE mail = ?");
                $reqmail->execute(array($email1));
                $nbmail = $reqmail->rowCount();
                if($nbmail == 0) {
                    $insertemail = $pdo->prepare("UPDATE user SET email = ? WHERE id = ?");
                    $insertemail->execute(array($email1, $_SESSION['id']));
                    $msg = "Vos informations ont été modifié avec succès !";    
                }
                else {
                    $erreur = "L'adresse mail existe déjà !";
                }
            }
            else {
                $erreur = "Votre adresse mail n'est pas valide !";
            }
        }
        else {
            $erreur = "Vos deux emails ne correspondent pas !";
        }
    }

    if (isset($_POST['newmdp']) AND !empty($_POST['newmdp']) AND isset($_POST['confnewmdp']) AND !empty($_POST['confnewmdp']) AND isset($_POST['ancienmdp']) AND !empty($_POST['ancienmdp'])) {
        $ancien = sha1($_POST['ancienmdp']);
        $mdp1 = sha1($_POST['newmdp']);
        $mdp2 = sha1($_POST['confnewmdp']);
        if (strcmp($ancien, $user['mdp']) == 0) {
            if ($mdp1 == $mdp2) {
                $insertmdp = $pdo->prepare("UPDATE user SET mdp = ? WHERE id = ?");
                $insertmdp->execute(array($mdp1, $_SESSION['id']));
                $msg = "Votre mot de passe a été modifié avec succès !";            }
            else {
                $erreur = "Vos deux mots de passe ne correspondent pas !";
            }
        }
        else {
            $erreur = "Le mot de passe saisie ne correspont pas à votre mot de passe actuel !";
        }
    }

    if (isset($_POST['newname']) AND $_POST['newname'] == $user['name']) {
        header('LOCATION:index.php?id='.$_SESSION['id']);
    }
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Projet Web</title>

    <link rel="icon shortcut" href="../images/favicon.png">

    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <link href="../css/inscription.css" rel="stylesheet" type="text/css">

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
            ?>

                <?php echo '<li><a href="index.php?id='.$_GET['id'].'">Accueil</a></li>'; ?>

                <li><a href="<?php echo 'hebergement.php?id='.$_GET['id'].''; ?>">Hebergement</a></li>

                <li><a href="<?php echo 'contact.php?id='.$_GET['id'].''; ?>">Contact</a></li>

                <li><a href="<?php echo 'transport.php?id='.$_GET['id'].''; ?>">Transport</a></li>

            <?php
            }
            ?>

            <?php 
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

<div>

    <div id="inscription">
        <br>
        <h2> Edition de mon profil </h2>
        <br>
        <form class="bookform form-inline row" action="" method="POST">
            <table>
                <tr>
                    <td>
                        <label for="nom"> Nom :  </label>
                    </td>
                    <td>
                        <input id="newnom" type="text" name="newnom" value="<?php echo $user['nom'] ?>" required><br><br>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="prenom"> Prenom : </label> 
                    </td>
                    <td>
                        <input id="newprenom" type="text" name="newprenom" value="<?php echo $user['prenom'] ?>" required><br><br>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="email"> E-mail : </label> 
                    </td>
                    <td>
                        <input id="newemail" type="email" name="newemail" value="<?php echo $user['mail'] ?>" required><br><br>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="email_conf"> Confirmer votre e-mail : </label>
                    </td>
                    <td>
                        <input id="confnewemail" type="email" name="confnewemail" value="<?php echo $user['mail'] ?>" required><br><br>
                    </td>
                </tr>

            </table>

            <span></span>
            <input style="margin-left : 140px;" id="valider1" name="valider" type="submit" value="Valider" >

            <br><br>
        </form>

    </div>

    <div id="modifier">
        <br>
        <h2> Modification du mot de passe </h2>
        <br>
        <form class="bookform form-inline row" action="" method="POST">
            <table>

                <tr>
                    <td>
                        <label for="mdp"> Votre mot de passe actuel : </label> 
                    </td>
                    <td>
                        <input id="confnewmdp" type="password" name="ancienmdp" required><br><br>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="mdp"> Nouveau mot de passe : </label> 
                    </td>
                    <td>
                        <input id="confnewmdp" type="password" name="newmdp" required><br><br>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="mdp_conf"> Confirmer votre nouveau mot de passe : </label> 
                    </td>
                    <td>
                        <input id="newmdp" type="password" name="confnewmdp" required><br><br>
                    </td>
                </tr>
            </table>

            <span></span>
            <input style="margin-left : 190px;" id="valider" name="valider" type="submit" value="Valider" >

            <br><br>

        </form>

    </div>

    <?php
        if (isset($erreur)) {
            echo '<p id="erreur">'.$erreur.'</p>';
        }
        if (isset($msg)) {
            echo '<p id="message">'.$msg.'</p>';
        }
    ?>
</div>


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
else {
    header('LOCATION:index.php');
}
?>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>
