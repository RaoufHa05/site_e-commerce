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

// Vérifier si le formulaire a été soumis
if (isset($_POST['submitOrder'])) {
    // Si l'utilisateur est connecté, on utilise les infos de session
    if (isset($_SESSION['user_nom'], $_SESSION['user_prenom'], $_SESSION['user_email'])) {
        $nom = $conn->real_escape_string($_SESSION['user_nom']);
        $prenom = $conn->real_escape_string($_SESSION['user_prenom']);
        $telephone = $conn->real_escape_string($_SESSION['user_email']); // on met l'email dans téléphone pour l'instant si pas de champ téléphone dans la base
    } else {
        // Sinon on récupère depuis le formulaire
        $nom = $conn->real_escape_string($_POST["lastName"]);
        $prenom = $conn->real_escape_string($_POST["firstName"]);
        $telephone = $conn->real_escape_string($_POST["phoneNumber"]);
    }
}




?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - ShopCasquette</title>
    <link rel="stylesheet" href="style_SQL.css">
    <style>
        main {
            padding: 20px;
            padding-bottom: 100px;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 30px auto;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 50px;
        }
        input[type="text"], input[type="number"] {
            padding: 8px;
            width: 200px;
            margin: 5px 0;
            border: 1px solid #ddd;
        }
        label {
            margin-top: 10px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<header>
    <h1>ShopCasquette</h1>
    <nav>
        <ul>
            <li><a href="index_SQL.php">Accueil</a></li>
            <li><a href="products_SQL.php">Produits</a></li>
            <li><a href="cart_SQL.php">Panier</a></li>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li><a href="admin_SQL.php">Interface Admin</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_prenom'])): ?>
                <li>
                    Bonjour <?= htmlspecialchars($_SESSION['user_prenom']) ?> <?= htmlspecialchars($_SESSION['user_nom']) ?>
                    (<a href="déconnexion_SQL.php">Déconnexion</a> | 
                    <a href="suppression_compte_SQL.php" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');" style="color:red;">Supprimer mon compte</a>)
                </li>
            <?php else: ?>
                <li><a href="formulaire_SQL.php">Créer un compte / Se connecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <form action="confirmation_SQL.php" method="POST">
        <?php if (isset($_SESSION['user_nom'], $_SESSION['user_prenom'], $_SESSION['user_email'])): ?>
            <input type="text" name="lastName" value="<?= htmlspecialchars($_SESSION['user_nom']) ?>" readonly>
            <input type="text" name="firstName" value="<?= htmlspecialchars($_SESSION['user_prenom']) ?>" readonly>
            <input type="text" name="phoneNumber" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" readonly>
        <?php else: ?>
            <input type="text" name="lastName" placeholder="Nom" required>
            <input type="text" name="firstName" placeholder="Prénom" required>
            <input type="text" name="phoneNumber" placeholder="Téléphone ou Email" required>
        <?php endif; ?>

        <input type="number" name="blueCaps" min="0" value="0"> Bleu
        <input type="number" name="redCaps" min="0" value="0"> Rouge
        <input type="number" name="greenCaps" min="0" value="0"> Vert
        <input type="number" name="yellowCaps" min="0" value="0"> Jaune
        <input type="number" name="blackCaps" min="0" value="0"> Noir
        <button type="submit" name="submitOrder">Valider</button>
    </form>


</main>

<script src="script_SQL.js"></script>
<footer>© 2025 ShopCasquette - Tous droits réservés</footer>
</body>
</html>

<?php $conn->close(); ?>


