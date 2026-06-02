<?php
require_once('includes/dbcon.php');

$success = '';
$error = '';

// Als team is ingelogd, gebruik die teamnaam
$teamName = $_SESSION['team_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');
    $roomId = (int)($_POST['room_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $difficulty = (int)($_POST['difficulty'] ?? 0);
    $feedback = trim($_POST['feedback'] ?? '');
    
    if ($teamName === '') {
        $error = 'Vul een teamnaam in.';
    } elseif ($roomId < 1 || $roomId > 3) {
        $error = 'Selecteer een geldige kamer.';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Geef een beoordeling tussen 1 en 5 sterren.';
    } elseif ($difficulty < 1 || $difficulty > 5) {
        $error = 'Geef een moeilijkheidsgraad tussen 1 en 5.';
    } else {
        try {
            $stmt = $db_connection->prepare("INSERT INTO reviews (team_name, room_id, rating, difficulty, feedback) VALUES (:team, :room, :rating, :diff, :fb)");
            $stmt->execute([
                ':team' => $teamName,
                ':room' => $roomId,
                ':rating' => $rating,
                ':diff' => $difficulty,
                ':fb' => $feedback ?: null
            ]);
            $success = '✅ Bedankt voor je review! Je wordt doorgestuurd...';
            header('refresh:2;url=reviews/view.php');
        } catch (PDOException $e) {
            $error = 'Databasefout: ' . $e->getMessage();
        }
    }
}

// Haal kamers op voor dropdown
$rooms = $db_connection->query("SELECT * FROM rooms ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review – The Dark House</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .page { max-width: 600px; margin: 0 auto; padding: 2rem; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .form-card { background: #0e0a0a; border: 2px solid #8b0000; border-radius: 24px; padding: 2rem; width: 100%; }
    .form-card h1 { font-family: 'Creepster', cursive; color: #c0000a; text-align: center; margin-bottom: 0.5rem; font-size: 2rem; }
    .form-card p { text-align: center; color: #8b7355; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; margin-bottom: 5px; color: #c9a84c; font-size: 0.85rem; letter-spacing: 1px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; background: #1a1111; border: 1px solid #3a2a2a; color: #d4cfc9; border-radius: 8px; font-family: inherit; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #c0000a; }
    .star-rating { display: flex; gap: 8px; font-size: 2rem; cursor: pointer; justify-content: center; margin: 10px 0; }
    .star { color: #6b5a4a; transition: 0.1s; cursor: pointer; }
    .star.active, .star.hover { color: #c9a84c; text-shadow: 0 0 5px #c9a84c; }
    .difficulty-buttons { display: flex; gap: 12px; justify-content: center; margin: 10px 0; }
    .diff-btn { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: #1a1111; border: 1px solid #3a2a2a; border-radius: 50px; cursor: pointer; transition: 0.1s; font-size: 1.1rem; }
    .diff-btn.active { background: #8b0000; border-color: #c0000a; color: white; box-shadow: 0 0 10px #8b0000; }
    .btn-submit { width: 100%; background: #8b0000; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; margin-top: 0.5rem; transition: 0.2s; }
    .btn-submit:hover { background: #c0000a; transform: scale(0.98); }
    .error { background: #3a1a1a; padding: 10px; border-radius: 8px; color: #ff6666; margin-bottom: 1rem; text-align: center; }
    .success { background: #1a3a1a; padding: 10px; border-radius: 8px; color: #66ff66; margin-bottom: 1rem; text-align: center; }
    .back-link { display: inline-block; margin-top: 1rem; color: #6b6060; text-decoration: none; font-size: 0.85rem; text-align: center; width: 100%; }
    .back-link:hover { color: #c0000a; }
    .drip-container { z-index: 1; }
  </style>
</head>
<body>

<div class="drip-container">
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
</div>

<div class="page">
  <div class="form-card">
    <h1>★ Jouw Ervaring ★</h1>
    <p>Hoe was jouw tocht door The Dark House?</p>
    
    <?php if ($error): ?>
      <div class="error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" id="reviewForm">
      <div class="form-group">
        <label>👥 Teamnaam</label>
        <input type="text" name="team_name" required value="<?= htmlspecialchars($teamName) ?>" placeholder="Jouw teamnaam">
      </div>
      
      <div class="form-group">
        <label>🕯 Welke kamer speelde je?</label>
        <select name="room_id" required>
          <option value="">— Kies een kamer —</option>
          <?php foreach ($rooms as $room): ?>
            <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label>⭐ Beoordeling (1-5 sterren)</label>
        <div class="star-rating" id="starRating">
          <span class="star" data-value="1">☆</span>
          <span class="star" data-value="2">☆</span>
          <span class="star" data-value="3">☆</span>
          <span class="star" data-value="4">☆</span>
          <span class="star" data-value="5">☆</span>
        </div>
        <input type="hidden" name="rating" id="rating" value="0" required>
      </div>
      
      <div class="form-group">
        <label>💀 Moeilijkheidsgraad (1-5)</label>
        <div class="difficulty-buttons" id="difficultyButtons">
          <span class="diff-btn" data-diff="1">1</span>
          <span class="diff-btn" data-diff="2">2</span>
          <span class="diff-btn" data-diff="3">3</span>
          <span class="diff-btn" data-diff="4">4</span>
          <span class="diff-btn" data-diff="5">5</span>
        </div>
        <input type="hidden" name="difficulty" id="difficulty" value="0" required>
      </div>
      
      <div class="form-group">
        <label>💬 Feedback (optioneel)</label>
        <textarea name="feedback" rows="4" placeholder="Wat vond je goed? Wat kan beter? Deel je ervaring..."></textarea>
      </div>
      
      <button type="submit" class="btn-submit">★ Review Versturen ★</button>
    </form>
    
    <a href="index.php" class="back-link">← Terug naar de ingang</a>
    <a href="reviews/view.php" class="back-link" style="margin-left: 10px;">★ Bekijk alle reviews →</a>
  </div>
</div>

<script>
// Sterren rating systeem
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('rating');
let currentRating = 0;

stars.forEach(star => {
    star.addEventListener('mouseover', function() {
        const value = parseInt(this.dataset.value);
        stars.forEach((s, i) => {
            if (i < value) {
                s.innerHTML = '★';
                s.style.color = '#c9a84c';
            } else {
                s.innerHTML = '☆';
                s.style.color = '#6b5a4a';
            }
        });
    });
    
    star.addEventListener('mouseout', function() {
        stars.forEach((s, i) => {
            if (i < currentRating) {
                s.innerHTML = '★';
                s.style.color = '#c9a84c';
            } else {
                s.innerHTML = '☆';
                s.style.color = '#6b5a4a';
            }
        });
    });
    
    star.addEventListener('click', function() {
        currentRating = parseInt(this.dataset.value);
        ratingInput.value = currentRating;
        stars.forEach((s, i) => {
            if (i < currentRating) {
                s.innerHTML = '★';
                s.style.color = '#c9a84c';
            } else {
                s.innerHTML = '☆';
                s.style.color = '#6b5a4a';
            }
        });
    });
});

// Moeilijkheid buttons
const diffBtns = document.querySelectorAll('.diff-btn');
const diffInput = document.getElementById('difficulty');

diffBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const value = parseInt(btn.dataset.diff);
        diffInput.value = value;
        diffBtns.forEach(b => {
            if (parseInt(b.dataset.diff) <= value) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
    });
});

// Form validation
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    if (parseInt(ratingInput.value) === 0) {
        e.preventDefault();
        alert('Geef een beoordeling (1-5 sterren)');
    }
    if (parseInt(diffInput.value) === 0) {
        e.preventDefault();
        alert('Geef een moeilijkheidsgraad (1-5)');
    }
});
</script>

</body>
</html>