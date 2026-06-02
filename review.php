<?php
require_once('includes/dbcon.php');
$teamName = $_SESSION['team_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review – The Dark House</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .page { max-width: 600px; margin: 0 auto; padding: 2rem; }
    .form-card { background: #111; border: 1px solid #8b0000; border-radius: 16px; padding: 2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 5px; color: #8b7355; }
    input, select, textarea { width: 100%; padding: 8px; background: #1a1111; border: 1px solid #3a2a2a; color: #d4cfc9; border-radius: 8px; }
    .btn { background: #8b0000; color: white; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; }
    .star-rating { display: flex; gap: 8px; font-size: 1.8rem; cursor: pointer; }
    .star { color: #6b5a4a; }
    .diff-buttons { display: flex; gap: 10px; }
    .diff-btn { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #1a1111; border: 1px solid #3a2a2a; border-radius: 40px; cursor: pointer; }
    .diff-btn.active { background: #8b0000; color: white; }
  </style>
</head>
<body>
<div class="page">
  <a href="index.php" class="back-link" style="color:#6b6060;">← Terug</a>
  <div class="form-card">
    <h1 style="color:#8b0000;">★ Jouw ervaring ★</h1>
    <form method="POST" action="save_review.php">
      <div class="form-group">
        <label>Teamnaam</label>
        <input type="text" name="team_name" value="<?= htmlspecialchars($teamName) ?>" required>
      </div>
      <div class="form-group">
        <label>Kamer</label>
        <select name="room_id" required>
          <option value="">Kies...</option>
          <option value="1">🕯 De Verlaten Kelder</option>
          <option value="2">🩸 De Operatiekamer</option>
          <option value="3">💀 Het Kerkhof</option>
        </select>
      </div>
      <div class="form-group">
        <label>Beoordeling (1-5)</label>
        <div class="star-rating" id="starRating">
          <span class="star" data-val="1">☆</span><span class="star" data-val="2">☆</span>
          <span class="star" data-val="3">☆</span><span class="star" data-val="4">☆</span><span class="star" data-val="5">☆</span>
        </div>
        <input type="hidden" name="rating" id="rating" value="0">
      </div>
      <div class="form-group">
        <label>Moeilijkheid (1-5)</label>
        <div class="diff-buttons" id="diffButtons">
          <span class="diff-btn" data-diff="1">1</span><span class="diff-btn" data-diff="2">2</span>
          <span class="diff-btn" data-diff="3">3</span><span class="diff-btn" data-diff="4">4</span><span class="diff-btn" data-diff="5">5</span>
        </div>
        <input type="hidden" name="difficulty" id="difficulty" value="0">
      </div>
      <div class="form-group">
        <label>Feedback</label>
        <textarea name="feedback" rows="3" placeholder="Wat vond je ervan?"></textarea>
      </div>
      <button type="submit" class="btn">★ Review versturen ★</button>
    </form>
  </div>
</div>
<script>
  // Sterren rating
  let stars = document.querySelectorAll('.star');
  let ratingInput = document.getElementById('rating');
  stars.forEach(star => {
    star.addEventListener('click', () => {
      let val = parseInt(star.dataset.val);
      ratingInput.value = val;
      stars.forEach((s, i) => s.innerHTML = i < val ? '★' : '☆');
    });
  });
  // Moeilijkheid
  let diffBtns = document.querySelectorAll('.diff-btn');
  let diffInput = document.getElementById('difficulty');
  diffBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      let val = parseInt(btn.dataset.diff);
      diffInput.value = val;
      diffBtns.forEach(b => {
        if (parseInt(b.dataset.diff) <= val) b.classList.add('active');
        else b.classList.remove('active');
      });
    });
  });
</script>
</body>
</html>