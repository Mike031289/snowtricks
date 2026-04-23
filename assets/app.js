/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import './styles/app.mobile.css';

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