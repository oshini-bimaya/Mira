(() => {
 const form = document.getElementById('loginForm');
 const password = document.getElementById('password');
 const show = document.getElementById('showPassword');
 const status = document.getElementById('loginStatus');
 if (!form) return;
 show?.addEventListener('click', () => { const visible = password.type === 'password'; password.type = visible ? 'text' : 'password'; show.textContent = visible ? 'Hide' : 'Show'; show.setAttribute('aria-pressed', String(visible)); });
 form.addEventListener('submit', async (event) => {
  event.preventDefault(); status.textContent = 'Logging in...';
  const button = form.querySelector('[type="submit"]'); if (button) button.disabled = true;
  try {
   const response = await fetch('login.php', {method:'POST',body:new FormData(form),credentials:'same-origin'});
   const data = await response.json(); status.textContent = data.message || 'Unable to log in.';
   if (response.ok && data.success && ['admin/dashboard.php','user-home.php'].includes(data.redirect)) window.location.assign(data.redirect);
  } catch (error) { status.textContent = 'Unable to log in. Please try again.'; }
  finally { if (button) button.disabled = false; }
 });
})();
