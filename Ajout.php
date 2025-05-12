<?php
// ==================== CONFIGURATION DB ====================
$host = 'localhost';
$dbname = 'hypermarket-admin';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// ==================== TRAITEMENT DU FORMULAIRE ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $categorie_id = $_POST['categorie_id'];
    
    // Gestion de l'image
    $image_name = '';
    if (isset($_FILES['image']) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_name = uniqid().'_'.$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir.$image_name);
    }
    
    // Insertion en base
    try {
        $stmt = $db->prepare("
            INSERT INTO produits (nom, prix, description, image, categorie_id, date_ajout)
            VALUES (:nom, :prix, :description, :image, :categorie_id, NOW())
        ");
        
        $stmt->execute([
            ':nom' => $nom,
            ':prix' => $prix,
            ':description' => $description,
            ':image' => $image_name,
            ':categorie_id' => $categorie_id
        ]);
        
        // Redirection pour éviter la resoumission du formulaire
        header("Location: ".$_SERVER['PHP_SELF']."?success=1&categorie_id=".$categorie_id);
        exit();
    } catch (PDOException $e) {
        $error_message = "Erreur d'insertion : " . $e->getMessage();
    }
}

// ==================== RÉCUPÉRATION DES DONNÉES ====================
// Catégories pour le select
$categories = $db->query("SELECT id, nom FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Produits par catégorie
$produits_par_categorie = [];
foreach ($categories as $categorie) {
    $stmt = $db->prepare("SELECT * FROM produits WHERE categorie_id = ?");
    $stmt->execute([$categorie['id']]);
    $produits_par_categorie[$categorie['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - HyperMarket</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2e8b57;
            --secondary: #343a40;
            --light: #f8f9fa;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-title {
            color: var(--primary);
            margin-bottom: 25px;
            font-weight: 600;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
        }
        
        .produits-categorie {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .categorie-title {
            color: var(--secondary);
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .categorie-title i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        .produit {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .produit:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .produit img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .produit .new {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            animation: pulse 2s infinite;
        }
        
        .produit.newly-added {
            animation: highlight 1.5s ease-out;
            border: 2px solid var(--primary);
        }
        
        @keyframes highlight {
            0% { background-color: rgba(46, 139, 87, 0.1); }
            100% { background-color: white; }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 10px 25px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #218838;
            border-color: #218838;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25);
        }
        
        .file-input-label {
            display: block;
            padding: 12px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        
        .file-input-label:hover {
            border-color: var(--primary);
            background-color: rgba(46, 139, 87, 0.05);
        }
        
        .file-input-label i {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        #image {
            display: none;
        }
        
        .empty-category {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }
        
        .alert-success {
            animation: fadeOut 3s forwards;
            animation-delay: 2s;
        }
        
        @keyframes fadeOut {
            to { opacity: 0; display: none; }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Produit ajouté avec succès!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <div class="form-container">
            <h2 class="form-title"><i class="fas fa-plus-circle"></i> Ajouter un nouveau produit</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom du produit</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="col-md-6">
                        <label for="prix" class="form-label">Prix (FCFA)</label>
                        <input type="number" class="form-control" id="prix" name="prix" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="categorie_id" class="form-label">Catégorie</label>
                        <select class="form-select" id="categorie_id" name="categorie_id" required>
                            <option value="" selected disabled>Choisir une catégorie</option>
                            <?php foreach ($categories as $categorie): ?>
                                <option value="<?= $categorie['id'] ?>"><?= htmlspecialchars($categorie['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image du produit</label>
                        <label for="image" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i><br>
                            <span id="file-name">Cliquez pour sélectionner une image</span>
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ajouter le produit
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Affichage par catégories -->
        <?php foreach ($categories as $categorie): ?>
            <div id="produits-<?= $categorie['id'] ?>" class="produits-categorie">
                <h3 class="categorie-title">
                    <i class="fas fa-tag"></i> <?= htmlspecialchars($categorie['nom']) ?>
                </h3>
                <div class="row" id="<?= $categorie['id'] ?>-container">
                    <?php if (!empty($produits_par_categorie[$categorie['id']])): ?>
                        <?php foreach ($produits_par_categorie[$categorie['id']] as $produit): ?>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="produit <?= (isset($_GET['categorie_id']) && $_GET['categorie_id'] == $categorie['id'] && isset($_GET['success'])) ? 'newly-added' : '' ?>">
                                    <img src="uploads/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" class="img-fluid">
                                    <div class="p-2">
                                        <h5 class="mb-1"><strong><?= htmlspecialchars($produit['nom']) ?></strong></h5>
                                        <p class="text-muted small mb-2"><?= htmlspecialchars($produit['description']) ?></p>
                                        <p class="fw-bold text-primary"><?= number_format($produit['prix'], 2) ?> FCFA</p>
                                        <?php if (isset($_GET['categorie_id']) && $_GET['categorie_id'] == $categorie['id'] && isset($_GET['success'])): ?>
                                            <span class="new">Nouveau</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 empty-category">Aucun produit dans cette catégorie</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Afficher le nom du fichier sélectionné
        document.getElementById('image').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Cliquez pour sélectionner une image';
            document.getElementById('file-name').textContent = fileName;
        });
        
        // Scroll vers la catégorie après ajout
        <?php if (isset($_GET['success']) && isset($_GET['categorie_id'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const categorieId = <?= json_encode($_GET['categorie_id']) ?>;
                const element = document.getElementById('produits-' + categorieId);
                if (element) {
                    setTimeout(() => {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }, 500);
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>