<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WindowCheckResource\Pages;
use App\Filament\Resources\WindowCheckResource\RelationManagers;
use App\Models\Room;
use App\Models\WindowCheck;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\ValidationException;

class WindowCheckResource extends Resource
{
    protected static ?string $model = WindowCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Window Check';

    protected static ?string $pluralModelLabel = 'Window Checks';

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
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                // Clear validation errors when room changes
                                $livewire->resetValidation(['data.room_number']);
                            })
                            ->rules([
                                'required',
                                function ($livewire) {
                                    return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                        if (!$value) return;
                                        
                                        $data = $livewire->data ?? [];
                                        $month = $data['month'] ?? null;
                                        $year = $data['year'] ?? null;
                                        
                                        if ($month && $year) {
                                            $query = WindowCheck::where('user_id', auth()->id())
                                                ->where('room_number', $value)
                                                ->where('month', $month)
                                                ->where('year', $year);
                                                
                                            // If editing, exclude current record
                                            if (isset($livewire->record) && $livewire->record->id) {
                                                $query->where('id', '!=', $livewire->record->id);
                                            }
                                            
                                            if ($query->exists()) {
                                                $monthName = Carbon::create()->month($month)->format('F');
                                                $fail("Window check for Room {$value} in {$monthName} {$year} already exists. Please edit the existing record instead.");
                                            }
                                        }
                                    };
                                },
                            ]),

                        Forms\Components\Select::make('month')
                            ->options(array_combine(range(1, 12), array_map(fn($m) => Carbon::create()->month($m)->format('F'), range(1, 12))))
                            ->default(now()->month)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                // Clear validation errors when month changes
                                $livewire->resetValidation(['data.month']);
                            })
                            ->rules([
                                'required',
                                function ($livewire) {
                                    return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                        if (!$value) return;
                                        
                                        $data = $livewire->data ?? [];
                                        $roomNumber = $data['room_number'] ?? null;
                                        $year = $data['year'] ?? null;
                                        
                                        if ($roomNumber && $year) {
                                            $query = WindowCheck::where('user_id', auth()->id())
                                                ->where('room_number', $roomNumber)
                                                ->where('month', $value)
                                                ->where('year', $year);
                                                
                                            // If editing, exclude current record
                                            if (isset($livewire->record) && $livewire->record->id) {
                                                $query->where('id', '!=', $livewire->record->id);
                                            }
                                            
                                            if ($query->exists()) {
                                                $monthName = Carbon::create()->month($value)->format('F');
                                                $fail("Window check for Room {$roomNumber} in {$monthName} {$year} already exists. Please edit the existing record instead.");
                                            }
                                        }
                                    };
                                },
                            ]),

                        Forms\Components\Select::make('year')
                            ->options(array_combine(range(now()->year - 2, now()->year + 2), range(now()->year - 2, now()->year + 2)))
                            ->default(now()->year)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                // Clear validation errors when year changes
                                $livewire->resetValidation(['data.year']);
                            })
                            ->rules([
                                'required',
                                function ($livewire) {
                                    return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                        if (!$value) return;
                                        
                                        $data = $livewire->data ?? [];
                                        $roomNumber = $data['room_number'] ?? null;
                                        $month = $data['month'] ?? null;
                                        
                                        if ($roomNumber && $month) {
                                            $query = WindowCheck::where('user_id', auth()->id())
                                                ->where('room_number', $roomNumber)
                                                ->where('month', $month)
                                                ->where('year', $value);
                                                
                                            // If editing, exclude current record
                                            if (isset($livewire->record) && $livewire->record->id) {
                                                $query->where('id', '!=', $livewire->record->id);
                                            }
                                            
                                            if ($query->exists()) {
                                                $monthName = Carbon::create()->month($month)->format('F');
                                                $fail("Window check for Room {$roomNumber} in {$monthName} {$value} already exists. Please edit the existing record instead.");
                                            }
                                        }
                                    };
                                },
                            ]),
                    ]),

                Forms\Components\DatePicker::make('check_date')
                    ->required()
                    ->default(now()),

                Forms\Components\Section::make('Window Condition')
                    ->schema([
                        Forms\Components\Checkbox::make('fit_for_purpose')
                            ->label('Fit for Purpose?')
                            ->helperText('Check if the window is fit for its intended purpose'),

                        Forms\Components\Checkbox::make('status')
                            ->label('Good Status?')
                            ->helperText('Check if the window is in good working condition'),

                        Forms\Components\Textarea::make('comment')
                            ->label('Comments')
                            ->placeholder('Add any observations or notes about the window condition')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('action_taken')
                            ->label('Action Taken')
                            ->placeholder('Describe any actions taken to resolve issues')
                            ->rows(3)
                            ->columnSpanFull()
                            ->hidden(fn(Get $get): bool => $get('fit_for_purpose') === true && $get('status') === true),
                    ]),
            ])
            ->model(WindowCheck::class);
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
                Tables\Columns\IconColumn::make('fit_for_purpose')
                    ->label('Fit for Purpose')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Good Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('comment')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('check_date')
                    ->date(),
            ])
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(function () {
                        return WindowCheck::distinct('year')
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
                Tables\Filters\TernaryFilter::make('fit_for_purpose')
                    ->label('Fit for Purpose'),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Good Status'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWindowChecks::route('/'),
            'create' => Pages\CreateWindowCheck::route('/create'),
            'edit' => Pages\EditWindowCheck::route('/{record}/edit'),
            'report' => Pages\GenerateReport::route('/report'),
        ];
    }

    // Only show window check records belonging to the current user
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}