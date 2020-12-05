<?php
		try {
			$pdo = new PDO ('mysql:host=localhost;dbname=projetweb;charset=UTF8','root', '');
			$pdo->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->exec('SET NAMES utf8');
		}
		catch (PDOException $e) {
			echo 'Problème à la connexion';
			die();
		}
		return $pdo;
?>
