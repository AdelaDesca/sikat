// Grafik halaman Report (Chart.js). Data dikirim dari server lewat <script id="data-report">.
import { ArcElement, BarController, BarElement, CategoryScale, Chart, DoughnutController, LinearScale, Tooltip } from 'chart.js';

Chart.register(ArcElement, BarController, BarElement, CategoryScale, DoughnutController, LinearScale, Tooltip);

const TINTA = '#0f172a';
const TINTA_SEKUNDER = '#475569';
const GARIS = '#e2e8f0';
const PERMUKAAN = '#ffffff';

document.addEventListener('DOMContentLoaded', () => {
    const sumber = document.getElementById('data-report');
    if (!sumber) return;
    const data = JSON.parse(sumber.textContent);

    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.color = TINTA_SEKUNDER;

    const tooltip = {
        backgroundColor: TINTA,
        titleColor: '#ffffff',
        bodyColor: '#ffffff',
        padding: 10,
        cornerRadius: 8,
        boxPadding: 4,
        usePointStyle: true,
    };

    // Donat komposisi status stok.
    const total = data.status.reduce((n, s) => n + s.jumlah, 0);
    new Chart(document.getElementById('grafik-status'), {
        type: 'doughnut',
        data: {
            labels: data.status.map((s) => s.label),
            datasets: [{
                data: data.status.map((s) => s.jumlah),
                backgroundColor: data.status.map((s) => s.warna),
                borderColor: PERMUKAAN,
                borderWidth: 2,
                hoverOffset: 4,
            }],
        },
        options: {
            cutout: '70%',
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...tooltip,
                    callbacks: {
                        label: (c) => ` ${c.label}: ${c.parsed} barang (${total ? Math.round(c.parsed / total * 100) : 0}%)`,
                    },
                },
            },
        },
    });

    // Batang bertumpuk horizontal: jumlah barang per status di tiap kategori.
    new Chart(document.getElementById('grafik-kategori'), {
        type: 'bar',
        data: {
            labels: data.kategori,
            datasets: data.status.map((s) => ({
                label: s.label,
                data: data.perKategori.map((k) => k[s.kunci] || null), // 0 tidak digambar (tanpa garis tipis)
                backgroundColor: s.warna,
                borderColor: PERMUKAAN,
                borderWidth: { left: 1, right: 1 }, // celah 2px antar segmen
                borderSkipped: false,
                borderRadius: 4,
                barThickness: 16,
            })),
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            interaction: { mode: 'y', intersect: false },
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: GARIS },
                    border: { display: false },
                    title: { display: true, text: 'Jumlah barang' },
                },
                y: {
                    stacked: true,
                    grid: { display: false },
                    border: { color: GARIS },
                    ticks: { color: TINTA },
                },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...tooltip,
                    filter: (c) => c.parsed.x > 0,
                    callbacks: {
                        label: (c) => ` ${c.dataset.label}: ${c.parsed.x} barang`,
                        footer: (items) => `Total: ${data.perKategori[items[0].dataIndex] ? Object.values(data.perKategori[items[0].dataIndex]).reduce((a, b) => a + b, 0) : 0} barang`,
                    },
                },
            },
        },
    });
});
