<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$pageTitle = 'Sign In — Aura';
$bodyClass = 'auth';
require 'includes/header.php';
$justRegistered = isset($_GET['registered']);
?>
<div class="auth-wrap">
  <div class="auth-visual">
    <div class="auth-logo"><div class="nav-logo"><?= icon('logo', 18) ?></div> Aura</div>
    <div>
      <h2>Welcome back to your <em>intelligent</em> home.</h2>
      <p>Sign in to control every device, view your energy insights, and manage your automations from one place.</p>
      <div class="auth-quote">
        <p>"Aura turned my chaotic smart home into something I actually enjoy using."</p>
        <span>— Marcus T., verified user</span>
      </div>
    </div>
    <div style="font-size:0.82rem;color:#888;">&copy; <?= date('Y') ?> Aura Smart Home</div>
  </div>
  <div class="auth-form-side">
    <form class="auth-form" id="authForm" data-endpoint="api/auth.php" autocomplete="on">
      <h1>Sign in</h1>
      <p>Access your dashboard</p>
      <div class="auth-error" id="authError"></div>
      <?php if ($justRegistered): ?>
        <div style="background:rgba(20,184,166,0.1);color:#0d9488;padding:12px 16px;border-radius:10px;font-size:0.86rem;margin-bottom:18px;">
          Account created successfully. Sign in to continue.
        </div>
      <?php endif; ?>
      <input type="hidden" name="action" value="login">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com" autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" required placeholder="Enter your password" autocomplete="current-password" class="has-toggle">
          <button type="button" class="toggle-pw" data-target="password" aria-label="Show password"><?= icon('eye', 18) ?></button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Sign In</button>
      <p class="auth-switch">New to Aura? <a href="register.php">Create an account</a></p>
    </form>
  </div>
</div>
<?php require 'includes/footer.php'; ?>