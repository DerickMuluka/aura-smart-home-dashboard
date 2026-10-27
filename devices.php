<?php
require 'includes/auth-check.php';
$user = currentUser();
$pageTitle = 'Devices — Aura';
$bodyClass = 'devices';
require 'includes/header.php';
?>
<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-brand"><div class="sidebar-logo"><?= icon('logo', 16) ?></div> Aura</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><?= icon('dash', 18) ?><span>Dashboard</span></a>
      <a href="devices.php" class="active"><?= icon('devices', 18) ?><span>Devices</span></a>
      <a href="automation.php"><?= icon('auto', 18) ?><span>Automations</span></a>
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
        <h1>Devices</h1>
        <p class="subtitle">Manage every connected device</p>
      </div>
      <div class="top-actions">
        <button class="icon-btn" data-theme-toggle aria-label="Toggle theme"><?= icon('moon', 18) ?></button>
      </div>
    </div>
    <div class="device-toolbar">
      <div class="search-box">
        <?= icon('search', 16) ?>
        <input type="text" id="searchInput" placeholder="Search devices...">
      </div>
      <select class="filter-select" id="filterType">
        <option value="">All Types</option>
        <option value="light">Lights</option>
        <option value="thermostat">Thermostats</option>
        <option value="lock">Locks</option>
        <option value="camera">Cameras</option>
        <option value="speaker">Speakers</option>
        <option value="plug">Plugs</option>
        <option value="fan">Fans</option>
        <option value="sensor">Sensors</option>
      </select>
      <button class="btn btn-primary" id="addDeviceBtn"><?= icon('plus', 16) ?> Add Device</button>
    </div>
    <div class="room-tabs" id="roomTabs"></div>
    <div class="device-grid" id="deviceGrid"></div>
  </main>
</div>

<div class="modal-backdrop" id="deviceModal">
  <form class="modal" id="deviceForm">
    <h2>Add New Device</h2>
    <div class="form-group">
      <label>Device Name</label>
      <input type="text" name="name" required placeholder="e.g. Bedroom Lamp" autocomplete="off">
    </div>
    <div class="form-group">
      <label>Device Type</label>
      <select name="type" required>
        <option value="light">Light</option>
        <option value="thermostat">Thermostat</option>
        <option value="lock">Lock</option>
        <option value="camera">Camera</option>
        <option value="speaker">Speaker</option>
        <option value="plug">Smart Plug</option>
        <option value="fan">Fan</option>
        <option value="sensor">Sensor</option>
      </select>
    </div>
    <div class="form-group">
      <label>Room</label>
      <select name="room_id" id="deviceRoom"><option value="">No room</option></select>
    </div>
    <div class="modal-actions">
      <button type="button" class="btn btn-outline" onclick="document.getElementById('deviceModal').classList.remove('show')">Cancel</button>
      <button type="submit" class="btn btn-primary">Add Device</button>
    </div>
  </form>
</div>
<?php require 'includes/footer.php'; ?>