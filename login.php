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

    if (password_verify($motdepasseEntre, $result['password_hash'])) {
        // Connexion réussie
    } else {
        // Mot de passe incorrect
    }
    
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
?>