<?php
session_start();

// Connexion BDD
try {
    $pdo = new PDO("mysql:host=localhost;dbname=shopcasquette;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits - ShopCasquette</title>
    <link rel="stylesheet" href="style_SQL.css">
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
    <section class="hero">
        <h2>Nos casquettes</h2>
        <p>Choisissez votre style préféré !</p>
    </section>

    <section class="products" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; padding: 20px; padding-bottom: 80px;">
    <?php
    $stmt = $pdo->query("SELECT id, name, price, description, image FROM products");
    while ($product = $stmt->fetch()) {
        ?>
        <div class="product-card" style="border: 1px solid #ddd; padding: 20px; border-radius: 10px; width: 250px; text-align: center;">
            <?php if (!empty($product['image'])): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; height: auto; border-radius: 10px;">
            <?php endif; ?>
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p><strong><?= number_format($product['price'], 2) ?> €</strong></p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>
        <?php
    }
    ?>
    </section>
</main>

<script src="script_SQL.js"></script>
<footer>© 2025 ShopCasquette - Tous droits réservés</footer>

</body>
</html>
