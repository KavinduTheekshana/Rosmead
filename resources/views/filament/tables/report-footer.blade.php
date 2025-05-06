<div class="px-4 py-3 bg-gray-50">
    <div class="grid grid-cols-4 gap-4 text-sm">
        <div>
            <span class="font-medium">Total Rooms:</span> {{ $totalRooms }}
        </div>
        <div>
            <span class="font-medium">Rooms Checked:</span> {{ $totalCount }}
        </div>
        <div>
            <span class="font-medium">Faults Detected:</span> 
            <span class="{{ $faultCount > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $faultCount }}</span>
        </div>
        <div>
            <span class="font-medium">Completion Rate:</span> {{ $completionRate }}%
        </div>
    </div>
</div>