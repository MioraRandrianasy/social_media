<?php
require 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $type = $_POST['type'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur a déjà réagi à cette publication
    $stmt = $pdo->prepare("SELECT * FROM Reactions WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $existingReaction = $stmt->fetch();

    if ($existingReaction) {
        // Mise à jour de la réaction si elle existe
        $stmt = $pdo->prepare("UPDATE Reactions SET type = ? WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$type, $post_id, $user_id]);
    } else {
        // Ajout d'une nouvelle réaction
        $stmt = $pdo->prepare("INSERT INTO Reactions (post_id, user_id, type) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $type]);
    }
}
?>

