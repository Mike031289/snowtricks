/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';

/*
 * SnowTricks App JS
 */
// delete Trick modal system
document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('deleteModal');

    if (!modal) return;

    modal.addEventListener('show.bs.modal', (event) => {

        const button = event.relatedTarget;
        if (!button) return;

    });

});