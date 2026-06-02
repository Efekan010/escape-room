// ── Pas deze URLs aan naar eigen bestanden ──
const SCARE_IMAGE = 'img/jumpscare.jpg';   // vervang met jouw horror foto
const SCARE_SOUND = 'audio/scream.mp3';    // vervang met jouw geluid (mp3/ogg)

function triggerJumpscare() {
  const flash   = document.getElementById('flash');
  const scare   = document.getElementById('jumpscare');
  const scareImg = document.getElementById('scare-img');

  // Zet de foto net voor de trigger zodat hij niet al laadt
  scareImg.src = SCARE_IMAGE;

  // Speel geluid af als het beschikbaar is
  const audio = new Audio(SCARE_SOUND);
  audio.volume = 1.0;
  audio.play().catch(() => {
    // Browser blokkeert autoplay soms — jumpscare gaat gewoon door
  });

  // Rode flits
  flash.classList.add('pop');

  // Even wachten dan het gezicht tonen
  setTimeout(() => {
    scare.classList.add('active');
  }, 80);
}

function closeJumpscare() {
  const scare = document.getElementById('jumpscare');
  scare.style.transition = 'opacity 0.4s';
  scare.style.opacity = '0';
  setTimeout(() => { scare.style.display = 'none'; }, 420);
}

// Jumpscare na ~2.9s (zodra de loading bar vol is)
setTimeout(triggerJumpscare, 2900);
