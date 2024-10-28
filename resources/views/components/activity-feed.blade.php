<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <!-- Activity Feed Header -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Activity Feed</h3>
            <div class="flex space-x-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           id="activity-search" 
                           class="form-input rounded-md shadow-sm sm:text-sm" 
                           placeholder="Search activities...">
                </div>

                <!-- Type Filter -->
                <div class="relative">
                    <select id="activity-type" 
                            class="form-select rounded-md shadow-sm sm:text-sm">
                        <option value="">All Activities</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="flex space-x-2">
                    <input type="date" 
                           id="start-date" 
                           class="form-input rounded-md shadow-sm sm:text-sm">
                    <input type="date" 
                           id="end-date" 
                           class="form-input rounded-md shadow-sm sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Activity Statistics -->
        <div id="activity-stats" class="mb-6">
            <!-- Statistics will be populated by JavaScript -->
        </div>

        <!-- Activity List -->
        <div class="flow-root">
            <ul role="list" id="activity-feed" class="-mb-8">
                <!-- Activities will be populated by JavaScript -->
            </ul>

            <!-- Loading Indicator -->
            <div id="activity-loader" class="hidden">
                <div class="flex justify-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>
