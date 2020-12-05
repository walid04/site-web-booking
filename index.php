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
            header('LOCATION:index.php?id='.$_SESSION['id']);
    }
    else if (isset($_SESSION['id'])) {
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

    <link href="../css/index.css" rel="stylesheet" type="text/css">

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

                <li><a href="#">Accueil</a></li>

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

<div id="slider" class="hidden-xs">

    <div class="callbacks_container">

        <ul class="rslides pic_slider">

            <li>

                <div class="text-wrap">

                    <div class="inner">

                        <div class="mid">

                            <h1>Pourquoi choisir un riad <br> pour votre sejour ?</h1>

                        </div>

                    </div>

                </div>

                <img src="../images/slider-home/8.jpg" alt="">

            </li>

        </ul>

    </div>

</div>

<div class="clearfix"></div>

<div class="hidden-sm" id="titre_mobile">

    <div class="row">

        <div class="col-sm-12 text-center">

            <h1>Pourquoi choisir un riad <br> pour votre sejour ?</h1>

        </div>

    </div>

</div>

<div class="container-fluid">

    <div id="wild-beauty">

        <section id="themo_html_3" class="content-editor">

            <div class="container">

                <div class="row">

                    <div class="col-md-12">

                        <div class="row">

                            <div class="col-md-6">

                                <h3>Pourquoi choisir un riad pour votre sejour ?</h3>

                                <p>

                                    Le Riad, un des joyaux de la ville ochre dont son originalité remonte à plusieurs centaines d’années. L’architecture, la décoration et l’organisation de ce patrimoine est un héritage de la civilisation Berbère ; les premiers peuples marocains. La construction traditionnelle du Riad à Marrakech est inspirée des anciennes forteresses des Berbères dont la matière de base été en pierre ou en adobe. </p>

                                <p>

                                    Cette diversité de construction des Riads est due aux différentes civilisations qui se sont succédées au Maroc et qui ont marqué sont histoire. Suite à l’arrivée du peuple maghrébin au cours du Xème et XIème siècle, les Riads à Marrakech ont connus une extension au niveau de leur patio intérieur appelé West- ed-dar.
                                    <div class="small-border"></div>

                            </div>

                            <div class="col-md-6 text-center">

                                <img class="img img-responsive img-thumbnail" src="../images/articles/01.jpg">

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">

                        <div class="row">

                            <div class="col-md-6 text-center">

                                <img class="img img-responsive img-thumbnail" src="../images/articles/02.jpg">

                            </div>

                            <div class="col-md-6">

                                <p>

                                    Puis vient les almohades avec leur amour à la verdure, la beauté et une certaine inspiration de l’art andalou, chose qui a donné un brassage entre les jardins et les espaces verts immenses à l’intérieur des Riads. </p>

                                <p>

                                    Si on fait un flashback dans l’histoire des Riads, leurs première apparition été durant le règne Saâdien et plus précisément lors de l’époque d’Ahmed El Mansour. Ce dernier qui a ordonné a la construction du chef d’œuvre palais AL BADII, avec ses colonnes en marbre, ses tours, ses terrasses, ses fontaines aux jets d’eau dans de vasques en marbres et ses divers plantations. Ce même style de construction été adopté par les richards de l’époque afin d’établir leurs propre demeures. </p>
                                    <div class="small-border"></div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">

                        <div class="row">

                            <div class="col-md-6">

                                <p>

                                    La ville de Marrakech a connu un étendu et un développement dans les constructions des Riads tout en gardant l’authenticité des populations d’origine et cela durant l’époque des alaouites. Est c’est grâce à la passion de l’art et la beauté arabo-andalou Mauresque que la construction des ces merveilleux Riads s’est poursuivis. Il est digne de dire que les Riads de Marrakech sont des témoignes puissants d’un passé glorieux qui a contribué sans doute à forger le Maroc tel que nous le connaissons aujourd’hui. </p>

                                <p>

                                    Tout Marocain doit être fier du trésor qu’il possède : des Riads uniques ont leurs genres, ces anciennes demeures qui cachent une beauté extraordinaire derrières ses mures austères de la médina. Un vrai paradis oriental. </p>
                                    <div class="small-border"></div>

                            </div>

                            <div class="col-md-6 text-center">

                                <img class="img img-responsive img-thumbnail" src="../images/articles/03.jpg">

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">

                        <div class="row">

                            <div class="col-md-6 text-center">

                                <img class="img img-responsive img-thumbnail" src="../images/articles/04.jpg">

                            </div>

                            <div class="col-md-6">

                                <p>

                                    Chaque Riad à son propre plan et dispositions qui sont bien codifiés, c’est un espace qui porte sur l’intériorisation : des façades intérieures bien entretenus et considérées comme façades principales, quant a celles de l’extérieur, se sont des murs sans traits et qui révèlent de la curiosité afin de deviner l’unique richesse décorative des ses espaces intérieurs. </p>

                                <p>

                                    S’est en ouvrant la grande porte faite de l’ultime bois sur lequel des dessins inédits figures ainsi que la main de Fatima mise au centre de la porte qui serre comme sonnette, qu’on se trouve dans la chicane (Aagoummi) : un des premiers passages qui protège l’intimité du Riad. C’est un couloir dans lequel le chef du Riad reçoit ses amis sans gêner la vie privée de la femme au sein de sa demeure. </p>
                                    <div class="small-border"></div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">

                        <div class="row">

                            <div class="col-md-6">

                                <p>

                                    (West-ed-dar) la cour ; est le centre du Riad qui peut avoir l’une des trois formes : rectangulaire, carrée ou normal. La plus part du temps, une magnifique fontaine est au centre du Riad faite de marbre ou zellige et tout autour un beau jardin verdoyant. Ce jardin est constitué de différentes sortes d’arbres, le seul principe c’est d’avoir une profusion de plantes médicinales et aromatiques ainsi que des fruitiers tout comme des orangers ou mandariniers sans oublier le sigle de la ville ocre Marrakech qui est le palmier, ce dernier qui garantie une ombre et fraicheur durant la chaleur de l’été. </p>

                                <p>

                                    Les pièces d’habitation d’un Riad ont un charme spécial. Elles se positionnent tout autour du jardin, et chaque pièce à sa porte principale entouré de deux fenêtres qui éclairent la pièce. Les pièces d’habitation sont peu profondes mais très larges. </p>
                                    <div class="small-border"></div>

                            </div>

                            <div class="col-md-6 text-center">

                                <img class="img img-responsive img-thumbnail" src="../images/articles/05.jpg">

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">

                        <div class="row">

                            <div class="col-md-6 text-center">

                                <img class="img img-responsive img-thumbnail" src="../images/articles/06.jpg">

                            </div>

                            <div class="col-md-6">

                                <p>

                                    Les Riads de Marrakech sont construits sur un seul étage et très rarement en deux étages. Parfois on y trouve une petite maisonnette dont l’accès est séparé du Riad (douiria). Enfin, touts les Riads sont dominés par une terrasse, un espace protégé ou les femmes peuvent se rencontrer.

                                    De nos jours, les Riads sont devenus un Must à avoir et non pas qu’une simple demeure. </p>

                                <p>

                                    Qui dit vacances, dit un hôtel de luxe, mais d’après tout ce qu’on vient de vous raconter, une envie intense va vous poussez afin de passer vos vacances dans un palais authentique au cœur de la médina, un endroit on l’on joint l’utile à l’agréable. </p>
                                    <div class="small-border"></div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="clearfix"></div>

        </section>

    </div>

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

</body>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>
