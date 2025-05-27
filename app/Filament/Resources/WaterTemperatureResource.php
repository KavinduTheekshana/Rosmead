<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaterTemperatureResource\Pages;
use App\Models\Room;
use App\Models\WaterTemperature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Forms\Get;
use Illuminate\Validation\Rule;

class WaterTemperatureResource extends Resource
{
    protected static ?string $model = WaterTemperature::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Water Temperature Check';

    protected static ?string $pluralModelLabel = 'Water Temperature Checks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('room_number')
                            ->options(function () {
                                return Room::where('user_id', auth()->id())
                                    ->pluck('room_number', 'room_number');
                            })
                            ->required()
                            ->searchable()
                            ->live() // Make it reactive for validation
                            ->rules([
                                function ($livewire) {
                                    return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                        $month = data_get($livewire->data, 'month');
                                        $year = data_get($livewire->data, 'year');
                                        
                                        if ($value && $month && $year) {
                                            $query = WaterTemperature::where('user_id', auth()->id())
                                                ->where('room_number', $value)
                                                ->where('month', $month)
                                                ->where('year', $year);
                                                
                                            // If editing, exclude current record
                                            if (isset($livewire->record)) {
                                                $query->where('id', '!=', $livewire->record->id);
                                            }
                                            
                                            if ($query->exists()) {
                                                $monthName = Carbon::create()->month($month)->format('F');
                                                $fail("Water temperature for Room {$value} in {$monthName} {$year} already exists. Please edit the existing record instead.");
                                            }
                                        }
                                    };
                                },
                            ]),

                        Forms\Components\Select::make('month')
                            ->options(array_combine(range(1, 12), array_map(fn($m) => Carbon::create()->month($m)->format('F'), range(1, 12))))
                            ->default(now()->month)
                            ->required()
                            ->live() // Make it reactive for validation
                            ->rules([
                                function ($livewire) {
                                    return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                        $roomNumber = data_get($livewire->data, 'room_number');
                                        $year = data_get($livewire->data, 'year');
                                        
                                        if ($roomNumber && $value && $year) {
                                            $query = WaterTemperature::where('user_id', auth()->id())
                                                ->where('room_number', $roomNumber)
                                                ->where('month', $value)
                                                ->where('year', $year);
                                                
                                            // If editing, exclude current record
                                            if (isset($livewire->record)) {
                                                $query->where('id', '!=', $livewire->record->id);
                                            }
                                            
                                            if ($query->exists()) {
                                                $monthName = Carbon::create()->month($value)->format('F');
                                                $fail("Water temperature for Room {$roomNumber} in {$monthName} {$year} already exists. Please edit the existing record instead.");
                                            }
                                        }
                                    };
                                },
                            ]),

                        Forms\Components\Select::make('year')
                            ->options(array_combine(range(now()->year - 2, now()->year + 1), range(now()->year - 2, now()->year + 1)))
                            ->default(now()->year)
                            ->required()
                            ->live() // Make it reactive for validation
                            ->rules([
                                function ($livewire) {
                                    return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                        $roomNumber = data_get($livewire->data, 'room_number');
                                        $month = data_get($livewire->data, 'month');
                                        
                                        if ($roomNumber && $month && $value) {
                                            $query = WaterTemperature::where('user_id', auth()->id())
                                                ->where('room_number', $roomNumber)
                                                ->where('month', $month)
                                                ->where('year', $value);
                                                
                                            // If editing, exclude current record
                                            if (isset($livewire->record)) {
                                                $query->where('id', '!=', $livewire->record->id);
                                            }
                                            
                                            if ($query->exists()) {
                                                $monthName = Carbon::create()->month($month)->format('F');
                                                $fail("Water temperature for Room {$roomNumber} in {$monthName} {$value} already exists. Please edit the existing record instead.");
                                            }
                                        }
                                    };
                                },
                            ]),
                    ]),

                Forms\Components\DatePicker::make('check_date')
                    ->required()
                    ->default(now()),

                Forms\Components\Section::make('Temperature Readings')
                    ->schema([
                        Forms\Components\TextInput::make('cold_temp')
                            ->label('Cold Water Temperature (°C)')
                            ->numeric()
                            ->step(0.1)
                            ->rules(['nullable', 'numeric'])
                            ->helperText('Normal range: 5-20°C'),

                        Forms\Components\TextInput::make('hot_temp')
                            ->label('Hot Water Temperature (°C)')
                            ->numeric()
                            ->step(0.1)
                            ->rules(['nullable', 'numeric'])
                            ->helperText('Normal range: 50-70°C'),

                        Forms\Components\Checkbox::make('has_fault')
                            ->label('Fault Detected?')
                            ->helperText('Check if there are any issues with the water temperature system')
                            ->live(), // Make the checkbox trigger reactivity

                        Forms\Components\Textarea::make('action_taken')
                            ->label('Action Taken')
                            ->placeholder('Describe any actions taken to resolve issues')
                            ->rows(3)
                            ->columnSpanFull()
                            ->hidden(fn(Get $get): bool => $get('has_fault') !== true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return date('F', mktime(0, 0, 0, $state, 1));
                    }),
                Tables\Columns\TextColumn::make('cold_temp'),
                Tables\Columns\TextColumn::make('hot_temp'),
                Tables\Columns\IconColumn::make('has_fault')
                    ->boolean(),
                Tables\Columns\TextColumn::make('check_date')
                    ->date(),
            ])
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(function () {
                        return WaterTemperature::distinct('year')
                            ->pluck('year', 'year')
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('month')
                    ->options(function () {
                        $months = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
                        }
                        return $months;
                    }),
                Tables\Filters\SelectFilter::make('room_number')
                    ->relationship('room', 'room_number'),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWaterTemperatures::route('/'),
            'create' => Pages\CreateWaterTemperature::route('/create'),
            'edit' => Pages\EditWaterTemperature::route('/{record}/edit'),
            'report' => Pages\GenerateReport::route('/report'),
        ];
    }

    // Only show water temperature records belonging to the current user
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}