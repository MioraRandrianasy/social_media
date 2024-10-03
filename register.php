<?php
require 'config.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérification des données envoyées par le formulaire
    if (!isset($_POST['username']) || empty($_POST['username'])) {
        echo "Le nom d'utilisateur est requis.";
        exit();
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas.";
        exit();
    }

    // Vérifier si l'email est déjà utilisé
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Cet email est déjà utilisé.";
        exit();
    }

    // Hacher le mot de passe avec md5
    $hashed_password = md5($password); // Utilisation de md5 pour le hachage

    // Insérer le nouvel utilisateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed_password])) {
        echo "Inscription réussie. Vous pouvez maintenant vous connecter.";
        header("Location: index.html"); // Redirection vers la page de connexion
        exit();
    } else {
        echo "Erreur lors de l'inscription.";
    }
}
?>
