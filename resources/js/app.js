import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/* ================= Toast Notifikasi (kanan atas) ================= */
function ensureToastContainer() {
    let container = document.getElementById('jk-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'jk-toast-container';
        container.className = 'jk-toast-container';
        document.body.appendChild(container);
    }

    const header = document.querySelector('.shop-header, .topbar');
    if (header) {
        const rect = header.getBoundingClientRect();
        container.style.top = Math.max(12, rect.bottom + 10) + 'px';
    }

    return container;
}

window.jkToast = function (type, message) {
    const container = ensureToastContainer();

    // Buang notifikasi lama dulu — pastikan cuma 1 yang tampil sekaligus
    container.querySelectorAll('.jk-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `jk-toast jk-toast-${type}`;
    toast.innerHTML = `<span class="dot"></span><span class="jk-toast-msg"></span>`;
    toast.querySelector('.jk-toast-msg').textContent = message;
    container.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('show'));

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
};

/* ================= AJAX Tambah ke Keranjang ================= */
document.addEventListener('submit', async function (e) {
    const form = e.target.closest('.add-to-cart-form');
    if (!form) return;

    e.preventDefault();
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: new FormData(form),
        });
        const data = await response.json();

        if (data.success) {
            window.jkToast(data.type || 'success', data.message);
            document.querySelectorAll('.cart-count').forEach(el => (el.textContent = data.cartCount));
        } else {
            window.jkToast(data.type || 'error', data.message);
        }
    } catch (err) {
        window.jkToast('error', 'Gagal menambahkan ke keranjang. Coba lagi.');
    } finally {
        button.disabled = false;
    }
});

/* ================= Lightbox Galeri Foto Produk ================= */
let lightboxImages = [];
let lightboxIndex = 0;

function renderLightbox() {
    const img = document.getElementById('jk-lightbox-img');
    const counter = document.getElementById('jk-lightbox-counter');
    if (!img || !lightboxImages.length) return;
    img.src = lightboxImages[lightboxIndex];
    counter.textContent = `${lightboxIndex + 1} / ${lightboxImages.length}`;
}

window.jkOpenGallery = function (images) {
    lightboxImages = images;
    lightboxIndex = 0;
    const modal = document.getElementById('jk-lightbox');
    if (!modal) return;
    renderLightbox();
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
};

window.jkCloseGallery = function () {
    const modal = document.getElementById('jk-lightbox');
    if (!modal) return;
    modal.classList.remove('open');
    document.body.style.overflow = '';
};

window.jkNextImage = function () {
    if (!lightboxImages.length) return;
    lightboxIndex = (lightboxIndex + 1) % lightboxImages.length;
    renderLightbox();
};

window.jkPrevImage = function () {
    if (!lightboxImages.length) return;
    lightboxIndex = (lightboxIndex - 1 + lightboxImages.length) % lightboxImages.length;
    renderLightbox();
};

window.jkToggleMenu = function () {
    const menu = document.getElementById('jk-mobile-menu');
    if (menu) menu.classList.toggle('open');
};

window.jkToggleSidebar = function () {
    document.getElementById('jk-sidebar')?.classList.toggle('open');
    document.getElementById('jk-sidebar-backdrop')?.classList.toggle('open');
};

function jkMakeTablesResponsive() {
    document.querySelectorAll('.panel > table').forEach(function (table) {
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        if (!headers.length) return;

        table.querySelectorAll('tbody tr').forEach(function (tr) {
            if (tr.children.length <= 1) return; // lewati baris "belum ada data"

            Array.from(tr.children).forEach(function (td, i) {
                if (headers[i]) td.setAttribute('data-label', headers[i]);
            });

            const firstCell = tr.children[0];
            if (firstCell && !firstCell.querySelector('.jk-row-toggle')) {
                const toggle = document.createElement('span');
                toggle.className = 'jk-row-toggle';
                toggle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"></polyline></svg>';
                firstCell.prepend(toggle);
            }

            tr.addEventListener('click', function (e) {
                if (window.innerWidth > 760) return; // hanya aktif di mobile
                if (e.target.closest('a, button, form')) return; // jangan toggle saat klik tombol Ubah/Hapus
                tr.classList.toggle('jk-expanded');
            });
        });
    });
}
document.addEventListener('DOMContentLoaded', jkMakeTablesResponsive);

function jkInitCombobox(root) {
    const input = root.querySelector('.combobox-input');
    const hidden = root.querySelector('input[type="hidden"]');
    const panel = root.querySelector('.combobox-panel');
    const options = Array.from(panel.querySelectorAll('.combobox-option'));

    function filter() {
        const q = input.value.trim().toLowerCase();
        options.forEach(opt => {
            opt.style.display = opt.dataset.label.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    input.addEventListener('input', () => {
        hidden.value = '';
        filter();
        panel.classList.add('open');
    });

    input.addEventListener('focus', () => {
        filter();
        panel.classList.add('open');
    });

    document.addEventListener('click', (e) => {
        if (!root.contains(e.target)) panel.classList.remove('open');
    });

    options.forEach(opt => {
        opt.addEventListener('click', () => {
            hidden.value = opt.dataset.id;
            input.value = opt.dataset.label;
            panel.classList.remove('open');

            // Beritahu halaman yang butuh data tambahan dari opsi yang
            // dipilih (mis. harga & stok produk), tanpa membuat komponen
            // combobox ini terikat ke kebutuhan satu halaman tertentu.
            root.dispatchEvent(new CustomEvent('combobox:select', { detail: { ...opt.dataset }, bubbles: true }));
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.jk-combobox').forEach(jkInitCombobox);
});