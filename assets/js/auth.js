document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('authForm');
  if (!form) return;

  const errorBox = document.getElementById('authError');
  const pwInput = form.querySelector('input[name="password"]');

  /* ---- Password visibility toggle ---- */
  document.querySelectorAll('.toggle-pw').forEach((btn) => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.target);
      if (!target) return;
      const show = target.type === 'password';
      target.type = show ? 'text' : 'password';
      btn.innerHTML = show
        ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19M1 1l22 22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>'
        : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="2" fill="none"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/></svg>';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });

  /* ---- Password strength meter (register only) ---- */
  const strengthWrap = document.getElementById('pwStrength');
  if (strengthWrap && pwInput) {
    const fill = strengthWrap.querySelector('.pw-strength-fill');
    const label = strengthWrap.querySelector('.pw-strength-label');
    pwInput.addEventListener('input', () => {
      const v = pwInput.value;
      if (!v) { strengthWrap.classList.remove('show'); return; }
      strengthWrap.classList.add('show');
      let score = 0;
      if (v.length >= 6) score++;
      if (v.length >= 10) score++;
      if (/[A-Z]/.test(v)) score++;
      if (/[0-9]/.test(v)) score++;
      if (/[^A-Za-z0-9]/.test(v)) score++;
      const pct = (score / 5) * 100;
      const colors = ['#f43f5e', '#f43f5e', '#f59e0b', '#84cc16', '#14b8a6'];
      const labels = ['Too short', 'Weak', 'Fair', 'Good', 'Strong'];
      fill.style.width = pct + '%';
      fill.style.background = colors[Math.max(0, score - 1)];
      label.textContent = labels[Math.max(0, score - 1)];
    });
  }

  /* ---- Submit ---- */
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type=submit]');
    const original = btn.innerHTML;
    btn.innerHTML = '<span style="opacity:0.7;">Processing...</span>';
    btn.disabled = true;
    if (errorBox) errorBox.classList.remove('show');

    try {
      const data = Object.fromEntries(new FormData(form).entries());
      const result = await SH.api(form.dataset.endpoint, {
        method: 'POST',
        body: JSON.stringify(data),
      });
      if (result.redirect) {
        btn.innerHTML = 'Success';
        window.location.href = result.redirect;
      }
    } catch (err) {
      if (errorBox) {
        errorBox.textContent = err.message;
        errorBox.classList.add('show');
      }
      btn.innerHTML = original;
      btn.disabled = false;
    }
  });
});