<?php
session_start();

// Connexion BDD
try {
    $pdo = new PDO("mysql:host=localhost;dbname=shopcasquette;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$conn = new mysqli("localhost", "root", "", "shopcasquette");

// Vérification que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_prenom']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_SQL.php');
    exit();
}

// Suppression utilisateur si demandé
if (isset($_POST['delete_user'])) {
    $id_user = intval($_POST['delete_user']);
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id_user]);
    header("Location: admin_SQL.php"); // Refresh pour actualiser la liste
    exit();
}

// Modification produit si demandé
if (isset($_POST['update_product'])) {
    $id_product = intval($_POST['id']);
    $new_price = floatval($_POST['price']);
    $new_description = htmlspecialchars($_POST['description']);
    $stmt = $pdo->prepare("UPDATE products SET price = ?, description = ? WHERE id = ?");
    $stmt->execute([$new_price, $new_description, $id_product]);
    header("Location: admin_SQL.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Admin - ShopCasquette</title>
    <link rel="stylesheet" href="style_SQL.css">
    <style>
        .admin-nav {
            text-align: center;
            margin: 20px;
			padding-bottom: 80px;
        }
        .admin-nav button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .admin-nav button:hover {
            background-color: #0056b3;
        }
        .admin-section {
            display: none;
            padding: 20px;
            margin-bottom: 50px; /* Ajout de marge en bas pour ne pas coller le footer */
        }
        .admin-section.active {
            display: block;
        }
        table {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        form {
            display: inline;
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

<?php if (isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
    <p style="color:green; text-align:center; font-weight:bold;">La base de données a été réinitialisée avec succès.</p>
<?php endif; ?>


<div style="text-align: center; margin: 20px;">
    <form action="reinitialiser_base_SQL.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir réinitialiser toute la base ? Cela supprimera tout.');">
        <button type="submit" style="padding: 10px 20px; background-color: red; color: white; border: none; border-radius: 5px; font-weight: bold;">Réinitialiser la Base de Données</button>
    </form>
</div>

<main>
    <section class="hero">
        <h2>Bienvenue dans l'interface Admin</h2>
        <p>Gérez votre boutique ShopCasquette</p>
    </section>

    <div class="admin-nav">
        <button onclick="showSection('users')">Gérer les utilisateurs</button>
        <button onclick="showSection('products')">Gérer les produits</button>
    </div>

    <!-- Section Utilisateurs -->
    <section id="users" class="admin-section active">
        <h3>Liste des utilisateurs</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, nom, prenom, email, role FROM utilisateurs");
            while ($user = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
                echo "<td>" . htmlspecialchars($user['prenom']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "<td>
                    <form method='POST' onsubmit='return confirm(\"Voulez-vous vraiment supprimer cet utilisateur ?\");'>
                        <input type='hidden' name='delete_user' value='" . $user['id'] . "'>
                        <button type='submit' style='background-color:red;color:white;border:none;padding:5px 10px;border-radius:5px;'>Supprimer</button>
                    </form>
                </td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </section>

    <!-- Section Produits -->
    <section id="products" class="admin-section">
        <h3>Gérer les produits</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prix (€)</th>
                    <th>Description</th>
                    <th>Modifier</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, name, price, description, image FROM products");
            while ($product = $stmt->fetch()) {
                echo "<tr>";
                echo "<form method='POST'>";
                echo "<td>" . htmlspecialchars($product['id']) . "<input type='hidden' name='id' value='" . $product['id'] . "'></td>";
                echo "<td>" . htmlspecialchars($product['name']) . "</td>";
                echo "<td><input type='number' step='0.01' name='price' value='" . htmlspecialchars($product['price']) . "' required></td>";
                echo "<td><textarea name='description' required>" . htmlspecialchars($product['description']) . "</textarea></td>";
                echo "<td><button type='submit' name='update_product' value='1'>Modifier</button></td>";
                echo "</form>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
		    <h2 style="text-align:center;">Historique des commandes</h2>
        <table>
        <thead>
            <tr>
                <th>Nom</th><th>Prénom</th><th>Téléphone/Email</th><th>Total (€)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT clients.nom, clients.prenom, clients.telephone, commandes.total FROM commandes JOIN clients ON commandes.client_id = clients.id");
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['nom']}</td><td>{$row['prenom']}</td><td>{$row['telephone']}</td><td>{$row['total']}€</td></tr>";
        }
        ?>
        </tbody>
        </table>

        <h2 style="text-align:center;">Stock Disponible</h2>
        <table>
        <thead>
            <tr>
                <th>Produit</th><th>Stock</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT products.name, stock.quantity FROM stock JOIN products ON stock.product_id = products.id");
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['name']}</td><td>{$row['quantity']}</td></tr>";
        }
        ?>
        </tbody>
    </table>
    </section>

</main>

<script src="script_SQL.js"></script>
<script>
function showSection(sectionId) {
    document.querySelectorAll('.admin-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(sectionId).classList.add('active');
}
</script>

<footer>© 2025 ShopCasquette - Tous droits réservés</footer>

</body>
</html>