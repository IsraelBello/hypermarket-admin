<?php
header('Content-Type: application/json');

// Configuration BDD
$host = 'localhost';
$dbname = 'hypermarket_admin';
$user = 'root';
$pass = '';

// Récupération des données POST
$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recherche de l'admin
    $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Connexion réussie
        echo json_encode([
            'success' => true,
            'adminId' => $admin['id'],
            'username' => $admin['username']
        ]);
    } else {
        // Échec connexion
        echo json_encode([
            'success' => false,
            'message' => 'Identifiants incorrects'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur système : ' . $e->getMessage()
    ]);
}
// À modifier dans register.php
$username = 'admin';              // Votre nom d'utilisateur admin
$plainPassword = 'Hypermarket12@';     // Votre mot de passe en clair
$email = 'oladossoubello@email.com';       // Votre email admin
?>