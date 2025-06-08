<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintainResource\Pages;
use App\Models\Maintain;
use App\Models\MaintenanceComment;
use App\Models\FireDoorGuardCheck;
use App\Models\FireDoorGuardBatteryReplacement;
use App\Models\Room;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;

class MaintainResource extends Resource
{
    protected static ?string $model = Maintain::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Maintenance';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'room_number';

    // This method filters the query to only show records created by the currently logged-in user
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => Auth::id()),

                Section::make('Room Information')
                    ->schema([
                        Forms\Components\Select::make('room_number')
                            ->options(function ($livewire) {
                                // Get rooms only for the current user
                                $userRooms = Room::where('user_id', auth()->id())
                                    ->pluck('room_number', 'room_number');

                                // Get rooms that already have maintenance records
                                $maintainedRooms = Maintain::where('user_id', auth()->id())
                                    ->pluck('room_number')
                                    ->toArray();

                                // If we're editing an existing record, include the current room
                                if (isset($livewire->record) && $livewire->record->room_number) {
                                    $maintainedRooms = array_diff($maintainedRooms, [$livewire->record->room_number]);
                                }

                                // Filter out rooms that already have maintenance records
                                return $userRooms->filter(function ($roomNumber, $key) use ($maintainedRooms) {
                                    return !in_array($roomNumber, $maintainedRooms);
                                });
                            })
                            ->required()
                            ->searchable()
                            ->placeholder('Select a room that doesn\'t have maintenance record yet'),
                    ])->columns(2),

                Section::make('Bed Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('bed_type')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('has_bed_rails')
                            ->label('Bed Rails')
                            ->default(false),
                        Forms\Components\Toggle::make('has_bed_rail_covers')
                            ->label('Bed Rail Covers')
                            ->default(false),
                        Forms\Components\Select::make('mattress_type')
                            ->options([
                                'Normal Mattress' => 'Normal Mattress',
                                'Air Mattress' => 'Air Mattress',
                            ])
                            ->default('Normal Mattress'),
                        Forms\Components\TextInput::make('air_mattress_machine_type')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get) => $get('mattress_type') === 'Air Mattress'),
                        Forms\Components\Toggle::make('has_sensor_mat')
                            ->label('Sensor Mat')
                            ->default(false),
                    ])->columns(2),

                Section::make('Room Features')
                    ->schema([
                        Forms\Components\Toggle::make('has_ceiling_light')
                            ->label('Ceiling Light')
                            ->default(false),
                        Forms\Components\Toggle::make('has_ceiling_fan')
                            ->label('Ceiling Fan')
                            ->default(false),
                        Forms\Components\Toggle::make('has_wall_light')
                            ->label('Wall Light')
                            ->default(false),
                        Forms\Components\Toggle::make('has_bathroom_light')
                            ->label('Bathroom Light')
                            ->default(false),
                        Forms\Components\Toggle::make('has_ac')
                            ->label('Air Conditioning')
                            ->default(false),
                    ])->columns(2),

                Section::make('Security Features')
                    ->schema([
                        Forms\Components\Toggle::make('has_door_lock')
                            ->label('Door Lock')
                            ->default(false)
                            ->reactive(),

                        Forms\Components\TextInput::make('door_lock_pin')
                            ->label('Door Lock PIN')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get) => $get('has_door_lock') === true),

                        Forms\Components\Repeater::make('fireDoorGuardChecks')
                            ->label('Fire Door Guard Check History')
                            ->relationship('fireDoorGuardChecks')
                            ->schema([
                                Forms\Components\DatePicker::make('checked_date')
                                    ->label('Date Checked')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes'),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['checked_date'] ?? null)
                            ->columns(2),

                        Forms\Components\Repeater::make('fireDoorGuardBatteryReplacements')
                            ->label('Fire Door Guard Battery Replacement History')
                            ->relationship('fireDoorGuardBatteryReplacements')
                            ->schema([
                                Forms\Components\DatePicker::make('replaced_date')
                                    ->label('Date Replaced')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes'),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['replaced_date'] ?? null)
                            ->columns(2),
                    ])->columns(2),

                Section::make('Entertainment')
                    ->schema([
                        Forms\Components\Toggle::make('has_tv')
                            ->label('Television')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\TextInput::make('tv_model')
                            ->label('TV Model')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get) => $get('has_tv') === true),
                        Forms\Components\Toggle::make('has_tv_remote')
                            ->label('TV Remote')
                            ->default(false)
                            ->visible(fn(Forms\Get $get) => $get('has_tv') === true),
                        Forms\Components\Select::make('tv_place')
                            ->label('TV Placement')
                            ->options([
                                'Wall fixed' => 'Wall fixed',
                                'Table' => 'Table',
                            ])
                            ->visible(fn(Forms\Get $get) => $get('has_tv') === true),
                    ])->columns(2),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Repeater::make('comments')
                            ->label('Maintenance Comments History')
                            ->relationship('comments')
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('comment')
                                    ->label('Comment')
                                    ->required()
                                    ->columnSpan(2),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['date'] ?? null)
                            ->columns(3),
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
                IconColumn::make('has_ceiling_light')
                    ->boolean()
                    ->label('Ceiling Light'),
                IconColumn::make('has_ceiling_fan')
                    ->boolean()
                    ->label('Ceiling Fan'),
                IconColumn::make('has_tv')
                    ->boolean()
                    ->label('TV'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Maintained By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                // Print with year selection
                Tables\Actions\Action::make('print_with_year')
                    ->label('Print')
                    ->icon('heroicon-o-calendar')
                    ->form([
                        Forms\Components\Select::make('filter_year')
                            ->label('Filter by Year')
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = [];
                                for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->default(now()->year)
                            ->required(),
                    ])
                    ->action(function (Maintain $record, array $data) {
                        $url = route('maintain.print', [
                            'record' => $record->id,
                            'year' => $data['filter_year']
                        ]);

                        return redirect($url);
                    })
                    ->extraAttributes(['target' => '_blank'])

                    ->modalHeading('Select Year for Report')
                    ->modalSubmitActionLabel('Open Print View'),
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
            'index' => Pages\ListMaintains::route('/'),
            'create' => Pages\CreateMaintain::route('/create'),
            'edit' => Pages\EditMaintain::route('/{record}/edit'),
            'view' => Pages\ViewMaintain::route('/{record}'),
        ];
    }
}
