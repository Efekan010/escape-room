<?php
require_once('../includes/dbcon.php');

// Haal alle reviews op met kamernaam
try {
    $reviews = $db_connection->query(
        "SELECT r.*, rooms.name as room_name 
         FROM reviews r 
         LEFT JOIN rooms ON r.room_id = rooms.id 
         ORDER BY r.created_at DESC"
    )->fetchAll();
    
    // Bereken gemiddelde scores
    $totalReviews = count($reviews);
    $avgRating = 0;
    $avgDifficulty = 0;
    
    if ($totalReviews > 0) {
        $sumRating = array_sum(array_column($reviews, 'rating'));
        $sumDifficulty = array_sum(array_column($reviews, 'difficulty'));
        $avgRating = round($sumRating / $totalReviews, 1);
        $avgDifficulty = round($sumDifficulty / $totalReviews, 1);
    }
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alle reviews – The Dark House</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .page { max-width: 900px; margin: 0 auto; padding: 2rem; }
    .page h1 { font-family: 'Creepster', cursive; color: #c0000a; text-align: center; margin-bottom: 0.5rem; font-size: 2.2rem; }
    .page-header p { text-align: center; color: #8b7355; margin-bottom: 2rem; }
    
    /* Stats balk */
    .stats-bar {
      display: flex; gap: 1rem; justify-content: center; margin-bottom: 2rem; flex-wrap: wrap;
    }
    .stat-card {
      background: #0e0a0a; border: 1px solid #3a2a2a; border-radius: 16px; padding: 1rem 1.5rem; text-align: center; min-width: 150px;
    }
    .stat-card .number { font-size: 2rem; font-weight: bold; color: #c9a84c; }
    .stat-card .label { font-size: 0.75rem; color: #8b7355; letter-spacing: 1px; }
    .stat-card .stars { font-size: 1.1rem; letter-spacing: 2px; }
    
    /* Review kaarten */
    .review-card {
      background: #0e0a0a; border: 1px solid #2a2a2a; border-radius: 16px; padding: 1.2rem; margin-bottom: 1rem; transition: 0.2s;
    }
    .review-card:hover { border-color: #8b0000; background: #120c0c; }
    .review-header {
      display: flex; flex-wrap: wrap; gap: 12px; align-items: baseline; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed #2a2a2a;
    }
    .team-name { font-weight: bold; color: #c9a84c; font-size: 1.1rem; }
    .room-badge { background: #8b000033; padding: 3px 12px; border-radius: 20px; font-size: 0.7rem; color: #ff9999; }
    .review-date { color: #6b6060; font-size: 0.7rem; margin-left: auto; }
    .review-stars { font-size: 1.3rem; letter-spacing: 3px; color: #c9a84c; margin: 8px 0; }
    .review-difficulty { font-size: 0.8rem; color: #b07a5a; margin-bottom: 12px; }
    .review-feedback { background: #1a1010; padding: 12px; border-radius: 12px; font-style: italic; color: #bcae8a; line-height: 1.5; margin-top: 8px; }
    .empty-state { text-align: center; padding: 3rem; color: #6b6060; background: #0e0a0a; border-radius: 16px; }
    .empty-state a { color: #c0000a; text-decoration: none; }
    .back-link { display: inline-block; margin-bottom: 1.5rem; color: #6b6060; text-decoration: none; }
    .back-link:hover { color: #c0000a; }
    .btn-review { display: inline-block; background: #8b0000; color: white; padding: 8px 20px; border-radius: 30px; text-decoration: none; margin-left: 1rem; font-size: 0.85rem; }
    .btn-review:hover { background: #c0000a; }
    .header-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 1rem; }
  </style>
</head>
<body>

<div class="drip-container">
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
</div>

<div class="page">
  <div class="header-actions">
    <a href="../index.php" class="back-link">← Terug naar de ingang</a>
    <a href="../review.php" class="btn-review">★ Nieuwe review schrijven</a>
  </div>
  
  <h1>★ Alle Speler Reviews ★</h1>
  <p>Wat vonden andere durfals van The Dark House?</p>
  
  <!-- Statistieken -->
  <?php if ($totalReviews > 0): ?>
  <div class="stats-bar">
    <div class="stat-card">
      <div class="number"><?= $totalReviews ?></div>
      <div class="label">Reviews</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $avgRating ?></div>
      <div class="label">Gem. Beoordeling</div>
      <div class="stars"><?= str_repeat('★', round($avgRating)) . str_repeat('☆', 5 - round($avgRating)) ?></div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $avgDifficulty ?></div>
      <div class="label">Gem. Moeilijkheid</div>
      <div class="stars">💀 <?= $avgDifficulty ?>/5</div>
    </div>
  </div>
  <?php endif; ?>
  
  <?php if (isset($_GET['success'])): ?>
    <div style="background:#1a3a1a; padding:12px; border-radius:8px; margin-bottom:1rem; text-align:center; color:#66ff66;">✅ Bedankt voor je review! Je helpt andere spelers.</div>
  <?php endif; ?>
  
  <!-- Reviews lijst -->
  <?php if (empty($reviews)): ?>
    <div class="empty-state">
      <p>👻 Nog geen reviews geschreven...</p>
      <p>Wees de eerste die zijn ervaring deelt!</p>
      <a href="../review.php" style="display:inline-block; margin-top:1rem; background:#8b0000; color:white; padding:8px 20px; border-radius:30px; text-decoration:none;">★ Schrijf een review ★</a>
    </div>
  <?php else: ?>
    <?php foreach ($reviews as $rev): ?>
    <div class="review-card">
      <div class="review-header">
        <span class="team-name">👥 <?= htmlspecialchars($rev['team_name']) ?></span>
        <span class="room-badge"><?= htmlspecialchars($rev['room_name'] ?? 'Kamer ' . $rev['room_id']) ?></span>
        <span class="review-date">📅 <?= date('d-m-Y', strtotime($rev['created_at'])) ?></span>
      </div>
      <div class="review-stars">
        <?= str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) ?>
      </div>
      <div class="review-difficulty">
        💀 Moeilijkheid: <?= str_repeat('●', $rev['difficulty']) . str_repeat('○', 5 - $rev['difficulty']) ?> (<?= $rev['difficulty'] ?>/5)
      </div>
      <?php if ($rev['feedback'] && $rev['feedback'] !== '—'): ?>
        <div class="review-feedback">
          “<?= nl2br(htmlspecialchars($rev['feedback'])) ?>”
        </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>