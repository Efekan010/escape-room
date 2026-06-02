<?php
require_once('includes/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($teamName === '') {
        header('Location: index.php?error=1');
        exit;
    }
    
    // Check team in database
    $stmt = $db_connection->prepare("SELECT * FROM teams WHERE team_name = :name");
    $stmt->execute([':name' => $teamName]);
    $team = $stmt->fetch();
    
    if ($team) {
        // Wachtwoord check (ondersteunt zowel gehashte als plain text voor backward compatibility)
        $validPassword = false;
        if ($team['password'] === '') {
            $validPassword = true; // Geen wachtwoord nodig
        } elseif (password_verify($password, $team['password'])) {
            $validPassword = true; // Gehasht wachtwoord
        } elseif ($password === $team['password']) {
            $validPassword = true; // Plain text (oude stijl)
        }
        
        if ($validPassword) {
            $_SESSION['team_id'] = $team['id'];
            $_SESSION['team_name'] = $team['team_name'];
            $_SESSION['is_admin'] = false;
            header('Location: index.php');
            exit;
        }
    }
    
    header('Location: index.php?error=2');
    exit;
}
?>