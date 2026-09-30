// Buka / tutup modal <dialog>: tombol dengan data-open="id-modal" dan data-close.
document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-open]');
    if (opener) {
        const dialog = document.getElementById(opener.dataset.open);
        if (dialog) {
            fillForm(dialog, opener.dataset);
            dialog.showModal();
        }
        return;
    }

    if (event.target.closest('[data-close]')) {
        event.target.closest('dialog')?.close();
        return;
    }

    // Klik di luar kotak modal (area backdrop) menutup modal.
    if (event.target instanceof HTMLDialogElement) {
        event.target.close();
    }

    // Tutup dropdown notifikasi jika klik di luar.
    document.querySelectorAll('details[data-dropdown][open]').forEach((el) => {
        if (!el.contains(event.target)) el.removeAttribute('open');
    });
});

// Isi form di dalam modal dari atribut data-* tombol pembuka,
// mis. data-field-nama="Kertas" mengisi input name="nama", data-action mengganti action form.
function fillForm(dialog, data) {
    const form = dialog.querySelector('form');
    if (!form) return;

    if (data.action) form.action = data.action;

    Object.entries(data).forEach(([key, value]) => {
        if (!key.startsWith('field')) return;
        const name = key.slice(5).replace(/^./, (c) => c.toLowerCase()).replace(/[A-Z]/g, (c) => '_' + c.toLowerCase());
        const input = form.elements.namedItem(name);
        if (input) input.value = value;
    });

    Object.entries(data).forEach(([key, value]) => {
        if (!key.startsWith('text')) return;
        const target = dialog.querySelector(`[data-slot="${key.slice(4).toLowerCase()}"]`);
        if (target) target.textContent = value;
    });
}

// Buka kembali modal yang gagal validasi setelah reload.
document.addEventListener('DOMContentLoaded', () => {
    const id = document.body.dataset.reopen;
    if (id) document.getElementById(id)?.showModal();

    // Sembunyikan notifikasi flash otomatis.
    document.querySelectorAll('[data-flash]').forEach((el) => {
        setTimeout(() => el.remove(), 5000);
    });
});
