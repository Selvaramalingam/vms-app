import './bootstrap';

import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        }
    });
});

window.Alpine = Alpine;

Alpine.start();
