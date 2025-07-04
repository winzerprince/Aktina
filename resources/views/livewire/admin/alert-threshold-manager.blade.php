<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900">Alert Threshold Management</h3>
        <p class="text-sm text-gray-600">Configure alert thresholds for inventory and system performance</p>
    </div>
    
    @if ($successMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative" role="alert">
            <span class="block sm:inline">{{ $successMessage }}</span>
        </div>
    @endif
    
    <div class="mb-6 grid grid-cols-2 gap-4">
        <div>
            <label for="selectedCategory" class="block text-sm font-medium text-gray-700">Category</label>
            <select 
                id="selectedCategory"
                wire:model.live="selectedCategory"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="selectedType" class="block text-sm font-medium text-gray-700">Type</label>
            <select 
                id="selectedType"
                wire:model.live="selectedType"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                @foreach($types as $type)
                    <option value="{{ $type }}">{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="border rounded-lg overflow-hidden mb-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Threshold</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ ucfirst($selectedCategory) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ str_replace('_', ' ', ucfirst($selectedType)) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if ($isEditing)
                            <div class="flex items-center">
                                <input 
                                    type="number" 
                                    wire:model="newThreshold" 
                                    class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                @error('newThreshold') 
                                    <span class="text-red-500 text-xs ml-2">{{ $message }}</span>
                                @enderror
                            </div>
                        @else
                            {{ $thresholds[$selectedCategory][$selectedType] ?? 'N/A' }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if ($isEditing)
                            <button 
                                wire:click="updateThreshold" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs mr-2"
                            >
                                Save
                            </button>
                            <button 
                                wire:click="cancelEditing" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs"
                            >
                                Cancel
                            </button>
                        @else
                            <button 
                                wire:click="startEditing" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs"
                            >
                                Edit
                            </button>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-700 mb-2">Current Thresholds</h4>
        <div class="space-y-2 text-sm">
            @foreach($thresholds as $category => $typeValues)
                <div class="font-medium">{{ ucfirst($category) }}</div>
                <ul class="list-disc pl-5 mb-4">
                    @foreach($typeValues as $type => $value)
                        <li>{{ str_replace('_', ' ', ucfirst($type)) }}: {{ $value }}</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
    
    @script
    <script>
        $wire.on('clearMessage', () => {
            setTimeout(() => {
                $wire.set('successMessage', '');
            }, 3000);
        });
    </script>
    @endscript
</div>
