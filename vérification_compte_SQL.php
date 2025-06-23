<?php
session_start();


$pdo = new PDO("mysql:host=localhost;dbname=shopcasquette;charset=utf8", "root", "");


// Récupération des données du formulaire
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$mot_de_passe = $_POST['mot_de_passe'];

// Vérification si l'utilisateur existe
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    // Vérification du mot de passe
    if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // Connexion réussie → on stocke toutes les infos importantes
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role']; // très important pour l'accès admin

        // Redirection vers la page d'accueil
        header("Location: index_SQL.php");
        exit();
    } else {
        // Mauvais mot de passe
        echo "Mot de passe incorrect.";
    }
} else {
    // Email non trouvé
    echo "Aucun compte trouvé avec cet email.";
}
?>

