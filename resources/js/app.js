import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.Chart = Chart;
window.createIcons = () => createIcons({ icons });

// Global Real-time Clock Component
window.realtimeHeaderClock = function() {
    return {
        dateString: '',
        timeString: '',
        init() {
            this.tick();
            setInterval(() => this.tick(), 1000);
        },
        tick() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            const dayName = days[now.getDay()];
            const date = String(now.getDate()).padStart(2, '0');
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            this.dateString = `${dayName}, ${date} ${monthName} ${year}`;
            this.timeString = `${hours}:${minutes}:${seconds}`;
        }
    };
};

document.addEventListener('DOMContentLoaded', () => {
    window.createIcons();
});

// Re-init lucide after Alpine morphs or dynamically inserts DOM
document.addEventListener('alpine:initialized', () => {
    window.createIcons();
});

Alpine.start();
