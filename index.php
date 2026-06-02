<?php require_once('includes/dbcon.php'); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>The Dark House – Horror Escape Room</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .login-box {
      margin-top: 2rem;
      padding: 1.5rem;
      background: rgba(0,0,0,0.5);
      border-radius: 16px;
      border-top: 1px solid #8b0000;
    }
    .login-box input {
      background: #1a1111;
      border: 1px solid #3a2a2a;
      padding: 8px 12px;
      color: #d4cfc9;
      border-radius: 8px;
      margin: 5px;
    }
    .login-box button {
      background: #8b0000;
      border: none;
      padding: 8px 20px;
      color: white;
      border-radius: 8px;
      cursor: pointer;
    }
    .team-info {
      margin-top: 1rem;
      color: #c9a84c;
    }
    .logout-btn {
      background: #3a2a2a;
      padding: 5px 12px;
      font-size: 0.8rem;
    }
    .register-link {
      margin-top: 10px;
    }
    .register-link a {
      color: #8b7355;
      font-size: 0.8rem;
    }
    .admin-link {
      margin-top: 10px;
      border-top: 1px dashed #3a2a2a;
      padding-top: 10px;
    }
  </style>
</head>
<body>

<div class="drip-container">
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
</div>

<header class="hero">
  <div class="hero-box">
    <div class="eye-icon">👁️‍🗨️</div>
    <h1>THE DARK HOUSE</h1>
    <p>Een verlaten huis... vreemde geluiden... en geen uitgang.<br>
    Kies een kamer en ontsnap voor het te laat is.</p>
    
    <nav class="rooms-nav">
      <a href="rooms/room_1.php" class="btn">🕯 De Verlaten Kelder</a>
      <a href="rooms/room_2.php" class="btn">🩸 De Operatiekamer</a>
      <a href="rooms/room_3.php" class="btn">💀 Het Kerkhof</a>
    </nav>

    <?php if (isset($_SESSION['team_id']) && isset($_SESSION['team_name'])): ?>
      <div class="team-info">
        👥 Team: <?= htmlspecialchars($_SESSION['team_name']) ?>
        <a href="logout.php" class="btn logout-btn" style="margin-left:10px;">🚪 Uitloggen</a>
      </div>
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <div class="admin-link">
          <a href="admin/show_all_teams.php" class="btn-muted">⚙️ Admin Panel</a>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="login-box">
        <form method="POST" action="login.php" style="display:flex; flex-wrap:wrap; justify-content:center; gap:8px;">
          <input type="text" name="team_name" placeholder="Teamnaam" required>
          <input type="password" name="password" placeholder="Wachtwoord">
          <button type="submit">🔑 Inloggen</button>
        </form>
        <div class="register-link">
          <a href="register.php">✨ Nog geen team? Registreer hier! ✨</a>
        </div>
        <div class="admin-link">
          <a href="admin_login.php" style="color:#6b6060; font-size:0.7rem;">⚙️ Admin login</a>
        </div>
      </div>
    <?php endif; ?>

    <div class="review-link" style="margin-top:1.5rem;">
      <a href="review.php" class="btn-muted">★ Laat een review achter</a>
      <a href="reviews/view.php" class="btn-muted">★ Bekijk alle reviews</a>
    </div>
  </div>
</header>

</body>
</html>