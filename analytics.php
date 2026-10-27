<?php
require 'includes/auth-check.php';
$user = currentUser();
$pageTitle = 'Analytics — Aura';
$bodyClass = 'analytics';
require 'includes/header.php';
?>
<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-brand"><div class="sidebar-logo"><?= icon('logo', 16) ?></div> Aura</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><?= icon('dash', 18) ?><span>Dashboard</span></a>
      <a href="devices.php"><?= icon('devices', 18) ?><span>Devices</span></a>
      <a href="automation.php"><?= icon('auto', 18) ?><span>Automations</span></a>
      <a href="analytics.php" class="active"><?= icon('stats', 18) ?><span>Analytics</span></a>
    </nav>
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
      <div class="info"><div class="name"><?= htmlspecialchars($user['name']) ?></div><div class="role">Home Owner</div></div>
      <a href="logout.php" title="Sign out"><?= icon('power', 16) ?></a>
    </div>
  </aside>
  <main class="main-area">
    <div class="top-bar">
      <div>
        <h1>Analytics</h1>
        <p class="subtitle">Energy and activity insights</p>
      </div>
      <div class="top-actions">
        <button class="icon-btn" data-theme-toggle aria-label="Toggle theme"><?= icon('moon', 18) ?></button>
        <button class="btn btn-outline" id="exportBtn"><?= icon('download', 16) ?> Export CSV</button>
      </div>
    </div>
    <div class="summary-row" id="summaryRow"></div>
    <div class="chart-grid">
      <div class="chart-panel">
        <h2>Energy Usage <span class="pill">Last 7 Days</span></h2>
        <div class="bar-chart" id="barChart"></div>
      </div>
      <div class="chart-panel">
        <h2>By Device Type</h2>
        <div class="donut-wrap">
          <div class="donut" id="donut"></div>
        </div>
        <div class="legend" id="legend"></div>
      </div>
    </div>
    <div class="table-panel">
      <div class="table-panel-head"><h2>Recent Events</h2></div>
      <table class="data-table">
        <thead><tr><th>Severity</th><th>Message</th><th>When</th></tr></thead>
        <tbody id="eventBody"></tbody>
      </table>
    </div>
  </main>
</div>
<?php require 'includes/footer.php'; ?>