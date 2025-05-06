<?php

namespace App\Filament\Resources\WaterTemperatureResource\Pages;

use App\Filament\Resources\WaterTemperatureResource;
use App\Models\WaterTemperature;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Carbon\Carbon;

class GenerateReport extends Page
{
    protected static string $resource = WaterTemperatureResource::class;

    protected static string $view = 'filament.resources.water-temperature-resource.pages.generate-report';

    protected ?string $heading = 'Monthly Temperature Reports';

    public $selectedMonth;
    public $selectedYear;
    public $reportData = [];

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function generateReport(): void
    {
        $temperatures = WaterTemperature::where('user_id', auth()->id())
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->orderBy('room_number')
            ->get();

        $this->reportData = $temperatures;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('selectedMonth')
                            ->label('Month')
                            ->options(array_combine(range(1, 12), array_map(fn($m) => Carbon::create()->month($m)->format('F'), range(1, 12))))
                            ->default(now()->month)
                            ->required(),
                            
                        Forms\Components\Select::make('selectedYear')
                            ->label('Year')
                            ->options(array_combine(range(now()->year - 2, now()->year + 1), range(now()->year - 2, now()->year + 1)))
                            ->default(now()->year)
                            ->required(),
                    ]),
            ]);
    }
}