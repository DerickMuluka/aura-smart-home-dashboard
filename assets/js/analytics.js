/* Global error guard */
window.addEventListener('unhandledrejection', function (e) {
  console.error('[unhandled]', e.reason);
  if (window.SH && SH.toast) {
    SH.toast((e.reason && e.reason.message) || 'Unexpected error', 'error');
  }
});

var analyticsData = null;

async function init() {
  try {
    var data = await SH.api('api/analytics.php');
    if (!data || typeof data !== 'object') {
      throw new Error('Empty analytics response');
    }
    analyticsData = data;

    renderSummary(analyticsData);
    renderBarChart(Array.isArray(analyticsData.daily) ? analyticsData.daily : []);
    renderDonut(Array.isArray(analyticsData.breakdown) ? analyticsData.breakdown : []);
    renderEvents(Array.isArray(analyticsData.events) ? analyticsData.events : []);
    bindExport();
  } catch (err) {
    console.error('[analytics.init]', err);
    SH.toast(err.message || 'Failed to load analytics', 'error');

    var summary = document.getElementById('summaryRow');
    if (summary) {
      summary.innerHTML =
        '<div class="summary-card" style="grid-column:1/-1;text-align:center;">' +
          '<div class="lbl">Error</div>' +
          '<div class="val" style="font-size:1rem;color:var(--accent-rose);">' + SH.esc(err.message || 'Unable to load analytics') + '</div>' +
        '</div>';
    }
    var tbody = document.getElementById('eventBody');
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="3" class="table-empty">Unable to load data.</td></tr>';
    }
  }
}

function renderSummary(data) {
  var wrap = document.getElementById('summaryRow');
  if (!wrap) return;

  var daily = Array.isArray(data.daily) ? data.daily : [];
  var events = Array.isArray(data.events) ? data.events : [];

  var total = daily.reduce(function (s, d) { return s + Number(d.kwh || 0); }, 0);
  var avg = (total / (daily.length || 1)).toFixed(1);
  var today = daily.length ? Number(daily[daily.length - 1].kwh || 0) : 0;
  var peak = daily.reduce(function (a, b) {
    return (Number(a.kwh) > Number(b.kwh)) ? a : b;
  }, daily[0] || { kwh: 0, day: '\u2014' });

  wrap.innerHTML =
    '<div class="summary-card stagger">' +
      '<div class="lbl"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/></svg> Today</div>' +
      '<div class="val">' + today + ' <small style="font-size:0.8rem;color:var(--text-muted);font-weight:500;">kWh</small></div>' +
      '<div class="sub">Current day usage</div>' +
    '</div>' +
    '<div class="summary-card stagger" style="animation-delay:80ms">' +
      '<div class="lbl"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M3 20h18M6 16v-5M11 16V8M16 16v-9M21 16V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> 7-day Total</div>' +
      '<div class="val">' + total.toFixed(1) + ' <small style="font-size:0.8rem;color:var(--text-muted);font-weight:500;">kWh</small></div>' +
      '<div class="sub">Average ' + avg + ' kWh/day</div>' +
    '</div>' +
    '<div class="summary-card stagger" style="animation-delay:160ms">' +
      '<div class="lbl"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M3 3v18h18M7 14l4-4 3 3 5-6" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg> Peak Day</div>' +
      '<div class="val">' + SH.esc(peak.day || '\u2014') + '</div>' +
      '<div class="sub">' + (peak.kwh || 0) + ' kWh</div>' +
    '</div>';
}

function renderBarChart(daily) {
  var chart = document.getElementById('barChart');
  if (!chart) return;
  if (!daily.length) {
    chart.innerHTML = '<p style="color:var(--text-muted);font-size:0.85rem;padding:40px 0;text-align:center;">No energy data available.</p>';
    return;
  }
  var max = Math.max.apply(null, daily.map(function (d) { return Number(d.kwh) || 0; }));
  if (max <= 0) max = 1;

  chart.innerHTML = daily.map(function (d, i) {
    var h = (Number(d.kwh) / max) * 100;
    return '<div class="bar-wrap">' +
      '<div class="bar" style="height:' + h + '%;animation-delay:' + (i * 60) + 'ms" data-val="' + d.kwh + ' kWh"></div>' +
      '<span class="bar-label">' + SH.esc(d.day) + '</span>' +
    '</div>';
  }).join('');
}

function renderDonut(breakdown) {
  var donut  = document.getElementById('donut');
  var legend = document.getElementById('legend');
  if (!donut) return;

  if (!breakdown.length) {
    donut.style.background = 'conic-gradient(var(--surface-2) 0 360deg)';
    donut.innerHTML = '<div class="donut-center">0<small>no data</small></div>';
    if (legend) legend.innerHTML = '';
    return;
  }

  var colors = ['#14b8a6', '#6366f1', '#f59e0b', '#f43f5e', '#06b6d4', '#84cc16'];
  var total = breakdown.reduce(function (s, b) { return s + (Number(b.value) || 0); }, 0) || 1;
  var angle = 0;
  var stops = breakdown.map(function (b, i) {
    var start = angle;
    angle += (Number(b.value) / total) * 360;
    return colors[i % colors.length] + ' ' + start + 'deg ' + angle + 'deg';
  });

  donut.style.background = 'conic-gradient(' + stops.join(',') + ')';
  donut.innerHTML = '<div class="donut-center">' + Math.round(total) + '<small>total units</small></div>';

  if (legend) {
    legend.innerHTML = breakdown.map(function (b, i) {
      var pct = Math.round((Number(b.value) / total) * 100);
      return '<div class="legend-item">' +
        '<span class="legend-dot" style="background:' + colors[i % colors.length] + '"></span>' +
        '<span>' + SH.esc(b.label) + '</span>' +
        '<span class="pct">' + pct + '%</span>' +
      '</div>';
    }).join('');
  }
}

function renderEvents(events) {
  var tbody = document.getElementById('eventBody');
  if (!tbody) return;

  if (!events.length) {
    tbody.innerHTML = '<tr><td colspan="3" class="table-empty">No events logged yet.</td></tr>';
    return;
  }

  tbody.innerHTML = events.map(function (e) {
    return '<tr>' +
      '<td><span class="severity ' + SH.esc(e.severity) + '">' + SH.esc(e.severity) + '</span></td>' +
      '<td>' + SH.esc(e.message) + '</td>' +
      '<td style="color:var(--text-muted);">' + SH.timeAgo(e.created_at) + '</td>' +
    '</tr>';
  }).join('');
}

function bindExport() {
  var btn = document.getElementById('exportBtn');
  if (!btn) return;
  if (btn.dataset.bound === '1') return;
  btn.dataset.bound = '1';

  btn.addEventListener('click', function () {
    if (!analyticsData) return;

    var rows = [['Day', 'kWh']];
    (analyticsData.daily || []).forEach(function (d) { rows.push([d.day, d.kwh]); });
    rows.push([]);
    rows.push(['Event', 'Severity', 'When']);
    (analyticsData.events || []).forEach(function (e) { rows.push([e.message, e.severity, e.created_at]); });

    var csv = rows.map(function (r) {
      return r.map(function (v) {
        return '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"';
      }).join(',');
    }).join('\n');

    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'aura-analytics-' + new Date().toISOString().slice(0, 10) + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    SH.toast('Analytics exported', 'success');
  });
}

document.addEventListener('DOMContentLoaded', init);