<?php
header('Content-Type: application/json');
require '../includes/config.php';

$response = ['success' => false, 'message' => ''];

try {
    // Vérification des données
    if(empty($_POST['nom']) throw new Exception('Le nom est requis');
    if(empty($_POST['description'])) throw new Exception('La description est requise');
    if(!is_numeric($_POST['prix']) || $_POST['prix'] <= 0) throw new Exception('Prix invalide');
    if(empty($_POST['categorie_id'])) throw new Exception('Catégorie requise');
    if(empty($_FILES['image'])) throw new Exception('Image requise');

    // Traitement de l'image
    $uploadDir = '../uploads/produits/';
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    if(!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        throw new Exception('Erreur lors du téléchargement de l\'image');
    }

    // Insertion en base
    $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, categorie_id, image_path) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nom'],
        $_POST['description'],
        $_POST['prix'],
        $_POST['categorie_id'],
        'uploads/produits/' . $fileName
    ]);

    $response = [
        'success' => true,
        'message' => 'Produit ajouté avec succès',
        'produit_id' => $pdo->lastInsertId()
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
<select class="form-select" id="categorie_id" name="categorie_id" required>
    <option value="" selected disabled>Choisir une catégorie</option>
    <?php
    require 'includes/config.php';
    $stmt = $pdo->query("SELECT id, nom, icone FROM categories ORDER BY nom");
    while ($categorie = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="'.$categorie['id'].'" data-icon="'.$categorie['icone'].'">';
        echo htmlspecialchars($categorie['nom']);
        echo '</option>';
    }
    ?>
</select>
?>