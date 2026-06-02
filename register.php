<?php
require_once('includes/dbcon.php');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $members = array_filter(array_map('trim', $_POST['members'] ?? []), fn($m) => $m !== '');

    if ($teamName === '') {
        $error = 'Vul een teamnaam in.';
    } elseif (strlen($password) < 3 && $password !== '') {
        $error = 'Wachtwoord moet minimaal 3 tekens zijn.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Wachtwoorden komen niet overeen.';
    } elseif (count($members) < 1) {
        $error = 'Voeg minimaal één teamlid toe.';
    } else {
        // Check of teamnaam al bestaat
        $check = $db_connection->prepare("SELECT id FROM teams WHERE team_name = :name");
        $check->execute([':name' => $teamName]);
        if ($check->fetch()) {
            $error = 'Deze teamnaam bestaat al. Kies een andere naam.';
        } else {
            try {
                // Wachtwoord hashen voor veiligheid
                $hashedPassword = $password ? password_hash($password, PASSWORD_DEFAULT) : '';
                
                $stmt = $db_connection->prepare("INSERT INTO teams (team_name, password) VALUES (:name, :pwd)");
                $stmt->execute([':name' => $teamName, ':pwd' => $hashedPassword]);
                $teamId = $db_connection->lastInsertId();

                $stmtM = $db_connection->prepare("INSERT INTO team_members (team_id, member_name) VALUES (:tid, :name)");
                foreach ($members as $member) {
                    $stmtM->execute([':tid' => $teamId, ':name' => $member]);
                }
                
                // Automatisch inloggen na registratie
                $_SESSION['team_id'] = $teamId;
                $_SESSION['team_name'] = $teamName;
                $_SESSION['is_admin'] = false;
                
                $success = "Team \"{$teamName}\" is geregistreerd! Je wordt doorgestuurd...";
                header('refresh:2;url=index.php');
            } catch (PDOException $e) {
                $error = 'Databasefout: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registreren – The Dark House</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .register-page { max-width: 500px; margin: 0 auto; padding: 2rem; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .register-card { background: #0e0a0a; border: 1px solid #8b0000; border-radius: 24px; padding: 2rem; width: 100%; }
    .register-card h1 { font-family: 'Creepster', cursive; color: #c0000a; text-align: center; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 5px; color: #8b7355; font-size: 0.85rem; }
    .form-group input { width: 100%; padding: 10px; background: #1a1111; border: 1px solid #3a2a2a; color: #d4cfc9; border-radius: 8px; }
    .form-group input:focus { outline: none; border-color: #c0000a; }
    .btn { width: 100%; background: #8b0000; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; margin-top: 1rem; }
    .btn:hover { background: #c0000a; }
    .login-link { text-align: center; margin-top: 1rem; color: #6b6060; }
    .login-link a { color: #c0000a; text-decoration: none; }
    .error { background: #3a1a1a; padding: 10px; border-radius: 8px; color: #ff6666; margin-bottom: 1rem; text-align: center; }
    .success { background: #1a3a1a; padding: 10px; border-radius: 8px; color: #66ff66; margin-bottom: 1rem; text-align: center; }
    .member-row { display: flex; gap: 10px; align-items: center; margin-bottom: 10px; }
    .member-row input { flex: 1; }
    .remove-member { background: #3a1a1a; color: #ff6666; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-size: 1.2rem; }
    .add-member { background: #1a1a3a; color: #8888ff; border: none; padding: 5px 12px; border-radius: 8px; cursor: pointer; margin-top: 5px; font-size: 0.8rem; }
  </style>
</head>
<body>

<div class="drip-container">
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
</div>

<div class="register-page">
  <div class="register-card">
    <h1>👥 Registreer Team</h1>
    
    <?php if ($error): ?>
      <div class="error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" id="registerForm">
      <div class="form-group">
        <label>👑 Teamnaam</label>
        <input type="text" name="team_name" required value="<?= htmlspecialchars($_POST['team_name'] ?? '') ?>">
      </div>
      
      <div class="form-group">
        <label>🔒 Wachtwoord (optioneel - laat leeg voor geen wachtwoord)</label>
        <input type="password" name="password" id="password">
      </div>
      
      <div class="form-group">
        <label>🔒 Bevestig wachtwoord</label>
        <input type="password" name="confirm_password" id="confirm_password">
      </div>
      
      <div class="form-group">
        <label>👥 Teamleden</label>
        <div id="membersContainer">
          <div class="member-row">
            <input type="text" name="members[]" placeholder="Teamlid 1" required>
            <button type="button" class="remove-member" onclick="removeMember(this)" style="display:none;">✖</button>
          </div>
          <div class="member-row">
            <input type="text" name="members[]" placeholder="Teamlid 2" required>
            <button type="button" class="remove-member" onclick="removeMember(this)">✖</button>
          </div>
        </div>
        <button type="button" class="add-member" onclick="addMember()">+ Teamlid toevoegen</button>
      </div>
      
      <button type="submit" class="btn">🎮 Registreren & Starten</button>
    </form>
    
    <div class="login-link">
      Heb je al een team? <a href="index.php">→ Inloggen</a>
    </div>
  </div>
</div>

<script>
function addMember() {
    const container = document.getElementById('membersContainer');
    const rowCount = container.children.length;
    const newRow = document.createElement('div');
    newRow.className = 'member-row';
    newRow.innerHTML = `
        <input type="text" name="members[]" placeholder="Teamlid ${rowCount + 1}">
        <button type="button" class="remove-member" onclick="removeMember(this)">✖</button>
    `;
    container.appendChild(newRow);
}

function removeMember(btn) {
    const row = btn.parentElement;
    if (document.querySelectorAll('.member-row').length > 1) {
        row.remove();
    }
}

// Wachtwoord validatie optioneel
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const pwd = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    if (pwd !== confirm) {
        e.preventDefault();
        alert('Wachtwoorden komen niet overeen!');
    }
});
</script>

</body>
</html>