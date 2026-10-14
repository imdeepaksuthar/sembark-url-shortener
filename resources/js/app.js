import './bootstrap';

import Alpine from 'alpinejs';

window.shortUrlsManager = function(initialPeriod, dashboardUrl) {
    return {
        period: initialPeriod,
        loading: false,
        init() {
            window.addEventListener('popstate', () => {
                const params = new URLSearchParams(window.location.search);
                this.period = params.get('period') || 'this_month';
                this.applyFilter(window.location.href, false);
            });
        },
        async applyFilter(url = null, push = true) {
            this.loading = true;
            const targetUrl = url || `${dashboardUrl}?period=${this.period}`;
            try {
                const response = await fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (this.$refs.tableContainer) {
                        this.$refs.tableContainer.innerHTML = data.html;
                    }
                    if (data.exportUrl && this.$refs.exportBtn) {
                        this.$refs.exportBtn.href = data.exportUrl;
                    }
                    if (push && window.location.href !== targetUrl) {
                        window.history.pushState({}, '', targetUrl);
                    }
                }
            } catch (error) {
                console.error('Filter request failed', error);
            } finally {
                this.loading = false;
            }
        },
        handlePagination(event) {
            const anchor = event.target.closest('a');
            if (!anchor) return;

            if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
                return;
            }

            const isPaginationLink = anchor.closest("nav[role='navigation']") ||
                                     anchor.closest('nav') ||
                                     anchor.closest('.pagination') ||
                                     (anchor.search && (anchor.search.includes('page=') || anchor.search.includes('view_all=')));

            if (isPaginationLink && anchor.href && this.$refs.tableContainer && this.$refs.tableContainer.contains(anchor)) {
                event.preventDefault();
                this.applyFilter(anchor.href);
            }
        }
    };
};

Alpine.data('shortUrlsManager', window.shortUrlsManager);

window.Alpine = Alpine;

Alpine.start();
