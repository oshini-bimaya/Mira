document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('artSearch');
  const tiles = [...document.querySelectorAll('.mira-live-pin')];
  const empty = document.getElementById('noResults');
  if (!input || !empty) return;
  input.addEventListener('input', () => {
    const text = input.value.trim().toLocaleLowerCase();
    let visible = 0;
    tiles.forEach(tile => { const matches = (tile.dataset.search || '').includes(text); tile.hidden = !matches; if(matches) visible++; });
    empty.hidden = visible > 0;
    if (visible === 0) empty.textContent = text ? 'No artworks match your search.' : 'No approved artworks yet.';
  });
});
