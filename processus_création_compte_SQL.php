<?php
session_start();

// Connexion BDD
try {
    $pdo = new PDO("mysql:host=localhost;dbname=shopcasquette;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération
$nom = htmlspecialchars($_POST['nom']);
$prenom = htmlspecialchars($_POST['prenom']);
$date_naissance = $_POST['date_naissance'];
$adresse = htmlspecialchars($_POST['adresse']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$mdp = $_POST['mot_de_passe'];
$mdp2 = $_POST['confirm_mot_de_passe'];

// Vérification
if ($mdp !== $mdp2) {
    die("Les mots de passe ne correspondent pas.");
}

// Vérifier doublon email
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
    die("Un compte avec cet email existe déjà.");
}

// Hash du mot de passe
$mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

// Insérer nouvel utilisateur avec rôle 'user' par défaut
$stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, date_naissance, adresse, email, mot_de_passe, role)
VALUES (?, ?, ?, ?, ?, ?, 'user')");
$stmt->execute([$nom, $prenom, $date_naissance, $adresse, $email, $mdp_hash]);

// Stockage en session
$_SESSION['user_nom'] = $nom;
$_SESSION['user_prenom'] = $prenom;
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = 'user'; // ou admin, selon ce qu'on récupère


// Redirection vers l'accueil
header("Location: index_SQL.php");
exit();
?>
