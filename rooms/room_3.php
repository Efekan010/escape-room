<?php
require_once('../includes/dbcon.php');

// Check of team is ingelogd
if (!isset($_SESSION['team_id']) || $_SESSION['team_id'] == 0) {
    header('Location: ../index.php');
    exit;
}

try {
    $stmt = $db_connection->query("SELECT * FROM riddles WHERE roomId = 3");
    $riddles = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}

$teamName = $_SESSION['team_name'] ?? 'Onbekend';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kamer 1 – De Verlaten Kelder</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    /* Dezelfde stijlen als eerder */
    .page { max-width: 900px; margin: 0 auto; padding: 2rem; position: relative; z-index: 5; }
    .page-header { text-align: center; margin-bottom: 2rem; }
    .page-header h1 { font-family: 'Creepster', cursive; font-size: clamp(2rem, 6vw, 3rem); color: #c0000a; margin-bottom: 0.5rem; }
    .team-label { color: #c9a84c; margin-top: 10px; display: inline-block; }
    .lives-wrap { text-align: center; margin-bottom: 1rem; font-size: 1.2rem; }
    .progress-wrap { margin-bottom: 2rem; }
    .progress-bar-bg { background: #2a1a1a; height: 6px; border-radius: 3px; overflow: hidden; }
    .progress-bar-fill { background: #c0000a; height: 100%; width: 0%; transition: width 0.3s ease; }
    .container { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .box { background: #0e0a0a; border: 1px solid #3a2a2a; border-radius: 12px; text-align: center; padding: 1.5rem 0.5rem; cursor: pointer; transition: all 0.2s; }
    .box:hover { border-color: #c0000a; background: #1a0a0a; }
    .box.solved { border-color: #2a5a2a; background: #0a1a0a; cursor: default; opacity: 0.7; pointer-events: none; }
    .box-icon { font-size: 2rem; display: block; margin-bottom: 0.5rem; }
    .win-screen, .lose-screen { display: none; text-align: center; padding: 2rem; background: rgba(8,5,5,0.95); border-radius: 24px; margin-top: 1rem; }
    .win-screen h2 { color: #c9a84c; font-family: 'Creepster', cursive; }
    .lose-screen h2 { color: #c0000a; font-family: 'Creepster', cursive; }
    .btn { display: inline-block; padding: 10px 24px; margin: 0.5rem; background: transparent; border: 1px solid #c0000a; color: #d4cfc9; text-decoration: none; border-radius: 30px; }
    .btn-solid { background: #c0000a; color: white; }
    .btn:hover { background: #c0000a; color: white; }
    .back-link { color: #6b6060; text-decoration: none; display: inline-block; margin-bottom: 1rem; }
  </style>
</head>
<body>

<div class="drip-container">
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
  <div class="drip"></div><div class="drip"></div><div class="drip"></div>
  <div class="drip"></div>
</div>

<div class="page">
  <a href="../index.php" class="back-link">← Terug naar de ingang</a>

  <div class="page-header">
    <h1>🕯 De Verlaten Kelder</h1>
    <p>Een donkere, vochtige kelder vol geheimen. De muren fluisteren...</p>
    <div class="team-label">👥 Team: <?= htmlspecialchars($teamName) ?></div>
  </div>

  <div class="lives-wrap">💀 Levens: <span id="lives-display">♥♥♥</span></div>

  <div class="progress-wrap">
    <p class="progress-label" id="progress-label">0 / <?= count($riddles) ?> raadsels opgelost</p>
    <div class="progress-bar-bg"><div class="progress-bar-fill" id="progress-fill"></div></div>
  </div>

  <div class="container" id="puzzleContainer">
    <?php foreach ($riddles as $index => $riddle): ?>
    <div class="box" onclick="openModal(<?= $index ?>)" data-index="<?= $index ?>" 
         data-riddle="<?= htmlspecialchars($riddle['riddle']) ?>" 
         data-answer="<?= htmlspecialchars(strtolower($riddle['answer'])) ?>"
         data-hint="<?= htmlspecialchars($riddle['hint'] ?? '') ?>">
      <span class="box-icon">🔒</span>
      Raadsel <?= $index + 1 ?>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="win-screen" id="win-screen">
    <h2>🏆 Je bent ontsnapt! 🏆</h2>
    <p>Je hebt alle raadsels van De Verlaten Kelder opgelost.<br>Durf jij de volgende kamer aan?</p>
    <a class="btn btn-solid" href="room_2.php">→ Naar De Operatiekamer</a>
    <a class="btn" href="../index.php">← Terug naar start</a>
  </div>

  <div class="lose-screen" id="lose-screen">
    <h2>💀 Je bent gevangen... 💀</h2>
    <p>Je levens zijn op. De duisternis heeft je opgeslokt.</p>
    <a class="btn btn-solid" href="room_1.php">↩ Opnieuw proberen</a>
    <a class="btn" href="../index.php">← Terug naar de ingang</a>
  </div>
</div>

<!-- Flash en Jumpscare -->
<div id="flash" style="position:fixed; inset:0; background:#c0000a; z-index:9998; opacity:0; pointer-events:none;"></div>
<div id="jumpscare" style="position:fixed; inset:0; background:#000; z-index:9999; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:0.05s linear; flex-direction:column;">
  <div style="font-size:180px; filter:drop-shadow(0 0 20px red);">👹💀👁️</div>
  <div class="scare-text" style="font-size:2rem; font-family:'Creepster',cursive; color:#c0000a;">IK ZIE JOU</div>
  <div onclick="closeJumpscare()" style="position:absolute; bottom:40px; background:#220e0e; padding:8px 24px; border-radius:36px; cursor:pointer;">✖ doorgaan ✖</div>
</div>

<!-- Modal -->
<section class="overlay" id="overlay" style="position:fixed; inset:0; background:rgba(0,0,0,0.85); display:none; z-index:100;" onclick="closeModal()"></section>
<section class="modal" id="modal" style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#0e0a0a; border:1px solid #c0000a; border-radius:24px; padding:2rem; width:90%; max-width:450px; display:none; z-index:101;">
  <h2 style="font-family:'Creepster',cursive; color:#c0000a;">⚰️ Raadsel ⚰️</h2>
  <p id="riddle" style="margin:1rem 0; line-height:1.5;"></p>
  <span class="hint-toggle" onclick="toggleHint()" style="color:#8b7355; cursor:pointer;">🔍 Toon hint</span>
  <div id="hint-text" style="background:#1a1010; padding:8px; border-radius:8px; margin:8px 0; display:none;"></div>
  <input type="text" id="answer" placeholder="Typ je antwoord..." style="width:100%; padding:10px; background:#1a1111; border:1px solid #3a2a2a; color:#d4cfc9; border-radius:8px;">
  <div style="display:flex; gap:10px; margin-top:1rem;">
    <button onclick="checkAnswer()" style="flex:1; background:#8b0000; border:none; padding:10px; color:white; border-radius:8px; cursor:pointer;">Verzenden</button>
    <button onclick="closeModal()" style="flex:1; background:#2a1a1a; border:none; padding:10px; color:white; border-radius:8px; cursor:pointer;">Sluiten</button>
  </div>
  <p id="feedback" style="margin-top:8px; text-align:center;"></p>
</section>

<script>
  let totalBoxes = <?= count($riddles) ?>;
  let solvedBoxes = 0;
  let livesLeft = 3;
  let currentIndex = null;
  let gameActive = true;

  function updateProgress() {
    let pct = (solvedBoxes / totalBoxes) * 100;
    document.getElementById('progress-fill').style.width = pct + '%';
    document.getElementById('progress-label').innerText = `${solvedBoxes} / ${totalBoxes} raadsels opgelost`;
  }

  function renderLives() {
    let el = document.getElementById('lives-display');
    let full = '♥'.repeat(livesLeft);
    let empty = '♡'.repeat(3 - livesLeft);
    el.innerHTML = `<span style="color:#c0000a">${full}</span><span style="color:#3a2a2a">${empty}</span>`;
  }

  function openModal(idx) {
    if (!gameActive || livesLeft <= 0) return;
    let box = document.querySelector(`.box[data-index='${idx}']`);
    if (box.classList.contains('solved')) return;
    currentIndex = idx;
    document.getElementById('riddle').innerText = box.dataset.riddle;
    document.getElementById('modal').dataset.answer = box.dataset.answer;
    let hint = box.dataset.hint;
    let hintDiv = document.getElementById('hint-text');
    if (hint) {
      hintDiv.innerText = hint;
      hintDiv.style.display = 'none';
      document.querySelector('.hint-toggle').style.display = 'inline-block';
    } else {
      document.querySelector('.hint-toggle').style.display = 'none';
    }
    document.getElementById('answer').value = '';
    document.getElementById('feedback').innerText = '';
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('modal').style.display = 'block';
    setTimeout(() => document.getElementById('answer').focus(), 80);
  }

  function toggleHint() {
    let hintDiv = document.getElementById('hint-text');
    let hidden = hintDiv.style.display === 'none' || hintDiv.style.display === '';
    hintDiv.style.display = hidden ? 'block' : 'none';
    document.querySelector('.hint-toggle').innerText = hidden ? '🔮 Verberg hint' : '🔍 Toon hint';
  }

  function closeModal() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('modal').style.display = 'none';
    currentIndex = null;
  }

  function triggerJumpscare() {
    let flash = document.getElementById('flash');
    flash.classList.add('pop');
    setTimeout(() => { document.getElementById('jumpscare').classList.add('active'); }, 80);
    setTimeout(() => flash.classList.remove('pop'), 300);
  }

  function closeJumpscare() {
    let scare = document.getElementById('jumpscare');
    scare.style.transition = 'opacity 0.4s';
    scare.style.opacity = '0';
    setTimeout(() => { scare.classList.remove('active'); scare.style.opacity = '0'; }, 400);
  }

  function checkAnswer() {
    if (!gameActive || currentIndex === null) return;
    let userAnswer = document.getElementById('answer').value.trim().toLowerCase();
    let correctAnswer = document.getElementById('modal').dataset.answer;
    let feedback = document.getElementById('feedback');
    
    if (userAnswer === correctAnswer) {
      feedback.innerText = '✅ Correct! Het slot klikt open...';
      feedback.style.color = '#4caf50';
      let box = document.querySelector(`.box[data-index='${currentIndex}']`);
      box.classList.add('solved');
      box.querySelector('.box-icon').innerHTML = '🔓';
      solvedBoxes++;
      updateProgress();
      setTimeout(() => {
        closeModal();
        if (solvedBoxes >= totalBoxes) {
          gameActive = false;
          document.querySelector('.container').style.display = 'none';
          document.querySelector('.progress-wrap').style.display = 'none';
          document.querySelector('.lives-wrap').style.display = 'none';
          document.getElementById('win-screen').style.display = 'block';
          triggerJumpscare();
        }
      }, 900);
    } else {
      livesLeft--;
      renderLives();
      if (livesLeft <= 0) {
        feedback.innerText = '💀 Geen levens meer! Je wordt opgeslokt...';
        feedback.style.color = '#cc2222';
        setTimeout(() => {
          closeModal();
          gameActive = false;
          document.querySelector('.container').style.display = 'none';
          document.querySelector('.progress-wrap').style.display = 'none';
          document.querySelector('.lives-wrap').style.display = 'none';
          document.getElementById('lose-screen').style.display = 'block';
        }, 1000);
      } else {
        feedback.innerText = `❌ Fout! Nog ${livesLeft} leven(s) over.`;
        feedback.style.color = '#cc2222';
        document.getElementById('answer').value = '';
        document.getElementById('answer').focus();
      }
    }
  }

  renderLives();
  updateProgress();

  let style = document.createElement('style');
  style.textContent = `
    #flash.pop { animation: redFlash 0.25s ease forwards; }
    @keyframes redFlash { 0% { opacity: 0.9; } 100% { opacity: 0; } }
    #jumpscare.active { opacity: 1; pointer-events: all; animation: shakeScare 0.5s ease both; }
    @keyframes shakeScare {
      0% { transform: translate(0,0); }
      20% { transform: translate(-10px,5px); }
      40% { transform: translate(8px,-6px); }
      60% { transform: translate(-5px,3px); }
      100% { transform: translate(0,0); }
    }
  `;
  document.head.appendChild(style);
</script>
</body>
</html>