<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintainResource\Pages;
use App\Models\Maintain;
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
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\IconColumn;

class MaintainResource extends Resource
{
    protected static ?string $model = Maintain::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Room Maintenance';

    protected static ?string $recordTitleAttribute = 'room_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => Auth::id()),

                Section::make('Room Information')
                    ->schema([
                        Forms\Components\TextInput::make('room_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('resident_name')
                            ->maxLength(255),
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
                            ->visible(fn (Forms\Get $get) => $get('has_door_lock') === true),

                        Forms\Components\Repeater::make('window_lock_checked')
                            ->label('Window Lock Check History')
                            ->schema([
                                Forms\Components\DatePicker::make('checked_date')
                                    ->label('Date Checked')
                                    ->required(),
                                Forms\Components\TextInput::make('checked_by')
                                    ->label('Checked By')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes'),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['checked_date'] ?? null)
                            ->columns(3),

                        Forms\Components\Repeater::make('fire_door_guard_checked')
                            ->label('Fire Door Guard Check History')
                            ->schema([
                                Forms\Components\DatePicker::make('checked_date')
                                    ->label('Date Checked')
                                    ->required(),
                                Forms\Components\TextInput::make('checked_by')
                                    ->label('Checked By')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes'),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['checked_date'] ?? null)
                            ->columns(3),

                        Forms\Components\Repeater::make('fire_door_guard_battery_replaced')
                            ->label('Fire Door Guard Battery Replacement History')
                            ->schema([
                                Forms\Components\DatePicker::make('replaced_date')
                                    ->label('Date Replaced')
                                    ->required(),
                                Forms\Components\TextInput::make('replaced_by')
                                    ->label('Replaced By')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes'),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['replaced_date'] ?? null)
                            ->columns(3),
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
                            ->visible(fn (Forms\Get $get) => $get('has_tv') === true),
                        Forms\Components\Toggle::make('has_tv_remote')
                            ->label('TV Remote')
                            ->default(false)
                            ->visible(fn (Forms\Get $get) => $get('has_tv') === true),
                        Forms\Components\Select::make('tv_place')
                            ->label('TV Placement')
                            ->options([
                                'Wall fixed' => 'Wall fixed',
                                'Table' => 'Table',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('has_tv') === true),
                    ])->columns(2),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Repeater::make('comments')
                            ->label('Maintenance Comments History')
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('Date')
                                    ->required(),
                                Forms\Components\TextInput::make('user')
                                    ->label('Added By')
                                    ->required(),
                                Forms\Components\Textarea::make('comment')
                                    ->label('Comment')
                                    ->required(),
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
                Tables\Columns\TextColumn::make('resident_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bed_type')
                    ->searchable(),
                IconColumn::make('has_bed_rails')
                    ->boolean()
                    ->label('Bed Rails'),
                Tables\Columns\TextColumn::make('mattress_type')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('has_sensor_mat')
                    ->boolean()
                    ->label('Sensor Mat'),
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