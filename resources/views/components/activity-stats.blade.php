@props(['statistics'])

<div class="grid grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Activities</h3>
        <p class="text-3xl font-bold">{{ $statistics['total'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Image Generations</h3>
        <p class="text-3xl font-bold">{{ $statistics['by_type']['image_generation'] ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Transactions</h3>
        <p class="text-3xl font-bold">{{ $statistics['by_type']['transaction'] ?? 0 }}</p>
    </div>
</div>
