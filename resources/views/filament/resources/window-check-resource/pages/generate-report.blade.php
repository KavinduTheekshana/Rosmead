<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::section>
            <x-slot name="heading">
                Generate Report
            </x-slot>
            
            <form wire:submit="generateReport" class="space-y-4">
                {{ $this->form }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        Generate Report
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <!-- Report Results -->
        @if(count($reportData) > 0)
            <x-filament::section>
                <x-slot name="heading">
                    Window Check Report - {{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }} {{ $selectedYear }}
                </x-slot>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Room Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Check Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Fit for Purpose
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Comments
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action Taken
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($reportData as $check)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $check->room_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $check->check_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($check->fit_for_purpose)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                ✓ Yes
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                ✗ No
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($check->status)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                ✓ Good
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                ✗ Poor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                        <div class="truncate" title="{{ $check->comment }}">
                                            {{ $check->comment ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                        <div class="truncate" title="{{ $check->action_taken }}">
                                            {{ $check->action_taken ?: '-' }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Section -->
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-700 dark:text-blue-300">
                            Total Checks
                        </div>
                        <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                            {{ count($reportData) }}
                        </div>
                    </div>
                    
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-700 dark:text-green-300">
                            Fit for Purpose
                        </div>
                        <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                            {{ $reportData->where('fit_for_purpose', true)->count() }}
                        </div>
                    </div>
                    
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-red-700 dark:text-red-300">
                            Issues Found
                        </div>
                        <div class="text-2xl font-bold text-red-900 dark:text-red-100">
                            {{ $reportData->where('fit_for_purpose', false)->where('status', false)->count() }}
                        </div>
                    </div>
                </div>
                
                <!-- Print Button -->
                <div class="flex justify-end mt-6">
                    <x-filament::button 
                        onclick="window.print()" 
                        color="gray"
                        icon="heroicon-o-printer"
                    >
                        Print Report
                    </x-filament::button>
                </div>
            </x-filament::section>
        @elseif($selectedMonth && $selectedYear && empty($reportData))
            <x-filament::section>
                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400">
                        No window checks found for {{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }} {{ $selectedYear }}.
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>