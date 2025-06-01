<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $recordTitleAttribute = 'room_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('room_number')
                    ->required()
                    ->maxLength(255)
                    ->live() // Make it reactive for real-time validation
                    ->unique(
                        table: Room::class,
                        column: 'room_number',
                        ignoreRecord: true // This ignores the current record when editing
                    )
                    ->validationMessages([
                        'unique' => 'This room number already exists. Please choose a different room number.',
                    ])
                    ->rules([
                        function ($livewire) {
                            return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                if (!$value) return;
                                
                                $query = Room::where('room_number', $value);
                                
                                // If editing, exclude current record
                                if (isset($livewire->record) && $livewire->record->id) {
                                    $query->where('id', '!=', $livewire->record->id);
                                }
                                
                                if ($query->exists()) {
                                    $fail('Room number "' . $value . '" already exists. Please choose a different room number.');
                                }
                            };
                        },
                    ])
                    ->placeholder('Enter room number (e.g., 101, A-201, etc.)')
                    ->helperText('Room number must be unique across the system'),
                // User ID is automatically set via the model, so we don't need a field for it
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Room number copied to clipboard!')
                    ->label('Room Number'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Added By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(), // Shows "2 hours ago" format
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('created_today')
                    ->label('Created Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),
                Tables\Filters\Filter::make('created_this_week')
                    ->label('Created This Week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Room')
                    ->modalDescription('Are you sure you want to delete this room? This action cannot be undone and may affect related records.')
                    ->modalSubmitActionLabel('Yes, Delete Room'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Rooms')
                        ->modalDescription('Are you sure you want to delete these rooms? This action cannot be undone and may affect related records.')
                        ->modalSubmitActionLabel('Yes, Delete Rooms'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-home')
            ->emptyStateHeading('No rooms found')
            ->emptyStateDescription('Get started by creating your first room.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add First Room')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // You can add related resources here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    // This is important - ensures users only see rooms they created
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}