<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Créer un compte / Se connecter</title>
  <link rel="stylesheet" href="style_SQL.css">
  <style>
    .form-container {
      max-width: 400px;
      margin: auto;
      padding: 30px;
	  padding-bottom: 80px;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
      margin-top: 50px;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      box-sizing: border-box;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #007BFF;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }

    button:hover {
      background-color: #0056b3;
    }

    hr {
      margin: 30px 0;
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

  <div class="form-container">
    <h2>Créer un compte</h2>
    <form action="processus_création_compte_SQL.php" method="POST">
      <input type="text" name="nom" placeholder="Nom" required>
      <input type="text" name="prenom" placeholder="Prénom" required>
      <input type="date" name="date_naissance" required>
      <input type="text" name="adresse" placeholder="Adresse" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <input type="password" name="confirm_mot_de_passe" placeholder="Confirmer le mot de passe" required>
      <button type="submit">Créer mon compte</button>
    </form>

    <hr>

    <h2>Se connecter</h2>
    <form action="vérification_compte_SQL.php" method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <button type="submit">Se connecter</button>
    </form>
  </div>
  
  <script src="script_SQL.js"></script>
  <footer>© 2025 ShopCasquette - Tous droits réservés</footer>

</body>
</html>
