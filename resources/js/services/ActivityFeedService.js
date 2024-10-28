export class ActivityFeedService {
    constructor() {
        this.filters = {
            type: '',
            search: '',
            startDate: '',
            endDate: '',
            perPage: 10,
        };
        this.page = 1;
        this.loading = false;
        this.hasMore = true;
        this.activities = [];
        this.statistics = {};
    }

    async initialize() {
        await this.loadFilters();
        await this.loadActivities(true);
        this.setupInfiniteScroll();
        this.setupFilterListeners();
    }

    async loadFilters() {
        try {
            const response = await fetch('/api/activities?per_page=1');
            const data = await response.json();
            this.availableFilters = data.filters;
            this.renderFilterOptions();
        } catch (error) {
            console.error('Error loading filters:', error);
        }
    }

    async loadActivities(reset = false) {
        if (this.loading || (!this.hasMore && !reset)) return;
        
        this.loading = true;
        this.showLoader();
        
        if (reset) {
            this.page = 1;
            this.hasMore = true;
            this.activities = [];
            this.clearActivityFeed();
        }

        try {
            const queryParams = new URLSearchParams({
                ...this.filters,
                page: this.page,
            });

            const response = await fetch(`/api/activities?${queryParams}`);
            const data = await response.json();

            if (data.activities.length === 0) {
                this.hasMore = false;
                return;
            }

            this.activities = [...this.activities, ...data.activities];
            this.statistics = data.statistics;
            this.renderActivities(data.activities);
            this.updateStatistics();
            this.page++;

        } catch (error) {
            console.error('Error loading activities:', error);
        } finally {
            this.loading = false;
            this.hideLoader();
        }
    }

    setupInfiniteScroll() {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.loading && this.hasMore) {
                    this.loadActivities();
                }
            });
        }, options);

        const loader = document.getElementById('activity-loader');
        if (loader) {
            observer.observe(loader);
        }
    }

    setupFilterListeners() {
        // Type filter
        const typeSelect = document.getElementById('activity-type');
        if (typeSelect) {
            typeSelect.addEventListener('change', () => {
                this.filters.type = typeSelect.value;
                this.loadActivities(true);
            });
        }

        // Search filter
        const searchInput = document.getElementById('activity-search');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(() => {
                this.filters.search = searchInput.value;
                this.loadActivities(true);
            }, 300));
        }

        // Date range filters
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        if (startDateInput && endDateInput) {
            [startDateInput, endDateInput].forEach(input => {
                input.addEventListener('change', () => {
                    this.filters.startDate = startDateInput.value;
                    this.filters.endDate = endDateInput.value;
                    this.loadActivities(true);
                });
            });
        }
    }

    renderFilterOptions() {
        const typeSelect = document.getElementById('activity-type');
        if (typeSelect && this.availableFilters?.types) {
            typeSelect.innerHTML = `
                <option value="">All Activities</option>
                ${Object.entries(this.availableFilters.types).map(([value, label]) => `
                    <option value="${value}">${label}</option>
                `).join('')}
            `;
        }
    }

    renderActivities(activities) {
        const container = document.getElementById('activity-feed');
        if (!container) return;

        const html = activities.map(activity => this.createActivityHTML(activity)).join('');
        container.insertAdjacentHTML('beforeend', html);
        
        // Initialize any interactive elements
        this.initializeActivityInteractions();
    }

    createActivityHTML(activity) {
        switch (activity.type) {
            case 'image_generation':
                return this.createImageGenerationActivity(activity);
            case 'transaction':
                return this.createTransactionActivity(activity);
            default:
                return this.createDefaultActivity(activity);
        }
    }

    createImageGenerationActivity(activity) {
        const subject = activity.subject;
        return `
            <li class="activity-item" data-type="image_generation">
                <div class="relative pb-8">
                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm text-gray-500">
                                <div class="font-medium text-gray-900">
                                    ${activity.description}
                                </div>
                                <span class="whitespace-nowrap">${activity.created_at}</span>
                            </div>
                            ${subject ? this.createImagePreview(subject) : ''}
                        </div>
                    </div>
                </div>
            </li>
        `;
    }

    createImagePreview(subject) {
        return `
            <div class="mt-2">
                <div class="flex items-center space-x-2">
                    <img src="${subject.thumbnail_url}" alt="Generated Image" class="h-20 w-20 object-cover rounded">
                    <div class="flex-1">
                        <p class="text-sm text-gray-500">${this.truncateText(subject.prompt, 100)}</p>
                        <div class="mt-2 flex space-x-2">
                            <button type="button" 
                                    data-action="download" 
                                    data-generation-id="${subject.id}" 
                                    class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Download
                            </button>
                            <button type="button" 
                                    data-action="rerun" 
                                    data-generation-id="${subject.id}" 
                                    class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Re-run
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    createTransactionActivity(activity) {
        const subject = activity.subject;
        return `
            <li class="activity-item" data-type="transaction">
                <div class="relative pb-8">
                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full ${subject?.type === 'credit' ? 'bg-green-500' : 'bg-red-500'} flex items-center justify-center ring-8 ring-white">
                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm text-gray-500">
                                <div class="font-medium text-gray-900">
                                    ${activity.description}
                                </div>
                                <span class="whitespace-nowrap">${activity.created_at}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        `;
    }

    createDefaultActivity(activity) {
        return `
            <li class="activity-item" data-type="${activity.type}">
                <div class="relative pb-8">
                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm text-gray-500">
                                <div class="font-medium text-gray-900">
                                    ${activity.description}
                                </div>
                                <span class="whitespace-nowrap">${activity.created_at}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        `;
    }

    initializeActivityInteractions() {
        document.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', async (e) => {
                const action = e.target.dataset.action;
                const generationId = e.target.dataset.generationId;
                
                try {
                    const response = await fetch(`/api/generations/${generationId}/${action}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (action === 'download') {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `generation-${generationId}.png`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    } else if (action === 'rerun') {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error(`Error performing ${action}:`, error);
                }
            });
        });
    }

    updateStatistics() {
        const statsContainer = document.getElementById('activity-stats');
        if (!statsContainer || !this.statistics) return;

        statsContainer.innerHTML = `
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Total Activities</h3>
                    <p class="text-3xl font-bold">${this.statistics.total}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Image Generations</h3>
                    <p class="text-3xl font-bold">${this.statistics.by_type.image_generation || 0}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Transactions</h3>
                    <p class="text-3xl font-bold">${this.statistics.by_type.transaction || 0}</p>
                </div>
            </div>
        `;
    }

    showLoader() {
        const loader = document.getElementById('activity-loader');
        if (loader) {
            loader.classList.remove('hidden');
        }
    }

    hideLoader() {
        const loader = document.getElementById('activity-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    }

    clearActivityFeed() {
        const container = document.getElementById('activity-feed');
        if (container) {
            container.innerHTML = '';
        }
    }

    truncateText(text, length) {
        return text.length > length ? text.substring(0, length) + '...' : text;
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}
