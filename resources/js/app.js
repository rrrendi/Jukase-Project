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
    return container;
}

window.jkToast = function (type, message) {
    const container = ensureToastContainer();
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