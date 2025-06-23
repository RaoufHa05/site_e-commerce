<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=shopcasquette;charset=utf8", "root", "");
// Suppression de l'utilisateur par email
$email = $_SESSION['user_email'];
$stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header("Location: index_SQL.php");
exit();
?>
