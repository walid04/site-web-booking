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
            header('LOCATION:inscription.php');
        else if ($userinfo['id'] != $_SESSION['id'])
            header('LOCATION:index.php?id='.$_SESSION['id']);
        else if ($userinfo['id'] == $_SESSION['id'])
            header('LOCATION:index.php?id='.$_SESSION['id']);
    }

/* ------------ INSCRIPTION ------------ */

if(isset($_POST['valider'])) {

    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $email_conf = htmlspecialchars($_POST['email_conf']);
    $mdp = sha1($_POST['mdp']);
    $mdp_conf = sha1($_POST['mdp_conf']);

    if (!empty($_POST['nom']) AND !empty($_POST['prenom']) AND !empty($_POST['email']) AND !empty($_POST['email_conf']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp_conf'])) {

        $nomlength = strlen($nom);
            if ($nomlength <= 50) {
                if ($email == $email_conf) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        
                        $reqmail = $pdo->prepare("SELECT * FROM user WHERE mail = ?");
                        $reqmail->execute(array($email));
                        $nbmail = $reqmail->rowCount();
                        if($nbmail == 0) {

                            if ($mdp == $mdp_conf) {
                                $inserer = $pdo->prepare("INSERT INTO user (nom, prenom, mail, mdp, admin) VALUES (?, ?, ?, ?, 0)");
                                $inserer->execute(array($nom, $prenom, $email, $mdp));
                                $message = "Votre compte a bien été créé !";
                                //pour rediriger sur une autre page
                                //$_SESSION['comptecree'] = "Votre compte a bien été crée !"
                                //header('Location: index.php');
                            }
                            else {
                                $erreur = "Vos mots de passe ne correspondent pas !";
                            }

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
                    $erreur = "Vos adresse email ne correspondent pas !";
                }
            }
            else {
                $erreur = "Votre nom ne doit pas depasser 50 caractères !";
            }
    }
}

/* ---------- CONNEXION -------- */

if (isset($_POST['submitconnection'])) {
    $mailconnect = htmlspecialchars($_POST['mailconnect']);
    $mdpconnect = sha1($_POST['mdpconnect']);
    if (!empty($mailconnect) AND !empty($mdpconnect)) {
        $requser = $pdo->prepare("SELECT * FROM user WHERE mail = ? AND mdp = ?");
        $requser->execute(array($mailconnect, $mdpconnect));
        $userexiste = $requser->rowCount();
        if ($userexiste == 1) {
            $userinfo = $requser->fetch();
            $_SESSION['id'] = $userinfo['id'];
            $_SESSION['nom'] = $userinfo['nom'];
            $_SESSION['prenom'] = $userinfo['prenom'];
            $_SESSION['mail'] = $userinfo['mail'];
            header("Location: index.php?id=".$_SESSION['id']);
        }
        else {
            $erreur = "Mauvais mail ou mot de passe !";
        }
    }
    else {
        $erreur = "Veuilez renseignez tous les champs !";
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
                        $nbreservation = $reqreservetion->rowCount();

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
                        $nbreservation = $reqreservetion->rowCount();

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

<?php 
    if (isset($_GET['id']) AND $_GET['id'] == 'nv') {
    ?>
        <br><h2 align="center"> <font color="red">Connectez-vous pour continuer</font> </h2></font><br>
    <?php
    }
?>

<div id="inscription">
    <br>
    
    <h2 id="titre1"> Pas encore inscris ? </h2>

    <h2 id="titre2"> <font color="#EDB83A">Inscription</font>  </h2>
    <form class="bookform form-inline row" action="" method="POST">
        <br>
        <table>
            <tr>
                <td>
                    <label for="nom"> Nom :  </label>
                </td>
                <td>
                    <input id="nom" type="text" name="nom" required value="<?php if (isset($nom)) { echo $nom; } ?>"><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="prenom"> Prenom : </label> 
                </td>
                <td>
                    <input id="prenom" type="text" name="prenom" required value="<?php if (isset($prenom)) { echo $prenom; } ?>"><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="email"> E-mail : </label> 
                </td>
                <td>
                    <input id="email" type="email" name="email" required value="<?php if (isset($email)) { echo $email; } ?>"><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="email_conf"> Confirmer votre e-mail : </label>
                </td>
                <td>
                    <input id="email_conf" type="email" name="email_conf" required value="<?php if (isset($email_conf)) { echo $email_conf; } ?>"><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="mdp"> Mot de passe </label> 
                </td>
                <td>
                    <input id="mdp" type="password" name="mdp" required><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="mdp_conf"> Confirmer votre mot de passe : </label> 
                </td>
                <td>
                    <input id="mdp_conf" type="password" name="mdp_conf" required><br><br>
                </td>
            </tr>
        </table>
        

        <span></span>
        <input name="valider" type="submit" value="Je m'inscris">

        <br><br>

    </form>
</div>

<div id="connexion">

    <h2 id="titre3"> Vous avez déjà un compte ? </h2>

    <h2 id="titre4"> <font color="#EDB83A">Connexion</font></h2>
    <form class="bookform form-inline row" method="POST" action="">
        <br>
        <table>
            <tr>
                <td>
                    <label for="mailconnect"> E-mail :  </label>
                </td>
                <td>
                    <input id="mailconnect" type="email" name="mailconnect" required value="<?php if (isset($mailconnect)) { echo $mailconnect; } ?>"><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="mdpconnect"> Mot de passe : </label> 
                </td>
                <td>
                    <input id="mdpconnect" type="password" name="mdpconnect" required value="<?php if (isset($mdpconnect)) { echo $mdpconnect; } ?>"><br><br>
                </td>
            </tr>
        </table>
        <span></span>
        <input type="submit" name="submitconnection" value="Se connecter">
    </form> 
</div>

    <?php
        if (isset($erreur)) {
            echo '<p id="erreur1">'.$erreur.'</p>';
        }
        if (isset($message)) {
            echo '<p id="message">'.$message.'</p>';
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
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>

