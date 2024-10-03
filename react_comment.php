<?php
require 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commentId = $_POST['comment_id'];
    $reactionType = $_POST['type'];
    $userId = $_SESSION['user_id']; // Identifiant de l'utilisateur connecté

    // Vérifier si l'utilisateur a déjà réagi à ce commentaire
    $stmt = $pdo->prepare("SELECT id FROM Comment_Reactions WHERE comment_id = ? AND user_id = ?");
    $stmt->execute([$commentId, $userId]);
    $existingReaction = $stmt->fetch();

    if ($existingReaction) {
        // Mise à jour de la réaction existante
        $stmt = $pdo->prepare("UPDATE Comment_Reactions SET reaction_type = ? WHERE comment_id = ? AND user_id = ?");
        $stmt->execute([$reactionType, $commentId, $userId]);
    } else {
        // Insertion d'une nouvelle réaction
        $stmt = $pdo->prepare("INSERT INTO Comment_Reactions (comment_id, user_id, reaction_type) VALUES (?, ?, ?)");
        $stmt->execute([$commentId, $userId, $reactionType]);
    }

    // Récupérer les nouvelles statistiques des réactions pour ce commentaire
    $stmt = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM Comment_Reactions WHERE comment_id = ? AND reaction_type = 'like') AS likes,
            (SELECT COUNT(*) FROM Comment_Reactions WHERE comment_id = ? AND reaction_type = 'love') AS loves,
            (SELECT COUNT(*) FROM Comment_Reactions WHERE comment_id = ? AND reaction_type = 'haha') AS hahas
    ");
    $stmt->execute([$commentId, $commentId, $commentId]);
    $reactionCounts = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($reactionCounts); // Retourner les nouvelles statistiques des réactions
}
?>
