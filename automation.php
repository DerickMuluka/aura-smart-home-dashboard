<?php
require 'includes/auth-check.php';
$user = currentUser();
$pageTitle = 'Automations — Aura';
$bodyClass = 'automation';
require 'includes/header.php';
?>
<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-brand"><div class="sidebar-logo"><?= icon('logo', 16) ?></div> Aura</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><?= icon('dash', 18) ?><span>Dashboard</span></a>
      <a href="devices.php"><?= icon('devices', 18) ?><span>Devices</span></a>
      <a href="automation.php" class="active"><?= icon('auto', 18) ?><span>Automations</span></a>
      <a href="analytics.php"><?= icon('stats', 18) ?><span>Analytics</span></a>
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
        <h1>Automations</h1>
        <p class="subtitle">Rules that run your home automatically</p>
      </div>
      <div class="top-actions">
        <button class="icon-btn" data-theme-toggle aria-label="Toggle theme"><?= icon('moon', 18) ?></button>
        <button class="btn btn-primary" id="addAutoBtn"><?= icon('plus', 16) ?> New Rule</button>
      </div>
    </div>
    <div class="auto-header-strip">
      <div>
        <h2>Rule Overview</h2>
        <p>Trigger-based routines</p>
      </div>
      <div class="auto-stats" id="autoStats"></div>
    </div>
    <div class="auto-list" id="autoList"></div>
  </main>
</div>

<div class="modal-backdrop" id="autoModal">
  <form class="modal" id="autoForm">
    <h2>Create Automation</h2>
    <div class="form-group">
      <label>Rule Name</label>
      <input type="text" name="name" required placeholder="e.g. Evening Lights">
    </div>
    <div class="form-group">
      <label>Trigger Type</label>
      <select name="trigger_type" required>
        <option value="time">Time of day</option>
        <option value="temperature">Temperature threshold</option>
        <option value="motion">Motion detected</option>
        <option value="sunset">At sunset</option>
      </select>
    </div>
    <div class="form-group">
      <label>Trigger Value</label>
      <input type="text" name="trigger_value" placeholder="e.g. 18:00 or 22°C">
    </div>
    <div class="form-group">
      <label>Action Device</label>
      <select name="action_device" id="autoDevice" required></select>
    </div>
    <div class="form-group">
      <label>Action Value</label>
      <input type="text" name="action_value" placeholder="e.g. Turn on, set 21°C">
    </div>
    <div class="modal-actions">
      <button type="button" class="btn btn-outline" onclick="document.getElementById('autoModal').classList.remove('show')">Cancel</button>
      <button type="submit" class="btn btn-teal">Create Rule</button>
    </div>
  </form>
</div>
<?php require 'includes/footer.php'; ?>