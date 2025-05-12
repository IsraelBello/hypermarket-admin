<?php
session_start();

// Configuration sécurisée des identifiants admin
$admin_credentials = [
    'email' => 'oladossoubello@gmail.com',
    // Mot de passe généré avec password_hash('Hypermarket12@', PASSWORD_BCRYPT)
    'password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
];

// Protection contre les attaques par force brute
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_login_attempt'] = time();
}

if ($_SESSION['login_attempts'] > 3 && (time() - $_SESSION['last_login_attempt']) < 300) {
    header('Location: login.html?error=too_many_attempts');
    exit;
}

// Validation des entrées
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['rememberMe']) && $_POST['rememberMe'] === 'on';

// Vérification des identifiants
if ($email === $admin_credentials['email'] && password_verify($password, $admin_credentials['password_hash'])) {
    // Réinitialisation des tentatives après connexion réussie
    $_SESSION['login_attempts'] = 0;
    
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_email'] = $email;
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    
    // Gestion du "Rester connecté"
    if ($rememberMe) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + 86400 * 30; // 30 jours
        
        // Stocker le token en base de données serait idéal ici
        setcookie('admin_auth', $token, $expiry, "/", "", true, true);
    }
    
    // Régénération de l'ID de session pour prévenir les fixation de session
    session_regenerate_id(true);
    
    header('Location: bord.html');
    exit;
} else {
    // Incrémentation des tentatives échouées
    $_SESSION['login_attempts']++;
    $_SESSION['last_login_attempt'] = time();
    
    // Message d'erreur générique pour éviter la divulgation d'informations
    header('Location: login.html?error=auth_failed');
    exit;
}
?>