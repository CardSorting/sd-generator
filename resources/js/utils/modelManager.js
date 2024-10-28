export class ModelManager {
    constructor() {
        this.selectedTags = new Set();
        this.selectedCategory = null;
        this.models = [];
        this.tags = {};
        this.categories = [];
    }

    async initialize() {
        try {
            const response = await fetch('/models');
            const data = await response.json();
            
            this.models = data.models;
            this.tags = data.tags;
            this.categories = data.categories;
            
            this.initializeUI();
            this.addEventListeners();
        } catch (error) {
            console.error('Error initializing ModelManager:', error);
        }
    }

    initializeUI() {
        this.renderCategories();
        this.renderTags();
        this.updateModelSelect();
    }

    renderCategories() {
        const container = document.getElementById('model-categories');
        if (!container) return;

        container.innerHTML = this.categories.map(category => `
            <button type="button" 
                    data-category="${category}"
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium ${
                        this.selectedCategory === category ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'
                    } hover:bg-gray-200">
                ${category}
            </button>
        `).join('');
    }

    renderTags() {
        ['style', 'mood'].forEach(type => {
            const container = document.getElementById(`${type}-tags`);
            if (!container || !this.tags[type]) return;

            container.innerHTML = this.tags[type].map(tag => `
                <button type="button"
                        data-tag="${tag.slug}"
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium ${
                            this.selectedTags.has(tag.slug) ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'
                        } hover:bg-gray-200"
                        style="background-color: ${this.selectedTags.has(tag.slug) ? tag.color + '33' : ''}; color: ${tag.color}">
                    ${tag.name}
                </button>
            `).join('');
        });
    }

    updateModelSelect() {
        const modelSelect = document.getElementById('model');
        if (!modelSelect) return;

        const filteredModels = this.models.filter(model => {
            const categoryMatch = !this.selectedCategory || model.category === this.selectedCategory;
            const tagMatch = this.selectedTags.size === 0 || 
                           model.tags.some(tag => this.selectedTags.has(tag.slug));
            return categoryMatch && tagMatch;
        });

        modelSelect.innerHTML = `
            <option value="">Select a model</option>
            ${filteredModels.map(model => `
                <option value="${model.title}" 
                        data-settings='${JSON.stringify(model.recommended_settings)}'>
                    ${model.title}
                </option>
            `).join('')}
        `;

        // Update settings if a model is selected
        if (modelSelect.value) {
            this.updateModelSettings(modelSelect.value);
        }
    }

    updateModelSettings(modelTitle) {
        const model = this.models.find(m => m.title === modelTitle);
        if (!model || !model.recommended_settings) return;

        const settings = model.recommended_settings;
        
        // Update form fields with recommended settings
        const fields = ['steps', 'cfg_scale', 'width', 'height'];
        fields.forEach(field => {
            const input = document.getElementById(field);
            if (input && settings[field]) {
                input.value = settings[field];
            }
        });
    }

    addEventListeners() {
        // Category buttons
        document.querySelectorAll('#model-categories button').forEach(button => {
            button.addEventListener('click', () => {
                const category = button.dataset.category;
                this.selectedCategory = this.selectedCategory === category ? null : category;
                this.renderCategories();
                this.updateModelSelect();
            });
        });

        // Tag buttons
        ['style-tags', 'mood-tags'].forEach(containerId => {
            document.querySelectorAll(`#${containerId} button`).forEach(button => {
                button.addEventListener('click', () => {
                    const tag = button.dataset.tag;
                    if (this.selectedTags.has(tag)) {
                        this.selectedTags.delete(tag);
                    } else {
                        this.selectedTags.add(tag);
                    }
                    this.renderTags();
                    this.updateModelSelect();
                });
            });
        });

        // Model select
        const modelSelect = document.getElementById('model');
        if (modelSelect) {
            modelSelect.addEventListener('change', (e) => {
                this.updateModelSettings(e.target.value);
            });
        }
    }
}
