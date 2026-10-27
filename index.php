<?php
$pageTitle = 'Aura — Smart Home Control';
$bodyClass = 'landing';
require 'includes/header.php';
?>

<nav class="nav" id="mainNav">
  <div class="nav-brand">
    <div class="nav-logo"><?= icon('logo', 18) ?></div>
    Aura
  </div>
  <div class="nav-links">
    <a href="#features">Features</a>
    <a href="#stats">Stats</a>
    <a href="login.php">Sign in</a>
    <a href="register.php" class="btn btn-primary">Get Started</a>
  </div>
</nav>

<section class="hero">
  <div class="hero-content">
    <div class="hero-badge">
      <span class="pulse-dot"></span>
      All devices connected · Live
    </div>
    <h1>Your home, <em>intelligently</em> in sync.</h1>
    <p class="hero-lead">
      Control every light, thermostat, lock, and sensor from one beautiful dashboard.
      Automate your routines, track energy usage, and get real-time insights — all with zero effort.
    </p>
    <div class="hero-actions">
      <a href="register.php" class="btn btn-gradient btn-lg">
        Start Free <?= icon('arrow', 18) ?>
      </a>
      <a href="login.php" class="btn btn-outline btn-lg">Sign In</a>
    </div>
  </div>
  <div class="hero-visual">
    <div class="float-card">
      <h4>Living Room</h4>
      <div class="val">22.5°C</div>
      <div class="row">
        <span class="pill on">Heating</span>
        <span style="font-size:0.78rem;color:var(--text-muted);">Eco mode</span>
      </div>
    </div>
    <div class="float-card">
      <h4>Energy Today</h4>
      <div class="val">4.2 kWh</div>
      <div class="row">
        <span class="pill on">-18% vs avg</span>
      </div>
    </div>
    <div class="float-card">
      <h4>Security</h4>
      <div class="val" style="color:var(--accent-teal-strong);">Armed</div>
      <div class="row">
        <span class="pill warm">3 cameras</span>
        <span style="font-size:0.78rem;color:var(--text-muted);">All locked</span>
      </div>
    </div>
  </div>
</section>

<section class="section" id="features">
  <div class="section-head">
    <h2>Everything you need to run a smarter home</h2>
    <p>Built from the ground up for speed, clarity, and control. No clutter. No chaos.</p>
  </div>
  <div class="features">
    <div class="feature">
      <div class="feature-icon"><?= icon('zap', 24) ?></div>
      <h3>Instant Device Control</h3>
      <p>Toggle lights, adjust thermostats, and lock doors in milliseconds from anywhere.</p>
    </div>
    <div class="feature">
      <div class="feature-icon"><?= icon('auto', 24) ?></div>
      <h3>Smart Automations</h3>
      <p>Create routines triggered by time, temperature, or device state — running silently in the background.</p>
    </div>
    <div class="feature">
      <div class="feature-icon"><?= icon('chart', 24) ?></div>
      <h3>Energy Analytics</h3>
      <p>See exactly where your power goes with daily breakdowns and real-time consumption tracking.</p>
    </div>
    <div class="feature">
      <div class="feature-icon"><?= icon('shield', 24) ?></div>
      <h3>Unified Security</h3>
      <p>Cameras, locks, and sensors live in one view with instant alerts when something's off.</p>
    </div>
    <div class="feature">
      <div class="feature-icon"><?= icon('thermo', 24) ?></div>
      <h3>Climate Intelligence</h3>
      <p>Balance warmth, humidity, and air quality across every room without lifting a finger.</p>
    </div>
    <div class="feature">
      <div class="feature-icon"><?= icon('devices', 24) ?></div>
      <h3>Fully Responsive</h3>
      <p>Beautiful on desktop, tablet, and mobile. Your dashboard adapts to any screen size.</p>
    </div>
  </div>
</section>

<section class="stats-band" id="stats">
  <div class="stat"><div class="num">12K+</div><div class="lbl">Homes Connected</div></div>
  <div class="stat"><div class="num">98.4%</div><div class="lbl">Uptime</div></div>
  <div class="stat"><div class="num">3.2M</div><div class="lbl">Actions Automated</div></div>
  <div class="stat"><div class="num">4.9★</div><div class="lbl">User Rating</div></div>
</section>

<section class="cta-section">
  <h2>Ready to take control?</h2>
  <p>Join thousands of homes already running on Aura.</p>
  <a href="register.php" class="btn btn-gradient btn-lg">Create Your Account</a>
</section>

<footer class="site-footer">
  <div class="footer-grid">
    <div>
      <div class="footer-brand">
        <div class="nav-logo"><?= icon('logo', 18) ?></div> Aura
      </div>
      <p class="footer-tagline">Smart home control built for people who want less friction and more living.</p>
    </div>
    <div>
      <h4>Product</h4>
      <a href="#features">Features</a>
      <a href="#">Pricing</a>
      <a href="#">Integrations</a>
    </div>
    <div>
      <h4>Company</h4>
      <a href="#">About</a>
      <a href="#">Blog</a>
      <a href="#">Careers</a>
    </div>
    <div>
      <h4>Legal</h4>
      <a href="#">Privacy</a>
      <a href="#">Terms</a>
      <a href="#">Security</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= date('Y') ?> Aura Smart Home. All rights reserved.</span>
    <span>Built with care · v2.0</span>
  </div>
</footer>

<script>
  // Scrolled nav shadow
  (function () {
    var nav = document.getElementById('mainNav');
    if (!nav) return;
    function onScroll() {
      if (window.scrollY > 10) nav.classList.add('scrolled');
      else nav.classList.remove('scrolled');
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  })();
</script>

<?php require 'includes/footer.php'; ?>