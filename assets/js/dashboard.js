var ICONS = {
  light:      '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7V17h8v-2.3A7 7 0 0012 2z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
  thermostat: '<path d="M14 14.76V4a2 2 0 10-4 0v10.76a4 4 0 104 0z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
  lock:       '<rect x="4" y="11" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 11V7a4 4 0 118 0v4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
  camera:     '<path d="M23 7l-7 5 7 5V7zM14 5H3a2 2 0 00-2 2v10a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
  speaker:    '<path d="M11 5L6 9H2v6h4l5 4V5zM19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
  plug:       '<path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 01-6 6v5M12 17v-0" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
  fan:        '<circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 10c0-3 1-5 1-7M12 14c0 3-1 5-1 7M10 12c-3 0-5 1-7 1M14 12c3 0 5-1 7-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
  sensor:     '<circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 8a6 6 0 000 8M16 8a6 6 0 010 8M5 5a10 10 0 000 14M19 5a10 10 0 010 14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>'
};

function svg(path, size) {
  size = size || 18;
  return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 24 24" fill="none">' + path + '</svg>';
}

function showDashboardError(message) {
  var grid = document.getElementById('overviewGrid');
  if (grid) {
    grid.innerHTML =
      '<div class="metric" style="grid-column:1/-1;text-align:center;">' +
        '<div class="value" style="font-size:1rem;color:var(--accent-rose);">' + SH.esc(message) + '</div>' +
        '<div class="delta">Retrying in 5s...</div>' +
      '</div>';
  }
  var list = document.getElementById('deviceList');
  if (list) list.innerHTML = '<div class="activity-empty">Unable to load devices.</div>';
  var act = document.getElementById('activityList');
  if (act) act.innerHTML = '<div class="activity-empty">Unable to load activity.</div>';
}

async function loadDashboard() {
  try {
    var data = await SH.api('api/devices.php?summary=1');

    // Defensive validation
    if (!data || typeof data !== 'object') {
      throw new Error('Empty response from server');
    }
    if (!data.overview) {
      throw new Error('Missing overview data');
    }

    renderOverview(data.overview);
    renderDevices(Array.isArray(data.devices) ? data.devices.slice(0, 6) : []);
    renderActivity(Array.isArray(data.events) ? data.events : []);
  } catch (err) {
    console.error('[dashboard]', err);
    showDashboardError(err.message || 'Failed to load dashboard');
    SH.toast(err.message || 'Failed to load dashboard', 'error');
  }
}

function renderOverview(o) {
  var grid = document.getElementById('overviewGrid');
  if (!grid) return;
  o = o || {};
  var total = Number(o.total || 0);
  var online = Number(o.online || 0);
  var offline = Math.max(0, total - online);
  var energy = Number(o.energy || 0);
  var avgTemp = Number(o.avgTemp || 0);
  var autos = Number(o.automations || 0);
  var energyDelta = String(o.energyDelta || '0%');

  var items = [
    {
      label: 'Devices Online',
      value: online + '/' + total,
      icon: '<path d="M4 4h10v10H4zM14 4h6v16h-6z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
      sub: offline + ' offline'
    },
    {
      label: 'Energy Today',
      value: energy.toFixed(2) + ' kWh',
      icon: '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
      sub: energyDelta + ' vs yesterday',
      cls: energyDelta.indexOf('-') === 0 ? 'up' : 'down'
    },
    {
      label: 'Avg. Temperature',
      value: avgTemp.toFixed(1) + '\u00B0C',
      icon: '<path d="M14 14.76V4a2 2 0 10-4 0v10.76a4 4 0 104 0z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
      sub: 'Across thermostats'
    },
    {
      label: 'Active Automations',
      value: String(autos),
      icon: '<circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
      sub: 'Running routines'
    }
  ];

  grid.innerHTML = items.map(function (it, i) {
    return '<div class="metric stagger" style="animation-delay:' + (i * 60) + 'ms">' +
      '<div class="metric-head">' +
        '<div class="label">' + it.label + '</div>' +
        '<div class="metric-icon">' + svg(it.icon, 16) + '</div>' +
      '</div>' +
      '<div class="value">' + it.value + '</div>' +
      '<div class="delta ' + (it.cls || '') + '">' + it.sub + '</div>' +
    '</div>';
  }).join('');
}

function renderDevices(devices) {
  var list = document.getElementById('deviceList');
  if (!list) return;
  if (!devices.length) {
    list.innerHTML = '<div class="activity-empty">No devices yet. Add one to get started.</div>';
    return;
  }
  list.innerHTML = devices.map(function (d, i) {
    return '<div class="device-row stagger" style="animation-delay:' + (i * 40) + 'ms">' +
      '<div class="device-icon ' + d.type + '">' + svg(ICONS[d.type] || ICONS.light, 20) + '</div>' +
      '<div class="device-info">' +
        '<div class="name">' + SH.esc(d.name) + '</div>' +
        '<div class="meta">' + SH.esc(d.room_name || 'No room') + ' \u00B7 ' + d.type + '</div>' +
      '</div>' +
      '<label class="switch">' +
        '<input type="checkbox" ' + (d.status == 1 ? 'checked' : '') + ' data-id="' + d.id + '">' +
        '<span class="slider"></span>' +
      '</label>' +
    '</div>';
  }).join('');

  list.querySelectorAll('input[type=checkbox]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      toggleDevice(Number(cb.dataset.id), cb.checked);
    });
  });
}

function renderActivity(events) {
  var list = document.getElementById('activityList');
  if (!list) return;
  if (!events.length) {
    list.innerHTML = '<div class="activity-empty">No activity recorded yet.</div>';
    return;
  }
  list.innerHTML = events.map(function (e, i) {
    return '<div class="activity-item stagger" style="animation-delay:' + (i * 50) + 'ms">' +
      '<div class="activity-dot ' + e.severity + '"></div>' +
      '<div>' +
        '<div class="msg">' + SH.esc(e.message) + '</div>' +
        '<div class="time">' + SH.timeAgo(e.created_at) + '</div>' +
      '</div>' +
    '</div>';
  }).join('');
}

async function toggleDevice(id, status) {
  try {
    await SH.api('api/devices.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'toggle', id: id, status: status ? 1 : 0 })
    });
    SH.toast('Device updated', 'success');
    setTimeout(loadDashboard, 400);
  } catch (err) {
    SH.toast(err.message, 'error');
    loadDashboard();
  }
}

document.addEventListener('DOMContentLoaded', function () {
  SH.startClock('liveClock');
  SH.initShortcuts();
  loadDashboard();
  setInterval(loadDashboard, 30000);
});