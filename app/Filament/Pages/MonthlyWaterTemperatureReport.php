<?php

namespace App\Filament\Pages;

use App\Models\WaterTemperature;
use App\Models\Room;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class MonthlyWaterTemperatureReport extends Page implements HasTable
{
    use InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Monthly Temperature Report';
    protected static ?string $title = 'Water Temperature Monthly Report';
    protected static string $view = 'filament.pages.monthly-water-temperature-report';
    
    public ?array $data = [];
    public $year;
    public $month;
    
    public function mount(): void
    {
        $this->year = request()->query('year', date('Y'));
        $this->month = request()->query('month', date('n'));
        
        $this->form->fill([
            'year' => $this->year,
            'month' => $this->month,
        ]);
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('year')
                                ->options(function() {
                                    $years = WaterTemperature::distinct('year')
                                        ->pluck('year', 'year')
                                        ->toArray();
                                    
                                    if (empty($years)) {
                                        $years = [date('Y') => date('Y')];
                                    }
                                    
                                    return $years;
                                })
                                ->default($this->year)
                                ->live()
                                ->afterStateUpdated(function ($state) {
                                    $this->year = $state;
                                    // Remove automatic rendering
                                }),
                                
                            Forms\Components\Select::make('month')
                                ->options(function() {
                                    $months = [];
                                    for ($i = 1; $i <= 12; $i++) {
                                        $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
                                    }
                                    return $months;
                                })
                                ->default($this->month)
                                ->live()
                                ->afterStateUpdated(function ($state) {
                                    $this->month = $state;
                                    // Remove automatic rendering
                                }),
                        ])
                        ->columns(2),
                        
                    // Add a "Load Data" button
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('loadData')
                            ->label('Load Data')
                            ->button()
                            ->color('primary')
                            ->action(function () {
                                // Just trigger a re-render
                                $this->render();
                            }),
                    ]),
                ])
        ];
    }
    
    protected function getActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-on-square')
                ->action('exportPdf')
                ->color('success'),
            
            // Action::make('previousMonth')
            //     ->label('Previous Month')
            //     ->icon('heroicon-o-arrow-left')
            //     ->action('navigateMonth', ['direction' => 'prev'])
            //     ->color('primary'),
                
            // Action::make('nextMonth')
            //     ->label('Next Month')
            //     ->icon('heroicon-o-arrow-right')
            //     ->action('navigateMonth', ['direction' => 'next'])
            //     ->color('primary'),
                
            // Add a direct link to create a new reading
            Action::make('addReading')
                ->label('Add New Reading')
                ->icon('heroicon-o-plus')
                ->url(function () {
                    // Get the resource route for creating a new WaterTemperature
                    return route('filament.admin.resources.water-temperatures.create', [
                        'year' => $this->year, 
                        'month' => $this->month
                    ]);
                })
                ->color('primary'),
        ];
    }
    
    public function navigateMonth(array $data): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1);
        
        if ($data['direction'] === 'prev') {
            $date->subMonth();
        } else {
            $date->addMonth();
        }
        
        $this->year = $date->year;
        $this->month = $date->month;
        
        $this->form->fill([
            'year' => $this->year,
            'month' => $this->month,
        ]);
        
        // Still auto-load when using navigation buttons
        $this->render();
    }
    
    public function exportPdf()
    {
        $monthName = date('F', mktime(0, 0, 0, $this->month, 1));
        $filename = "water_temperature_{$this->year}_{$monthName}.pdf";
        
        $data = $this->getTableQuery()->get();
        $rooms = Room::all()->keyBy('room_number');
        
        $roomsWithRecords = $data->pluck('room_number')->toArray();
        $missingRooms = Room::whereNotIn('room_number', $roomsWithRecords)->pluck('room_number')->toArray();
        
        $pdf = PDF::loadView('pdfs.water-temperature-report', [
            'data' => $data,
            'year' => $this->year,
            'month' => $this->month,
            'monthName' => $monthName,
            'rooms' => $rooms,
            'missingRooms' => $missingRooms,
            'faultCount' => $data->where('has_fault', true)->count(),
            'totalRooms' => Room::count(),
            'completionRate' => Room::count() > 0 
                ? round(($data->count() / Room::count()) * 100) 
                : 0,
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
    
    protected function getTableQuery(): Builder
    {
        return WaterTemperature::query()
            ->where('year', $this->year)
            ->where('month', $this->month);
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room Number')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('cold_temp')
                    ->label('Cold Temperature')
                    ->formatStateUsing(fn($state) => $state ? "{$state}°C" : 'N/A'),
                    
                Tables\Columns\TextColumn::make('hot_temp')
                    ->label('Hot Temperature')
                    ->formatStateUsing(fn($state) => $state ? "{$state}°C" : 'N/A'),
                    
                Tables\Columns\IconColumn::make('has_fault')
                    ->label('Issue Detected')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('check_date')
                    ->label('Check Date')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('action_taken')
                    ->label('Action Taken')
                    ->limit(30)
                    ->wrap()
                    ->tooltip(function ($record) {
                        if (!$record->action_taken || strlen($record->action_taken) <= 30) {
                            return null;
                        }
                        return $record->action_taken;
                    }),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Checked By'),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_fault')
                    ->label('Show Only Issues')
                    ->query(fn (Builder $query) => $query->where('has_fault', true)),
                    
                Tables\Filters\SelectFilter::make('room_number')
                    ->label('Filter by Room')
                    ->options(fn () => Room::pluck('room_number', 'room_number')->toArray()),
            ])
            ->actions([
                // Use direct URLs instead of Filament actions
                // Tables\Actions\Action::make('view')
                //     ->label('View')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn (WaterTemperature $record) => route('filament.admin.resources.water-temperatures.view', $record)),
                    
                Tables\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (WaterTemperature $record) => route('filament.admin.resources.water-temperatures.edit', $record)),
            ])
            ->headerActions([
                // Replace the CreateAction with a direct URL action
                Tables\Actions\Action::make('add_reading')
                    ->label('Add New Reading')
                    ->icon('heroicon-o-plus')
                    ->url(fn () => route('filament.admin.resources.water-temperatures.create', [
                        'year' => $this->year,
                        'month' => $this->month,
                    ])),
            ])
            ->bulkActions([])
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading('No temperature records for this month')
            ->emptyStateDescription('Add temperature readings for rooms by clicking the button below.')
            ->emptyStateActions([
                // Also replace this CreateAction with a direct URL action
                Tables\Actions\Action::make('create')
                    ->label('Add Temperature Record')
                    ->icon('heroicon-o-plus')
                    ->url(fn () => route('filament.admin.resources.water-temperatures.create', [
                        'year' => $this->year,
                        'month' => $this->month,
                    ])),
            ]);
    }
    
    // Define the form method
    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }
}