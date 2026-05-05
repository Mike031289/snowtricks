/*
 * SnowTricks App JS
 */

document.addEventListener('DOMContentLoaded', () => {

    // ===== DELETE MODAL =====
    const modal = document.getElementById('deleteModal');

    if (modal) {
        modal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) return;
        });
    }

    // Auto-dismiss flash messages after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // ===== Medias carroussel =====

    window.openImage = function (src) {
        const img = document.getElementById('modalImageContent');
        if (img) img.src = src;
    };

    window.openVideo = function (src) {
        const iframe = document.getElementById('modalVideoContent');
        if (iframe) iframe.src = src;
    };

    const modalVideo = document.getElementById('modalVideo');

    if (modalVideo) {
        modalVideo.addEventListener('hidden.bs.modal', function () {
            const iframe = document.getElementById('modalVideoContent');
            if (iframe) iframe.src = '';
        });
    }

    // ===== COMMENTS TEXT EREA LIMITE (Count caracters max 900) =====
    const textarea = document.querySelector('textarea');
    const counter = document.getElementById('charCount');
    if (textarea && counter) {
        counter.textContent = textarea.value.length;
        textarea.addEventListener('input', () => {
            const length = textarea.value.length;
            counter.textContent = length;
            counter.classList.toggle('text-danger', length > 900);
        });
    }

    // ===== SCROLL BUTTONS =====
    const topBtn = document.getElementById("scrollTopBtn");
    const bottomBtn = document.getElementById("scrollBottomBtn");
    const footer = document.getElementById("siteFooter");

    // safety check
    if (topBtn && bottomBtn) {

        // ===== TOP BUTTON =====
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                topBtn.classList.add("show");
            } else {
                topBtn.classList.remove("show");
            }
        });

        topBtn.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });

        // ===== BOTTOM BUTTON =====
        bottomBtn.classList.add("show"); // visible par défaut

        bottomBtn.addEventListener("click", () => {
            window.scrollTo({
                top: document.documentElement.scrollHeight,
                behavior: "smooth"
            });
        });

        // ===== HIDE BOTTOM BUTTON ON FOOTER =====
        if (footer) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        bottomBtn.classList.remove("show");
                    } else {
                        bottomBtn.classList.add("show");
                    }
                });
            }, {
                threshold: 0.1
            });

            observer.observe(footer);
        }
    }

    // ===== AVATAR =====
    const input = document.getElementById('avatarInput');
    const form = document.getElementById('avatarForm');
    const preview = document.getElementById('avatarPreview');

    if (!input || !form) return;

    input.addEventListener('change', function () {

        console.log('avatar selected');

        const file = this.files[0];
        if (!file) return;

        // Preview
        const reader = new FileReader();
        reader.onload = function (e) {

            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                preview.innerHTML = `<img src="${e.target.result}" class="avatar-lg">`;
            }
        };

        reader.readAsDataURL(file);

        // Auto submit
        form.submit();
    });

});
