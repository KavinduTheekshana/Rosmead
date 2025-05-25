<x-filament::page>
    <x-filament::card>
        {{ $this->form }}
        <div class="mt-4">
            <h3 class="text-lg font-medium">
                Window Check Records for {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}
            </h3>
        </div>
    </x-filament::card>

    <div class="mt-4">
        {{ $this->table }}
    </div>

    <x-filament::card class="mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-medium mb-2">Missing Rooms</h3>
                @php
                    $roomsWithRecords = $this->getTableQuery()->pluck('room_number')->toArray();
                    $missingRooms = \App\Models\Room::whereNotIn('room_number', $roomsWithRecords)->pluck('room_number')->toArray();
                @endphp
                @if(count($missingRooms) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($missingRooms as $room)
                            <div class="px-4 py-2 bg-gray-100 rounded-md text-center">{{ $room }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-500">All rooms have window check records for this month.</div>
                @endif
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Summary</h3>
                @php
                    $recordCount = $this->getTableQuery()->count();
                    $fitForPurposeCount = $this->getTableQuery()->where('fit_for_purpose', true)->count();
                    $goodStatusCount = $this->getTableQuery()->where('status', true)->count();
                    $issuesCount = $this->getTableQuery()->where(function($query) {
                        $query->where('fit_for_purpose', false)->orWhere('status', false);
                    })->count();
                    $totalRooms = \App\Models\Room::count();
                    $completionRate = $totalRooms > 0 ? round(($recordCount / $totalRooms) * 100) : 0;
                @endphp
                <div class="space-y-2">
                    <div>Total Rooms: <span class="font-medium">{{ $totalRooms }}</span></div>
                    <div>Rooms Checked: <span class="font-medium">{{ $recordCount }}</span></div>
                    <div>Fit for Purpose: <span class="font-medium text-green-600">{{ $fitForPurposeCount }}</span></div>
                    <div>Good Status: <span class="font-medium text-green-600">{{ $goodStatusCount }}</span></div>
                    <div>Issues Found: <span class="font-medium {{ $issuesCount > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $issuesCount }}</span></div>
                    <div>Completion Rate: <span class="font-medium">{{ $completionRate }}%</span></div>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::page>