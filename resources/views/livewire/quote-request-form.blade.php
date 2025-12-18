<div class="bg-white shadow-sm rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Request a Quote</h2>

    <form wire:submit="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="buyer_name" class="block text-sm font-medium text-gray-700">Your Name</label>
                <input wire:model="buyer_name" type="text" id="buyer_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2 border">
                @error('buyer_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="buyer_email" class="block text-sm font-medium text-gray-700">Your Email</label>
                <input wire:model="buyer_email" type="email" id="buyer_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2 border">
                @error('buyer_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label for="part_description" class="block text-sm font-medium text-gray-700">Part Description</label>
            <textarea wire:model="part_description" id="part_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2 border" placeholder="Describe the part you're looking for..."></textarea>
            @error('part_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information (Optional)</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <label for="vehicle_year" class="block text-sm font-medium text-gray-700">Year</label>
                    <input wire:model="vehicle_year" type="number" id="vehicle_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2 border">
                    @error('vehicle_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="vehicle_make" class="block text-sm font-medium text-gray-700">Make</label>
                    <input wire:model="vehicle_make" type="text" id="vehicle_make" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2 border">
                    @error('vehicle_make') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Model</label>
                    <input wire:model="vehicle_model" type="text" id="vehicle_model" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2 border">
                    @error('vehicle_model') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Get Quotes
            </button>
        </div>
    </form>
</div>
