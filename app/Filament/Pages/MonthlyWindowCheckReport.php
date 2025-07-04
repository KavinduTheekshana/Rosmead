<?php

namespace App\Filament\Pages;

use App\Models\WindowCheck;
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

class MonthlyWindowCheckReport extends Page implements HasTable
{
    use InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Monthly Window Report';
    protected static ?string $title = 'Window Check Monthly Report';
    protected static string $view = 'filament.pages.monthly-window-check-report';
    
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
                                    $years = WindowCheck::distinct('year')
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
                
            // Add a direct link to create a new window check
            Action::make('addCheck')
                ->label('Add New Check')
                ->icon('heroicon-o-plus')
                ->url(function () {
                    // Get the resource route for creating a new WindowCheck
                    return route('filament.admin.resources.window-checks.create', [
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
        $filename = "window_check_{$this->year}_{$monthName}.pdf";
        
        $data = $this->getTableQuery()->get();
        $rooms = Room::all()->keyBy('room_number');
        
        $roomsWithRecords = $data->pluck('room_number')->toArray();
        $missingRooms = Room::whereNotIn('room_number', $roomsWithRecords)->pluck('room_number')->toArray();
        
        $pdf = PDF::loadView('pdfs.window-check-report', [
            'data' => $data,
            'year' => $this->year,
            'month' => $this->month,
            'monthName' => $monthName,
            'rooms' => $rooms,
            'missingRooms' => $missingRooms,
            'issueCount' => $data->where('fit_for_purpose', false)->where('status', false)->count(),
            'notFitForPurposeCount' => $data->where('fit_for_purpose', false)->count(),
            'poorStatusCount' => $data->where('status', false)->count(),
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
        return WindowCheck::query()
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
                    
                Tables\Columns\IconColumn::make('fit_for_purpose')
                    ->label('Fit for Purpose')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\IconColumn::make('status')
                    ->label('Good Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('check_date')
                    ->label('Check Date')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comments')
                    ->limit(30)
                    ->wrap()
                    ->tooltip(function ($record) {
                        if (!$record->comment || strlen($record->comment) <= 30) {
                            return null;
                        }
                        return $record->comment;
                    }),
                    
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
                Tables\Filters\Filter::make('not_fit_for_purpose')
                    ->label('Show Only Not Fit for Purpose')
                    ->query(fn (Builder $query) => $query->where('fit_for_purpose', false)),
                    
                Tables\Filters\Filter::make('poor_status')
                    ->label('Show Only Poor Status')
                    ->query(fn (Builder $query) => $query->where('status', false)),
                    
                Tables\Filters\Filter::make('has_issues')
                    ->label('Show Only Issues')
                    ->query(fn (Builder $query) => $query->where('fit_for_purpose', false)->orWhere('status', false)),
                    
                Tables\Filters\SelectFilter::make('room_number')
                    ->label('Filter by Room')
                    ->options(fn () => Room::pluck('room_number', 'room_number')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (WindowCheck $record) => route('filament.admin.resources.window-checks.edit', $record)),
            ])
            ->headerActions([
                // Replace the CreateAction with a direct URL action
                Tables\Actions\Action::make('add_check')
                    ->label('Add New Check')
                    ->icon('heroicon-o-plus')
                    ->url(fn () => route('filament.admin.resources.window-checks.create', [
                        'year' => $this->year,
                        'month' => $this->month,
                    ])),
            ])
            ->bulkActions([])
            ->emptyStateIcon('heroicon-o-squares-2x2')
            ->emptyStateHeading('No window checks for this month')
            ->emptyStateDescription('Add window checks for rooms by clicking the button below.')
            ->emptyStateActions([
                // Also replace this CreateAction with a direct URL action
                Tables\Actions\Action::make('create')
                    ->label('Add Window Check')
                    ->icon('heroicon-o-plus')
                    ->url(fn () => route('filament.admin.resources.window-checks.create', [
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