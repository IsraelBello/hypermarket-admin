<?php
$host = 'localhost';
$dbname = 'hypermarket_admin';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    
    // Nouveaux identifiants
    $newUsername = 'Genial';
    $newEmail = 'oladossoubello@email.com';
    $newPassword = password_hash('Hypermarket12@', PASSWORD_DEFAULT);
    
    // Mise à jour
    $stmt = $pdo->prepare("UPDATE administrateurs SET 
                          username = ?, 
                          password_hash = ?, 
                          email = ? 
                          WHERE id = 1");
    $stmt->execute([$newUsername, $newPassword, $newEmail]);
    
    echo "Identifiants admin mis à jour !";
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>