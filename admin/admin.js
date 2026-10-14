(() => {
 const btn=document.getElementById('themeToggle');
 function sync(){ const dark=document.documentElement.dataset.theme==='dark'; if(btn){btn.textContent=dark?'☀  Light mode':'☾  Dark mode';btn.setAttribute('aria-pressed',String(dark));} }
 btn?.addEventListener('click',()=>{const next=document.documentElement.dataset.theme==='dark'?'light':'dark';document.documentElement.dataset.theme=next;try{localStorage.setItem('mira-theme',next);}catch(e){} sync();});
 sync();
})();
