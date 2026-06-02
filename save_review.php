<?php
require_once('includes/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');
    $roomId = (int)($_POST['room_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $difficulty = (int)($_POST['difficulty'] ?? 0);
    $feedback = trim($_POST['feedback'] ?? '');
    
    if ($teamName && $roomId >= 1 && $roomId <= 3 && $rating >= 1 && $rating <= 5 && $difficulty >= 1) {
        $stmt = $db_connection->prepare("INSERT INTO reviews (team_name, room_id, rating, difficulty, feedback) VALUES (:team, :room, :rating, :diff, :fb)");
        $stmt->execute([
            ':team' => $teamName,
            ':room' => $roomId,
            ':rating' => $rating,
            ':diff' => $difficulty,
            ':fb' => $feedback ?: null
        ]);
    }
    header('Location: reviews/view.php?success=1');
    exit;
}
?>