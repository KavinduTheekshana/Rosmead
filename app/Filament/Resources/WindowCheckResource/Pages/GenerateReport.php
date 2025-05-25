<?php

namespace App\Filament\Resources\WindowCheckResource\Pages;

use App\Filament\Resources\WindowCheckResource;
use App\Models\WindowCheck;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Carbon\Carbon;

class GenerateReport extends Page
{
    protected static string $resource = WindowCheckResource::class;

    protected static string $view = 'filament.resources.window-check-resource.pages.generate-report';

    protected ?string $heading = 'Monthly Window Check Reports';

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
        $windowChecks = WindowCheck::where('user_id', auth()->id())
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->orderBy('room_number')
            ->get();

        $this->reportData = $windowChecks;
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