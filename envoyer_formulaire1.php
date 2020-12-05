<?php

session_start();

include 'connex.inc.php';

try {

    if (isset($_POST['submit'])) {
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            $getid = intval($_GET['id']);
            $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
            $requser->execute(array($getid));
            $userinfo = $requser->fetch();
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) {

                        $affiche = $pdo->prepare("UPDATE voiture set disponnibilite = ? WHERE id = ?");
                        $affiche->execute(array($_POST['disponnibilite']-1,$_POST['id_voiture']));
                        $insert = $pdo->prepare("INSERT INTO reservation (id_client, id_article, type_article, date_reservation) VALUES (?, ?, ?, ?)");
                        $insert->execute(array($_GET['id'], $_POST['id_voiture'], 'voiture', $_POST['date1']));
                        $_SESSION['page']=1;
                        $id_voiture=$_SESSION['id_voiture'];
                        $affiche = $pdo->prepare("SELECT * FROM voiture WHERE id = '".$_SESSION['id_voiture']."'");
                        $affiche->execute();
                        $location = $affiche->fetch();

                        $email_to = "ouchtitiwalid@gmail.com";
                        $email_subject = "Location voiture";
                        $email_message = "L'utilisateur ".$userinfo['prenom']." ".$userinfo['nom']." vient de réserver une voiture"."\n \n".
            		        "Voiture : ".$location['marque']." ".$location['modele']."\n".
                    		"Type : ".$location['type']."\n".
                            "Prix : ".$location['prix']."\n".
                    		"Date de réservation : ".$_POST['date1']."\n".
                    		"Adresse e-mail du client : ".$userinfo['mail']."\n";
                        @mail($email_to, $email_subject, $email_message);

                        $email_to = $userinfo['mail'];
                        $email_subject = "Confirmation de réservation";
                        $email_message = "Votre réservation a été bien effectuée"."\n \n".
                    		"Voiture : ".$location['marque']." ".$location['modele']."\n".
                    		"Type : ".$location['type']."\n".
                    		"Date de réservation : ".$_POST['date1']."\n";
                        @mail($email_to, $email_subject, $email_message);

                        header('LOCATION:operation_reussi.php?id='.$_SESSION['id']);
                }
            }
        }
    }
    else {
        if (isset($_GET['id']) AND $_GET['id'] > 0 AND isset($_SESSION['id'])) {
            if ($userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) 
                    header('LOCATION:index.php?id='.$_SESSION['id']);
                else 
                    header('LOCATION:index.php?id='.$_SESSION['id']);
            }
            else 
                header('LOCATION:index.php?id='.$_SESSION['id']);
        }
        else 
            header('LOCATION:index.php');
    }

}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}

?>
