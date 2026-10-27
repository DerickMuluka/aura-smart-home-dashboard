<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$pageTitle = 'Create Account — Aura';
$bodyClass = 'auth';
require 'includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-visual">
    <div class="auth-logo"><div class="nav-logo"><?= icon('logo', 18) ?></div> Aura</div>
    <div>
      <h2>Start running your home <em>on autopilot</em>.</h2>
      <p>Set up in under a minute. Add your devices, create automations, and watch your home come alive.</p>
      <div class="auth-quote">
        <p>"The onboarding took less than 60 seconds. My lights, thermostat, and lock were all connected before I finished my coffee."</p>
        <span>— Priya R., verified user</span>
      </div>
    </div>
    <div style="font-size:0.82rem;color:#888;">&copy; <?= date('Y') ?> Aura Smart Home</div>
  </div>
  <div class="auth-form-side">
    <form class="auth-form" id="authForm" data-endpoint="api/auth.php" autocomplete="on">
      <h1>Create account</h1>
      <p>Your smart home awaits</p>
      <div class="auth-error" id="authError"></div>
      <input type="hidden" name="action" value="register">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required placeholder="Alex Johnson" autocomplete="name">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com" autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" required placeholder="At least 6 characters" minlength="6" autocomplete="new-password" class="has-toggle">
          <button type="button" class="toggle-pw" data-target="password" aria-label="Show password"><?= icon('eye', 18) ?></button>
        </div>
        <div class="pw-strength" id="pwStrength">
          <div class="pw-strength-bar"><div class="pw-strength-fill"></div></div>
          <div class="pw-strength-label">Password strength</div>
        </div>
      </div>
      <button type="submit" class="btn btn-teal">Create Account</button>
      <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    </form>
  </div>
</div>
<?php require 'includes/footer.php'; ?>