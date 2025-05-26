
<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('filament.admin.resources.rooms.create') }}" 
                   class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                    <x-heroicon-o-home class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-3" />
                    <div>
                        <div class="font-medium text-blue-900 dark:text-blue-100">Add Room</div>
                        <div class="text-sm text-blue-600 dark:text-blue-300">Create new room</div>
                    </div>
                </a>
                
                <a href="{{ route('filament.admin.resources.water-temperatures.create') }}" 
                   class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                    <x-heroicon-o-fire class="w-8 h-8 text-red-600 dark:text-red-400 mr-3" />
                    <div>
                        <div class="font-medium text-red-900 dark:text-red-100">Water Check</div>
                        <div class="text-sm text-red-600 dark:text-red-300">Add temperature reading</div>
                    </div>
                </a>
                
                <a href="{{ route('filament.admin.resources.window-checks.create') }}" 
                   class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                    <x-heroicon-o-squares-2x2 class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" />
                    <div>
                        <div class="font-medium text-green-900 dark:text-green-100">Window Check</div>
                        <div class="text-sm text-green-600 dark:text-green-300">Check window status</div>
                    </div>
                </a>
                
                <a href="{{ route('filament.admin.resources.maintains.create') }}" 
                   class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                    <x-heroicon-o-cog-6-tooth class="w-8 h-8 text-purple-600 dark:text-purple-400 mr-3" />
                    <div>
                        <div class="font-medium text-purple-900 dark:text-purple-100">Maintenance</div>
                        <div class="text-sm text-purple-600 dark:text-purple-300">Add maintenance record</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>

