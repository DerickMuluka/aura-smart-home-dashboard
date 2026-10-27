<?php
require 'includes/auth-check.php';
$user = currentUser();
$pageTitle = 'Dashboard — Aura';
$bodyClass = 'dashboard';
require 'includes/header.php';
?>
<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo"><?= icon('logo', 16) ?></div> Aura
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="active"><?= icon('dash', 18) ?><span>Dashboard</span></a>
      <a href="devices.php"><?= icon('devices', 18) ?><span>Devices</span></a>
      <a href="automation.php"><?= icon('auto', 18) ?><span>Automations</span></a>
      <a href="analytics.php"><?= icon('stats', 18) ?><span>Analytics</span></a>
    </nav>
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
      <div class="info">
        <div class="name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="role">Home Owner</div>
      </div>
      <a href="logout.php" title="Sign out"><?= icon('power', 16) ?></a>
    </div>
  </aside>
  <main class="main-area">
    <div class="top-bar">
      <div>
        <h1>Dashboard</h1>
        <p class="subtitle">System overview and quick controls</p>
      </div>
      <div class="top-actions">
        <div class="clock-badge"><?= icon('clock', 14) ?><span id="liveClock">--:--</span></div>
        <button class="icon-btn" data-theme-toggle aria-label="Toggle theme"><?= icon('moon', 18) ?></button>
        <a href="devices.php" class="btn btn-primary"><?= icon('plus', 16) ?> Add Device</a>
      </div>
    </div>
    <div class="overview-grid" id="overviewGrid"></div>
    <div class="content-grid">
      <div class="panel">
        <div class="panel-head">
          <h2>Quick Controls</h2>
          <a href="devices.php" class="btn-ghost">View all <?= icon('arrow', 14) ?></a>
        </div>
        <div class="device-list" id="deviceList"></div>
      </div>
      <div class="panel">
        <div class="panel-head"><h2>Activity</h2></div>
        <div class="activity-list" id="activityList"></div>
      </div>
    </div>
  </main>
</div>
<?php require 'includes/footer.php'; ?>