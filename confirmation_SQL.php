<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "shopcasquette";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$produits = [];
$prix_total = 0;
$insertion_effectuee = false;
$message_stock = "";
$promo_anniversaire = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $conn->real_escape_string($_POST["lastName"]);
    $prenom = $conn->real_escape_string($_POST["firstName"]);
    $telephone = $conn->real_escape_string($_POST["phoneNumber"]);

    // Vérifier si l'utilisateur est connecté et s'il a un anniversaire aujourd'hui
    if (isset($_SESSION['user_email'])) {
        $email = $conn->real_escape_string($_SESSION['user_email']);
        $result = $conn->query("SELECT date_naissance FROM utilisateurs WHERE email = '$email'");

        if ($result && $row = $result->fetch_assoc()) {
            $date_naissance = $row['date_naissance'];

            // Vérifier si aujourd'hui est l'anniversaire
            $today = date('m-d');
            $birthdate_today = date('m-d', strtotime($date_naissance));

            if ($today == $birthdate_today) {
                $promo_anniversaire = true;
                $message_stock .= "<br>🎉 Joyeux anniversaire ! Vous bénéficiez de 10% de réduction !<br>";
            }
        }
    }

    // Charger tous les produits et leur stock
    $result = $conn->query("SELECT p.id AS product_id, p.name, p.price, s.quantity 
                            FROM products p 
                            JOIN stock s ON p.id = s.product_id");

    $products_data = [];
    while ($row = $result->fetch_assoc()) {
        $products_data[$row['name']] = [
            'id' => $row['product_id'],
            'price' => $row['price'],
            'stock' => $row['quantity']
        ];
    }

    // Correspondance couleurs -> champs formulaire
    $form_mapping = [
        'Casquette Bleue' => 'blueCaps',
        'Casquette Rouge' => 'redCaps',
        'Casquette Verte' => 'greenCaps',
        'Casquette Jaune' => 'yellowCaps',
        'Casquette Noire' => 'blackCaps'
    ];

    $articles = [];

    foreach ($form_mapping as $product_name => $post_field) {
        $quantite = (int)($_POST[$post_field] ?? 0);

        if ($quantite > 0 && isset($products_data[$product_name])) {
            $product_info = $products_data[$product_name];
            $stock_dispo = $product_info['stock'];
            $prix_unitaire = $product_info['price'];
            $product_id = $product_info['id'];

            if ($quantite > $stock_dispo) {
                $message_stock .= "⚠️ Stock insuffisant pour $product_name. Stock disponible : $stock_dispo<br>";
                $quantite = $stock_dispo;
            }

            if ($quantite > 0) {
                $produits[] = "$quantite x $product_name à $prix_unitaire €";
                $prix_total += $quantite * $prix_unitaire;
                $articles[] = [$product_id, $product_name, $quantite, $prix_unitaire];
            }
        }
    }

    // Appliquer la promo d'anniversaire si nécessaire
    if ($promo_anniversaire) {
        $prix_total *= 0.9; // Appliquer la réduction de 10%
    }

    if (isset($_POST['confirm'])) {
        // Insérer client
        $query = "INSERT INTO clients (nom, prenom, telephone) 
                  VALUES ('$nom', '$prenom', '$telephone')";
        $conn->query($query);
        $client_id = $conn->insert_id;

        // Insérer commande
        $query = "INSERT INTO commandes (client_id, total) 
                  VALUES ($client_id, $prix_total)";
        $conn->query($query);
        $commande_id = $conn->insert_id;

        // Insérer articles commandés et mettre à jour stock
        foreach ($articles as [$product_id, $product_name, $quantite, $prix_unitaire]) {
            $query = "INSERT INTO articles_commandes (commande_id, quantite, prix) 
                      VALUES ($commande_id, $quantite, $prix_unitaire)";
            $conn->query($query);

            $conn->query("UPDATE stock SET quantity = quantity - $quantite WHERE product_id = $product_id");
        }

        $insertion_effectuee = true;
        header("refresh:3;url=index_SQL.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation d'Achat</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: auto; padding: 20px; }
        h1, h2 { text-align: center; }
        ul { list-style-type: none; padding: 0; }
        li { margin: 5px 0; }
        .total { font-weight: bold; text-align: center; margin-top: 20px; }
        .confirm { text-align: center; margin-top: 30px; }
        .message { color: red; font-weight: bold; text-align: center; margin-top: 20px; }
        button { padding: 10px 20px; font-size: 1em; }
    </style>
</head>
<body>
<h1>Confirmation de votre commande</h1>
<?php if ($insertion_effectuee): ?>
    <p style="text-align:center; color: green; font-weight: bold;">Merci ! Votre commande a bien été enregistrée.</p>
    <p style="text-align:center;">Redirection vers l'accueil dans 3 secondes...</p>
<?php else: ?>

    <?php if (!empty($message_stock)): ?>
        <div class="message"><?= $message_stock ?></div>
    <?php endif; ?>

    <h2>Produits sélectionnés :</h2>
    <ul>
        <?php foreach ($produits as $produit): ?>
            <li><?= htmlspecialchars($produit) ?></li>
        <?php endforeach; ?>
    </ul>

    <p class="total">Prix total : <?= number_format($prix_total, 2) ?> €</p>

    <form class="confirm" method="post">
        <input type="hidden" name="lastName" value="<?= htmlspecialchars($_POST['lastName']) ?>">
        <input type="hidden" name="firstName" value="<?= htmlspecialchars($_POST['firstName']) ?>">
        <input type="hidden" name="phoneNumber" value="<?= htmlspecialchars($_POST['phoneNumber']) ?>">
        <?php foreach (['blueCaps','redCaps','greenCaps','yellowCaps','blackCaps'] as $cap): ?>
            <input type="hidden" name="<?= $cap ?>" value="<?= (int)($_POST[$cap] ?? 0) ?>">
        <?php endforeach; ?>
        <input type="hidden" name="confirm" value="1">
        <button type="submit">Confirmer l'achat</button>
    </form>
<?php endif; ?>
</body>
</html>
