/* Global error guard */
window.addEventListener('unhandledrejection', function (e) {
  console.error('[unhandled]', e.reason);
  if (window.SH && SH.toast) {
    SH.toast((e.reason && e.reason.message) || 'Unexpected error', 'error');
  }
});

var automations = [];
var devices = [];

async function init() {
  try {
    var results = await Promise.all([
      SH.api('api/automation.php'),
      SH.api('api/devices.php')
    ]);
    var autoData = results[0] || {};
    var devData  = results[1] || {};

    automations = Array.isArray(autoData.automations) ? autoData.automations : [];
    devices     = Array.isArray(devData.devices) ? devData.devices : [];

    renderStats();
    renderAutomations();
    bindEvents();
  } catch (err) {
    console.error('[automation.init]', err);
    SH.toast(err.message || 'Failed to load automations', 'error');
    var list = document.getElementById('autoList');
    if (list) {
      list.innerHTML =
        '<div class="auto-empty">' +
          '<h3>Unable to load automations</h3>' +
          '<p>' + SH.esc(err.message || 'Please refresh the page.') + '</p>' +
        '</div>';
    }
  }
}

function renderStats() {
  var wrap = document.getElementById('autoStats');
  if (!wrap) return;
  var active = automations.filter(function (a) { return a.enabled == 1; }).length;
  wrap.innerHTML =
    '<div class="auto-stat"><div class="num">' + automations.length + '</div><div class="lbl">Total</div></div>' +
    '<div class="auto-stat"><div class="num" style="color:var(--accent-teal)">' + active + '</div><div class="lbl">Active</div></div>' +
    '<div class="auto-stat"><div class="num" style="color:var(--text-muted)">' + (automations.length - active) + '</div><div class="lbl">Paused</div></div>';
}

function bindEvents() {
  var addBtn = document.getElementById('addAutoBtn');
  var modal  = document.getElementById('autoModal');
  var form   = document.getElementById('autoForm');

  if (addBtn) {
    addBtn.addEventListener('click', function () {
      if (!devices.length) {
        SH.toast('Add at least one device first', 'error');
        return;
      }
      populateDeviceSelect();
      if (modal) modal.classList.add('show');
    });
  }
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target.id === 'autoModal') e.target.classList.remove('show');
    });
  }
  if (form) form.addEventListener('submit', saveAutomation);
}

function renderAutomations() {
  var list = document.getElementById('autoList');
  if (!list) return;

  if (!automations.length) {
    list.innerHTML =
      '<div class="auto-empty">' +
        '<h3>No automations yet</h3>' +
        '<p>Create a routine to run your devices automatically.</p>' +
        '<button class="btn btn-primary" id="emptyAddBtn">Create your first automation</button>' +
      '</div>';
    var emptyBtn = document.getElementById('emptyAddBtn');
    if (emptyBtn) {
      emptyBtn.addEventListener('click', function () {
        var addBtn = document.getElementById('addAutoBtn');
        if (addBtn) addBtn.click();
      });
    }
    return;
  }

  list.innerHTML = automations.map(function (a, i) {
    return '<div class="auto-card stagger ' + (a.enabled == 1 ? '' : 'disabled') + '" style="animation-delay:' + (i * 50) + 'ms">' +
      '<div class="auto-icon">' +
        '<svg width="22" height="22" viewBox="0 0 24 24" fill="none">' +
          '<circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/>' +
          '<path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1l2.1-2.1M17 7l2.1-2.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
        '</svg>' +
      '</div>' +
      '<div class="auto-info">' +
        '<h3>' + SH.esc(a.name) + '</h3>' +
        '<div class="auto-flow">' +
          '<span class="chip trigger">' + SH.esc(a.trigger_type) + (a.trigger_value ? ': ' + SH.esc(a.trigger_value) : '') + '</span>' +
          '<span class="arrow">&rarr;</span>' +
          '<span class="chip action">' + SH.esc(a.action_device_name || 'No device') + (a.action_value ? ': ' + SH.esc(a.action_value) : '') + '</span>' +
        '</div>' +
      '</div>' +
      '<div class="auto-actions">' +
        '<label class="switch">' +
          '<input type="checkbox" ' + (a.enabled == 1 ? 'checked' : '') + ' data-toggle="' + a.id + '">' +
          '<span class="slider"></span>' +
        '</label>' +
      '</div>' +
    '</div>';
  }).join('');

  list.querySelectorAll('[data-toggle]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      toggleAutomation(Number(cb.dataset.toggle), cb.checked);
    });
  });
}

async function toggleAutomation(id, enabled) {
  try {
    await SH.api('api/automation.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'toggle', id: id, enabled: enabled ? 1 : 0 })
    });
    var a = automations.find(function (x) { return x.id == id; });
    if (a) a.enabled = enabled ? 1 : 0;
    renderStats();
    renderAutomations();
    SH.toast('Automation ' + (enabled ? 'enabled' : 'paused'), 'success');
  } catch (err) {
    SH.toast(err.message, 'error');
    renderAutomations();
  }
}

function populateDeviceSelect() {
  var sel = document.getElementById('autoDevice');
  if (!sel) return;
  sel.innerHTML = devices.map(function (d) {
    return '<option value="' + d.id + '">' + SH.esc(d.name) + '</option>';
  }).join('');
}

async function saveAutomation(e) {
  e.preventDefault();
  var form = e.target;
  var data = Object.fromEntries(new FormData(form).entries());
  data.action = 'create';

  try {
    var res = await SH.api('api/automation.php', {
      method: 'POST',
      body: JSON.stringify(data)
    });
    if (res && res.automation) {
      automations.unshift(res.automation);
      renderStats();
      renderAutomations();
    }
    form.reset();
    var modal = document.getElementById('autoModal');
    if (modal) modal.classList.remove('show');
    SH.toast('Automation created', 'success');
  } catch (err) {
    SH.toast(err.message, 'error');
  }
}

document.addEventListener('DOMContentLoaded', init);