<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SD Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        [v-cloak] { display: none; }
        .form-input, .form-textarea, .form-select {
            width: 100%;
            border-radius: 0.375rem;
            border-color: #d1d5db;
            padding: 0.5rem;
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #6366f1;
            ring: 2px;
            ring-color: #6366f1;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div id="app" class="container mx-auto px-4 py-8" v-cloak>
        <h1 class="text-3xl font-bold mb-8">Stable Diffusion Generator</h1>
        
        <div v-if="error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            @{{ error }}
            <button @click="error = null" class="absolute top-0 right-0 px-4 py-3">
                <span class="sr-only">Close</span>
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form @submit.prevent="generateImage" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Model</label>
                        <select v-model="form.model" class="form-select">
                            <option v-for="model in models" :key="model.title" :value="model.title">@{{ model.title }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prompt</label>
                        <textarea v-model="form.prompt" class="form-textarea" rows="3" required></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Negative Prompt</label>
                        <textarea v-model="form.negative_prompt" class="form-textarea" rows="2"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Steps</label>
                            <input type="number" v-model="form.steps" min="1" max="150" class="form-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Width</label>
                            <input type="number" v-model="form.width" min="64" max="2048" step="64" class="form-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Height</label>
                            <input type="number" v-model="form.height" min="64" max="2048" step="64" class="form-input" required>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50" 
                            :disabled="generating">
                        @{{ generating ? 'Generating...' : 'Generate' }}
                    </button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Generated Images</h2>
                <div v-if="images.length > 0" class="space-y-4">
                    <div v-for="(image, index) in images" :key="index" class="border rounded-lg p-2">
                        <img :src="'data:image/png;base64,' + image" class="w-full h-auto rounded" />
                        <button @click="downloadImage(image, index)" 
                                class="mt-2 w-full bg-gray-600 text-white py-1 px-4 rounded-md hover:bg-gray-700">
                            Download
                        </button>
                    </div>
                </div>
                <div v-else class="text-gray-500 text-center py-8">
                    No images generated yet
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue

        createApp({
            data() {
                return {
                    models: [],
                    form: {
                        model: '',
                        prompt: '',
                        negative_prompt: '',
                        steps: 20,
                        width: 512,
                        height: 512
                    },
                    images: [],
                    generating: false,
                    error: null
                }
            },
            async mounted() {
                // Set up axios CSRF token
                axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                try {
                    const response = await axios.get('/sdapi/v1/sd-models');
                    this.models = response.data;
                    if (this.models.length > 0) {
                        this.form.model = this.models[0].title;
                    }
                } catch (error) {
                    console.error('Failed to fetch models:', error);
                    this.error = 'Failed to fetch models. Please ensure the Stable Diffusion API is running.';
                }
            },
            methods: {
                async generateImage() {
                    this.generating = true;
                    this.error = null;
                    try {
                        const response = await axios.post('/sdapi/v1/txt2img', this.form);
                        if (response.data.images) {
                            this.images = response.data.images;
                        } else {
                            throw new Error('No images received from the API');
                        }
                    } catch (error) {
                        console.error('Generation failed:', error);
                        this.error = error.response?.data?.error || 'Failed to generate image. Please ensure the Stable Diffusion API is running and try again.';
                    } finally {
                        this.generating = false;
                    }
                },
                downloadImage(base64Image, index) {
                    const link = document.createElement('a');
                    link.href = 'data:image/png;base64,' + base64Image;
                    link.download = `generated-image-${index}.png`;
                    link.click();
                }
            }
        }).mount('#app')
    </script>
</body>
</html>
