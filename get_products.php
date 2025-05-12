<?php
// C:\xampp\htdocs\hypermarket-admin\api\get_products.php
require '../includes/config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT p.*, c.nom as categorie 
        FROM produits p
        JOIN categories c ON p.categorie_id = c.id
        ORDER BY p.date_ajout DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>