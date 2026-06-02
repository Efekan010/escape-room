<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Admin credentials (hardcoded voor veiligheid)
    if ($username === 'admin123' && $password === 'admin123') {
        $_SESSION['team_id'] = 0;
        $_SESSION['team_name'] = 'Admin';
        $_SESSION['is_admin'] = true;
        header('Location: admin/show_all_teams.php');
        exit;
    } else {
        $error = 'Ongeldige gebruikersnaam of wachtwoord!';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – The Dark House</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .login-page { max-width: 450px; margin: 0 auto; padding: 2rem; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { background: #0e0a0a; border: 1px solid #8b0000; border-radius: 24px; padding: 2rem; width: 100%; text-align: center; }
    .login-card h1 { font-family: 'Creepster', cursive; color: #c0000a; margin-bottom: 1.5rem; }
    .login-card h1 small { font-size: 0.8rem; color: #6b6060; }
    .form-group { margin-bottom: 1rem; text-align: left; }
    .form-group label { display: block; margin-bottom: 5px; color: #8b7355; }
    .form-group input { width: 100%; padding: 12px; background: #1a1111; border: 1px solid #3a2a2a; color: #d4cfc9; border-radius: 8px; font-size: 1rem; }
    .form-group input:focus { outline: none; border-color: #c0000a; }
    .btn { width: 100%; background: #8b0000; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; margin-top: 0.5rem; }
    .btn:hover { background: #c0000a; }
    .error { background: #3a1a1a; padding: 10px; border-radius: 8px; color: #ff6666; margin-bottom: 1rem; }
    .back-link { display: block; margin-top: 1.5rem; color: #6b6060; text-decoration: none; font-size: 0.85rem; }
    .back-link:hover { color: #c0000a; }
    .warning { font-size: 0.7rem; color: #6b6060; margin-top: 1rem; }
  </style>
</head>
<body>

<div class="drip-container">
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
</div>

<div class="login-page">
  <div class="login-card">
    <h1>⚙️ Admin Panel <small>🔒</small></h1>
    
    <?php if ($error): ?>
      <div class="error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
      <div class="form-group">
        <label>👤 Gebruikersnaam</label>
        <input type="text" name="username" placeholder="admin123" required autofocus>
      </div>
      <div class="form-group">
        <label>🔒 Wachtwoord</label>
        <input type="password" name="password" placeholder="admin123" required>
      </div>
      <button type="submit" class="btn">🔑 Inloggen als Admin</button>
    </form>
    
    <a href="index.php" class="back-link">← Terug naar de spelersingang</a>
    <div class="warning">⚠️ Alleen voor beheerders! Gebruik: admin123 / admin123</div>
  </div>
</div>

</body>
</html>