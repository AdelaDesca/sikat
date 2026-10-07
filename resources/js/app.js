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

    // Tampilkan / sembunyikan password: tombol dengan data-toggle-password="id-input".
    const toggle = event.target.closest('[data-toggle-password]');
    if (toggle) {
        const input = document.getElementById(toggle.dataset.togglePassword);
        const terlihat = input.type === 'password';
        input.type = terlihat ? 'text' : 'password';
        toggle.querySelector('[data-icon="show"]').classList.toggle('hidden', terlihat);
        toggle.querySelector('[data-icon="hide"]').classList.toggle('hidden', !terlihat);
        toggle.title = toggle.ariaLabel = terlihat ? 'Sembunyikan password' : 'Lihat password';
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

// Saat modal ditutup (Batal, ✕, klik luar, Esc), kosongkan isian & pesan error
// agar modal berikutnya dibuka dalam keadaan bersih. Nilai bawaan diambil dari data-reset.
document.addEventListener('close', (event) => {
    const dialog = event.target;
    if (!(dialog instanceof HTMLDialogElement)) return;

    dialog.querySelectorAll('input:not([type=hidden]), textarea, select').forEach((el) => {
        el.value = el.dataset.reset ?? '';
    });
    dialog.querySelectorAll('.form-error').forEach((el) => el.remove());
}, true);

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

// Pencarian langsung saat mengetik: form data-live-search="id-container" memuat ulang isi container.
let jedaCari;
let permintaanCari;
document.addEventListener('input', (event) => {
    const form = event.target.closest('form[data-live-search]');
    // Hanya kolom ketik; tanggal & pilihan dropdown sudah memuat ulang halaman sendiri.
    if (!form || !['search', 'text'].includes(event.target.type)) return;

    clearTimeout(jedaCari);
    jedaCari = setTimeout(async () => {
        const url = new URL(form.action);
        url.search = new URLSearchParams(new FormData(form)).toString();

        permintaanCari?.abort();
        permintaanCari = new AbortController();
        try {
            const res = await fetch(url, { signal: permintaanCari.signal });
            const html = new DOMParser().parseFromString(await res.text(), 'text/html');
            const id = form.dataset.liveSearch;
            document.getElementById(id).replaceWith(html.getElementById(id));
            history.replaceState(null, '', url);
        } catch (e) {
            if (e.name !== 'AbortError') form.submit();
        }
    }, 300);
});

// Buka kembali modal yang gagal validasi setelah reload.
document.addEventListener('DOMContentLoaded', () => {
    const id = document.body.dataset.reopen;
    if (id) document.getElementById(id)?.showModal();

    // Sembunyikan notifikasi flash otomatis.
    document.querySelectorAll('[data-flash]').forEach((el) => {
        setTimeout(() => el.remove(), 5000);
    });
});
