<?php
header('Content-Type: application/json');
require '../includes/config.php';

try {
    $stmt = $pdo->query("SELECT id, nom, icone FROM categories ORDER BY nom");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>