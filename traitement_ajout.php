<?php
// Affiche toutes les données reçues (pour débogage)
echo "<pre>";
print_r($_POST); // Si méthode POST, sinon $_GET
echo "</pre>";
// Arrête l'exécution pour vérifier
exit();
?>