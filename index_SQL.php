<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - ShopCasquette</title>
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
            <h2>Bienvenue sur ShopCasquette</h2>
            <p>Découvrez nos casquettes tendance pour tous les styles !</p>
            <a href="products_SQL.php" class="btn">Voir les produits</a>
        </section>
    </main>

    <script src="script_SQL.js"></script>
    <footer>© 2025 ShopCasquette - Tous droits réservés</footer>
</body>
</html>
