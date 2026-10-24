// Port of follow() in RegisterRow.tsx: the hover image drifts with the pointer.
// One listener on document, so nothing needs cleaning up across wire:navigate.
document.addEventListener('mousemove', (e) => {
    const row = e.target.closest?.('[data-register-row]');
    if (!row) return;

    const r = row.getBoundingClientRect();
    row.style.setProperty('--mx', (e.clientX - r.left) / r.width - 0.5);
    row.style.setProperty('--my', (e.clientY - r.top) / r.height - 0.5);
});
