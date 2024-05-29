/**
 * Front-end JavaScript
 *
 * The JavaScript code you place here will be processed by esbuild. The output
 * file will be created at `../theme/js/script.min.js` and enqueued in
 * `../theme/functions.php`.
 *
 * For esbuild documentation, please see:
 * https://esbuild.github.io/
 */

import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start()

window.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('.ssnail-icon').forEach((icon) => {
        let svgDataUrl = getComputedStyle(icon).getPropertyValue('--ssnail-icon-svg').trim().replace(/['"]+/g, '');
        if (svgDataUrl) {
            const svgData = atob(svgDataUrl.split(',')[1]);
            icon.innerHTML = svgData;
        }
    });
});