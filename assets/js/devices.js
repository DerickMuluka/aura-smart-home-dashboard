/* Global error guard — surfaces unhandled promise rejections as toasts */
window.addEventListener('unhandledrejection', function (e) {
  console.error('[unhandled]', e.reason);
  if (window.SH && SH.toast) {
    SH.toast((e.reason && e.reason.message) || 'Unexpected error', 'error');
  }
});

var DEV_ICONS = {
  light:      '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7V17h8v-2.3A7 7 0 0012 2z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
  thermostat: '<path d="M14 14.76V4a2 2 0 10-4 0v10.76a4 4 0 104 0z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
  lock:       '<rect x="4" y="11" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 11V7a4 4 0 118 0v4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
  camera:     '<path d="M23 7l-7 5 7 5V7zM14 5H3a2 2 0 00-2 2v10a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
  speaker:    '<path d="M11 5L6 9H2v6h4l5 4V5zM19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
  plug:       '<path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 01-6 6v5M12 17v-0" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
  fan:        '<circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 10c0-3 1-5 1-7M12 14c0 3-1 5-1 7M10 12c-3 0-5 1-7 1M14 12c3 0 5-1 7-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
  sensor:     '<circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 8a6 6 0 000 8M16 8a6 6 0 010 8M5 5a10 10 0 000 14M19 5a10 10 0 010 14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>'
};

function devSvg(path, size) {
  size = size || 20;
  return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 24 24" fill="none">' + path + '</svg>';
}

var allDevices = [];
var rooms = [];

async function init() {
  try {
    var results = await Promise.all([
      SH.api('api/devices.php'),
      SH.api('api/rooms.php')
    ]);
    var devData  = results[0] || {};
    var roomData = results[1] || {};

    allDevices = Array.isArray(devData.devices) ? devData.devices : [];
    rooms      = Array.isArray(roomData.rooms) ? roomData.rooms : [];

    renderRooms();
    renderDevices();
    bindEvents();
  } catch (err) {
    console.error('[devices.init]', err);
    SH.toast(err.message || 'Failed to load devices', 'error');
    var grid = document.getElementById('deviceGrid');
    if (grid) {
      grid.innerHTML = '<div class="device-empty"><h3>Unable to load devices</h3><p>' + SH.esc(err.message || 'Please refresh the page.') + '</p></div>';
    }
  }
}

function bindEvents() {
  var search = document.getElementById('searchInput');
  var filter = document.getElementById('filterType');
  var addBtn = document.getElementById('addDeviceBtn');
  var modal  = document.getElementById('deviceModal');
  var form   = document.getElementById('deviceForm');

  if (search) search.addEventListener('input', SH.debounce(renderDevices, 200));
  if (filter) filter.addEventListener('change', renderDevices);
  if (addBtn) {
    addBtn.addEventListener('click', function () {
      if (modal) modal.classList.add('show');
      populateRoomSelect();
    });
  }
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target.id === 'deviceModal') e.target.classList.remove('show');
    });
  }
  if (form) form.addEventListener('submit', saveDevice);
}

function renderRooms() {
  var tabs = document.getElementById('roomTabs');
  if (!tabs) return;
  tabs.innerHTML = '<button class="room-tab active" data-room="">All Rooms</button>' +
    rooms.map(function (r) {
      return '<button class="room-tab" data-room="' + r.id + '">' + SH.esc(r.name) + '</button>';
    }).join('');

  tabs.querySelectorAll('.room-tab').forEach(function (btn) {
    btn.addEventListener('click', function () {
      tabs.querySelectorAll('.room-tab').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      renderDevices();
    });
  });
}

function renderDevices() {
  var grid = document.getElementById('deviceGrid');
  if (!grid) return;

  var searchEl = document.getElementById('searchInput');
  var filterEl = document.getElementById('filterType');
  var activeTab = document.querySelector('.room-tab.active');

  var search = (searchEl && searchEl.value ? searchEl.value : '').toLowerCase().trim();
  var type   = filterEl ? filterEl.value : '';
  var activeRoom = activeTab ? activeTab.dataset.room : '';

  var filtered = allDevices.filter(function (d) {
    if (search && d.name.toLowerCase().indexOf(search) === -1) return false;
    if (type && d.type !== type) return false;
    if (activeRoom && String(d.room_id) !== activeRoom) return false;
    return true;
  });

  if (!filtered.length) {
    grid.innerHTML =
      '<div class="device-empty">' +
        '<h3>No devices match your filters</h3>' +
        '<p>Try adjusting the search or adding a new device.</p>' +
      '</div>';
    return;
  }

  grid.innerHTML = filtered.map(function (d, i) {
    var on = d.status == 1;
    var smallClass = (d.type === 'lock' || d.type === 'camera' || d.type === 'speaker') ? 'small' : '';
    return '<div class="device-card ' + (on ? 'on' : '') + ' stagger" style="animation-delay:' + (i * 40) + 'ms">' +
      '<div class="device-card-head">' +
        '<div class="device-icon ' + d.type + '">' + devSvg(DEV_ICONS[d.type] || DEV_ICONS.light, 22) + '</div>' +
        '<label class="switch">' +
          '<input type="checkbox" ' + (on ? 'checked' : '') + ' data-id="' + d.id + '">' +
          '<span class="slider"></span>' +
        '</label>' +
      '</div>' +
      '<h3>' + SH.esc(d.name) + '</h3>' +
      '<div class="room-tag">' + SH.esc(d.room_name || 'Unassigned') + '</div>' +
      '<div class="value-big ' + smallClass + '">' + formatValue(d) + '</div>' +
      '<div class="status-row">' +
        '<span class="status-text">' + (on ? 'Active' : 'Inactive') + '</span>' +
        '<button class="remove-btn" data-del="' + d.id + '" aria-label="Remove device">' +
          '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
        '</button>' +
      '</div>' +
    '</div>';
  }).join('');

  grid.querySelectorAll('input[type=checkbox]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      toggleDevice(Number(cb.dataset.id), cb.checked);
    });
  });
  grid.querySelectorAll('[data-del]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      deleteDevice(Number(btn.dataset.del));
    });
  });
}

function formatValue(d) {
  if (d.type === 'thermostat') return d.value ? d.value + '\u00B0C' : '\u2014';
  if (d.type === 'lock')       return d.status == 1 ? 'Locked' : 'Unlocked';
  if (d.type === 'camera')     return d.status == 1 ? 'Recording' : 'Standby';
  if (d.type === 'speaker')    return d.status == 1 ? 'Playing' : 'Idle';
  if (d.type === 'plug')       return (d.power_draw || 0) + ' W';
  if (d.type === 'sensor')     return d.value || '\u2014';
  return d.status == 1 ? 'On' : 'Off';
}

async function toggleDevice(id, status) {
  try {
    await SH.api('api/devices.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'toggle', id: id, status: status ? 1 : 0 })
    });
    var dev = allDevices.find(function (d) { return d.id == id; });
    if (dev) dev.status = status ? 1 : 0;
    renderDevices();
    SH.toast((dev ? dev.name : 'Device') + (status ? ' turned on' : ' turned off'), 'success');
  } catch (err) {
    SH.toast(err.message, 'error');
    renderDevices();
  }
}

async function deleteDevice(id) {
  var dev = allDevices.find(function (d) { return d.id == id; });
  var ok = await SH.confirm('Remove device?', '"' + (dev ? dev.name : 'This device') + '" will be permanently removed.');
  if (!ok) return;

  try {
    await SH.api('api/devices.php', {
      method: 'DELETE',
      body: JSON.stringify({ id: id })
    });
    allDevices = allDevices.filter(function (d) { return d.id != id; });
    renderDevices();
    SH.toast('Device removed', 'success');
  } catch (err) {
    SH.toast(err.message, 'error');
  }
}

function populateRoomSelect() {
  var sel = document.getElementById('deviceRoom');
  if (!sel) return;
  sel.innerHTML = '<option value="">No room</option>' +
    rooms.map(function (r) {
      return '<option value="' + r.id + '">' + SH.esc(r.name) + '</option>';
    }).join('');
}

async function saveDevice(e) {
  e.preventDefault();
  var form = e.target;
  var data = Object.fromEntries(new FormData(form).entries());
  data.action = 'create';

  try {
    var res = await SH.api('api/devices.php', {
      method: 'POST',
      body: JSON.stringify(data)
    });
    if (res && res.device) {
      allDevices.unshift(res.device);
      renderDevices();
    }
    form.reset();
    var modal = document.getElementById('deviceModal');
    if (modal) modal.classList.remove('show');
    SH.toast('Device added', 'success');
  } catch (err) {
    SH.toast(err.message, 'error');
  }
}

document.addEventListener('DOMContentLoaded', init);