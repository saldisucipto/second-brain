import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
// FullCalendar
import { Calendar } from '@fullcalendar/core';



document.addEventListener('alpine:init', () => {
    Alpine.store('requestLoading', {
        pending: 0,
        get active() {
            return this.pending > 0;
        },
        start() {
            this.pending += 1;
        },
        stop() {
            this.pending = Math.max(0, this.pending - 1);
        }
    });
});

window.addEventListener('app:loading:start', () => {
    const loadingStore = window.Alpine?.store('requestLoading');
    loadingStore?.start();
});

window.addEventListener('app:loading:stop', () => {
    const loadingStore = window.Alpine?.store('requestLoading');
    loadingStore?.stop();
});

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;

Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (event.defaultPrevented) {
            return;
        }

        if (form.dataset.loading === 'off') {
            return;
        }

        const method = (form.getAttribute('method') || 'get').toLowerCase();
        if (method === 'get') {
            return;
        }

        window.dispatchEvent(new CustomEvent('app:loading:start'));
    });

    // Map imports
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    // Chart imports
    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then(module => module.initChartThree());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then(module => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then(module => module.initChartEight());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-13').then(module => module.initChartThirteen());
    }

    // Calendar init
    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then(module => module.calendarInit());
    }
});
