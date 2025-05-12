<?php
// Vérification de la connexion
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: accueil.html');
    exit;
}

// Connexion BDD
$host = 'localhost';
$dbname = 'hypermarket_admin';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $stmt = $pdo->prepare("SELECT username FROM administrateurs WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>
    <h1>Bienvenue, <?= htmlspecialchars($admin['username']) ?>!</h1>
    <p>Ceci est votre tableau de bord administrateur.</p>
    <a href="logout.php">Déconnexion</a>
</body>
</html>