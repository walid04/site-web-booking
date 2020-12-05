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

                        $insert = $pdo->prepare("INSERT INTO transfert_aeroport (id_client, date_reservation, heure_reservation, langue) VALUES (?, ?, ?, ?)");
                        $insert->execute(array($_GET['id'], $_POST['date'], $_POST['heure'], $_POST['langue']));
                        $_SESSION['page']=1;

                        $email_to = "ouchtitiwalid@gmail.com";
                        $email_subject = "Transfert aéroport";
                        $email_message = "L'utilisateur ".$userinfo['prenom']." ".$userinfo['nom']." vient de réserver un transfert aéroport"."\n \n".
                            "Date de réservation : ".$_POST['date']."\n".
                            "Heure de réservation : ".$_POST['heure']."\n".
                            "Langue : ".$_POST['langue']."\n".
                            "Adresse e-mail du client : ".$userinfo['mail']."\n";
                        @mail($email_to, $email_subject, $email_message);

                        $email_to = $userinfo['mail'];
                        $email_subject = "Confirmation de réservation";
                        $email_message = "Votre réservation a été bien effectuée"."\n \n".
                            "Date de réservation : ".$_POST['date']."\n".
                            "Heure de réservation : ".$_POST['heure']."\n".
                            "Langue : ".$_POST['langue']."\n";
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
