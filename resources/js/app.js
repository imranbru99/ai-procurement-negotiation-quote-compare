import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('themeToggle', () => ({
        dark: localStorage.getItem('procureai-theme') === 'dark'
            || (!localStorage.getItem('procureai-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
        init() {
            this.apply();
            this.$watch('dark', () => this.apply());
        },
        apply() {
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('procureai-theme', this.dark ? 'dark' : 'light');
        },
        toggle() {
            this.dark = !this.dark;
        },
    }));

    Alpine.data('pricingToggle', () => ({
        yearly: false,
        currency: 'USD',
        currencies: {},
        init() {
            this.currencies = JSON.parse(this.$el.dataset.currencies || '{}');
        },
        price(monthly, yearly) {
            const base = this.yearly ? yearly : monthly;
            const rate = this.currencies[this.currency]?.rate ?? 1;

            return Math.round(base * rate);
        },
        symbol() {
            return this.currencies[this.currency]?.symbol ?? '$';
        },
    }));

    Alpine.data('savingsCalculator', () => ({
        spend: 250000,
        suppliers: 12,
        rfqs: 48,
        get estimatedSavings() {
            const base = this.spend * 0.042;
            const supplierFactor = Math.min(this.suppliers / 10, 1.4);
            const rfqFactor = Math.min(this.rfqs / 36, 1.3);

            return Math.round(base * supplierFactor * rfqFactor);
        },
    }));

    Alpine.data('quoteMatrix', () => ({
        highlight: null,
        cells: [],
        init() {
            this.cells = JSON.parse(this.$el.dataset.matrix || '[]');
        },
        lowest(row) {
            const values = this.cells[row] || [];

            return Math.min(...values);
        },
    }));
});

Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    });
}
