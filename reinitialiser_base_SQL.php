<?php
session_start();

// Vérification que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_SQL.php');
    exit();
}

// Connexion BDD
$host = "localhost";
$user = "root";
$password = "";
$database = "shopcasquette";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Réinitialisation de la base de données
$conn->query("DELETE FROM articles_commandes");
$conn->query("DELETE FROM commandes");
$conn->query("DELETE FROM clients");
$conn->query("UPDATE stock SET quantity = 50");

// Supprimer seulement les utilisateurs qui ne sont PAS admins
$conn->query("DELETE FROM utilisateurs WHERE role = 'user'");

$conn->close();

// Rediriger proprement vers Admin avec un message
header('Location: admin_SQL.php?reset=success');
exit();
?>
