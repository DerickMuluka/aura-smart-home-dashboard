window.SH = window.SH || {};

/* =========================================================
   THEME
   ========================================================= */
SH.getTheme = function () {
  return document.documentElement.getAttribute('data-theme') || 'light';
};

SH.themeIcon = function (theme) {
  if (theme === 'dark') {
    return '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
  }
  return '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>';
};

SH.applyTheme = function (theme) {
  document.documentElement.setAttribute('data-theme', theme);
  try { localStorage.setItem('aura-theme', theme); } catch (e) {}
  document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
    btn.innerHTML = SH.themeIcon(theme);
    btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
  });
};

SH.toggleTheme = function () {
  SH.applyTheme(SH.getTheme() === 'dark' ? 'light' : 'dark');
};

/* Sync theme on every page load + wire toggles */
(function () {
  function init() {
    SH.applyTheme(SH.getTheme());
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      // Avoid double-binding
      if (btn.dataset.themeBound === '1') return;
      btn.dataset.themeBound = '1';
      btn.addEventListener('click', SH.toggleTheme);
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

/* =========================================================
   TOAST
   ========================================================= */
SH.toast = function (message, type) {
  type = type || 'default';
  var toast = document.querySelector('.toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.className = 'toast';
    document.body.appendChild(toast);
  }
  toast.className = 'toast ' + (type === 'default' ? '' : type);
  var iconMarkup = type === 'error'
    ? '<path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>'
    : type === 'success'
      ? '<path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>'
      : '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
  toast.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none">' + iconMarkup + '</svg><span>' + SH.esc(message) + '</span>';
  void toast.offsetWidth;
  toast.classList.add('show');
  clearTimeout(SH._toastTimer);
  SH._toastTimer = setTimeout(function () { toast.classList.remove('show'); }, 3000);
};

/* =========================================================
   API  (validates JSON shape, surfaces backend errors)
   ========================================================= */
SH.api = async function (endpoint, options) {
  options = options || {};
  var res;
  try {
    res = await fetch(endpoint, {
      credentials: 'same-origin',
      headers: Object.assign({ 'Content-Type': 'application/json', 'Accept': 'application/json' }, options.headers || {}),
      ...options
    });
  } catch (netErr) {
    throw new Error('Network error. Check your connection.');
  }

  var raw = await res.text();
  var data;
  try {
    data = raw ? JSON.parse(raw) : {};
  } catch (e) {
    // Body isn't JSON. Probably a PHP warning/HTML or session redirect.
    console.error('[SH.api] Non-JSON response from ' + endpoint + ':', raw.slice(0, 300));
    throw new Error('Server returned an invalid response. Check PHP error logs.');
  }

  if (!res.ok) {
    throw new Error(data.error || ('Request failed (' + res.status + ')'));
  }
  if (data && data.error) {
    throw new Error(data.error);
  }
  return data;
};

/* =========================================================
   HELPERS
   ========================================================= */
SH.esc = function (s) {
  var d = document.createElement('div');
  d.textContent = String(s == null ? '' : s);
  return d.innerHTML;
};

SH.timeAgo = function (dateStr) {
  if (!dateStr) return '';
  var t = new Date(String(dateStr).replace(' ', 'T')).getTime();
  if (isNaN(t)) return '';
  var diff = (Date.now() - t) / 1000;
  if (diff < 60) return 'just now';
  if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
  if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
  return Math.floor(diff / 86400) + 'd ago';
};

SH.debounce = function (fn, delay) {
  delay = delay || 250;
  var t;
  return function () {
    var ctx = this, args = arguments;
    clearTimeout(t);
    t = setTimeout(function () { fn.apply(ctx, args); }, delay);
  };
};

SH.startClock = function (elId) {
  var el = document.getElementById(elId);
  if (!el) return;
  function tick() {
    var now = new Date();
    el.textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
  }
  tick();
  setInterval(tick, 15000);
};

SH.confirm = function (title, message) {
  return new Promise(function (resolve) {
    var backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop show';
    backdrop.innerHTML =
      '<div class="modal" style="max-width:380px;">' +
        '<h2>' + SH.esc(title) + '</h2>' +
        '<p style="color:var(--text-muted);font-size:0.88rem;margin-bottom:4px;">' + SH.esc(message) + '</p>' +
        '<div class="modal-actions">' +
          '<button class="btn btn-outline" data-res="no">Cancel</button>' +
          '<button class="btn btn-danger" data-res="yes">Confirm</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(backdrop);
    backdrop.addEventListener('click', function (e) {
      if (e.target === backdrop) { backdrop.remove(); resolve(false); return; }
      var btn = e.target.closest('[data-res]');
      if (!btn) return;
      backdrop.remove();
      resolve(btn.dataset.res === 'yes');
    });
  });
};

SH.initShortcuts = function () {
  document.addEventListener('keydown', function (e) {
    var tag = (e.target.tagName || '').toLowerCase();
    if (tag === 'input' || tag === 'textarea' || tag === 'select') return;
    if (e.key === '/') {
      var s = document.getElementById('searchInput');
      if (s) { e.preventDefault(); s.focus(); }
    }
  });
};