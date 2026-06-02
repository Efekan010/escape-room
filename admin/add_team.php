<?php
require_once('../includes/dbcon.php');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $members = array_filter(array_map('trim', $_POST['members'] ?? []), fn($m) => $m !== '');

    if ($teamName === '') {
        $error = 'Vul een teamnaam in.';
    } elseif (count($members) < 1) {
        $error = 'Voeg minimaal één teamlid toe.';
    } else {
        try {
            $hashedPassword = $password ? password_hash($password, PASSWORD_DEFAULT) : '';
            
            $stmt = $db_connection->prepare("INSERT INTO teams (team_name, password) VALUES (:name, :pwd)");
            $stmt->execute([':name' => $teamName, ':pwd' => $hashedPassword]);
            $teamId = $db_connection->lastInsertId();

            $stmtM = $db_connection->prepare("INSERT INTO team_members (team_id, member_name) VALUES (:tid, :name)");
            foreach ($members as $member) {
                $stmtM->execute([':tid' => $teamId, ':name' => $member]);
            }
            $success = "Team \"{$teamName}\" is aangemaakt! Wachtwoord: " . ($password ?: '(geen)');
        } catch (PDOException $e) {
            $error = 'Databasefout: ' . $e->getMessage();
        }
    }
}

$teams = $db_connection->query("SELECT * FROM teams ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team aanmaken – Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-page { max-width: 800px; margin: 0 auto; padding: 2rem; }
    .form-card { background: #111; border: 1px solid #8b0000; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 5px; color: #8b7355; }
    input, select { width: 100%; padding: 8px; background: #1a1111; border: 1px solid #3a2a2a; color: #d4cfc9; border-radius: 8px; }
    .btn { background: #8b0000; color: white; padding: 8px 20px; border: none; border-radius: 8px; cursor: pointer; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 8px; text-align: left; border-bottom: 1px solid #2a2a2a; }
    .back-link { color: #6b6060; text-decoration: none; display: inline-block; margin-bottom: 1rem; }
  </style>
</head>
<body>
<div class="admin-page">
  <a href="../index.php" class="back-link">← Terug naar de ingang</a>
  <a href="../admin_login.php" class="back-link" style="margin-left:1rem;">← Admin Login</a>
  
  <div class="form-card">
    <h2 style="color:#8b0000;">👥 Nieuw team aanmaken</h2>
    <form method="POST">
      <div class="form-group">
        <label>Teamnaam</label>
        <input type="text" name="team_name" required>
      </div>
      <div class="form-group">
        <label>Wachtwoord (optioneel)</label>
        <input type="text" name="password" placeholder="Laat leeg voor geen wachtwoord">
      </div>
      <div class="form-group">
        <label>Teamlid 1</label>
        <input type="text" name="members[]" required>
      </div>
      <div class="form-group">
        <label>Teamlid 2</label>
        <input type="text" name="members[]" required>
      </div>
      <div class="form-group">
        <label>Teamlid 3 (optioneel)</label>
        <input type="text" name="members[]">
      </div>
      <div class="form-group">
        <label>Teamlid 4 (optioneel)</label>
        <input type="text" name="members[]">
      </div>
      <button type="submit" class="btn">✅ Team aanmaken</button>
    </form>
    <?php if ($success): ?><div style="color:#4caf50; margin-top:1rem;"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div style="color:#c0000a; margin-top:1rem;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  </div>

  <h3 style="color:#8b7355;">Bestaande teams</h3>
  <table class="data-table">
    <thead><tr><th>ID</th><th>Team</th><th>Wachtwoord</th><th>Aangemaakt</th></tr></thead>
    <tbody>
      <?php foreach ($teams as $t): ?>
      <tr>
        <td><?= $t['id'] ?></td>
        <td><?= htmlspecialchars($t['team_name']) ?></td>
        <td><?= $t['password'] ? '🔒 heeft wachtwoord' : '❌ geen' ?></td>
        <td><?= date('d-m-Y', strtotime($t['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>