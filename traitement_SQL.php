<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST["nom"]);
    $prenom = htmlspecialchars($_POST["prenom"]);
    $email = htmlspecialchars($_POST["email"]);
    $adresse = htmlspecialchars($_POST["adresse"]);
    $produit = htmlspecialchars($_POST["produit"]);
    $prix_total = ($_POST["produit"] == "Casquette Rouge") ? 19.99 : 24.99;

    $dsn = "mysql:host=localhost;dbname=shopcasquette;charset=utf8";
    $username = "root";
    $password = "";

    try {
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $query = "INSERT INTO commandes (nom, prenom, email, adresse, produit, prix_total) VALUES (:nom, :prenom, :email, :adresse, :produit, :prix_total)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            "nom" => $nom,
            "prenom" => $prenom,
            "email" => $email,
            "adresse" => $adresse,
            "produit" => $produit,
            "prix_total" => $prix_total
        ]);
        
        echo "<h1>Merci pour votre commande, $prenom $nom!</h1>";
        echo "<p>Un email de confirmation a été envoyé à $email.</p>";
        echo "<p>Votre commande de $produit sera livrée à l'adresse suivante : $adresse.</p>";
        echo "<p><a href='historique_achats.html'>Voir votre historique d'achats</a></p>";
    } catch (PDOException $e) {
        echo "<p>Erreur lors du traitement de la commande : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Erreur dans la soumission du formulaire.</p>";
}
?>

