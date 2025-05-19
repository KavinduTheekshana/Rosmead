<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintainResource\Pages;
use App\Models\Maintain;
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
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Log;

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
                            ->options(function () {
                                // Get rooms only for the current user
                                return Room::where('user_id', auth()->id())
                                    ->pluck('room_number', 'room_number');
                            })
                            ->required()
                            ->searchable(),
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

                        Forms\Components\Repeater::make('fire_door_guard_checked')
                            ->label('Fire Door Guard Check History')
                            ->schema([
                                Forms\Components\DatePicker::make('checked_date')
                                    ->label('Date Checked')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes'),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['checked_date'] ?? null)
                            ->columns(2),

                        Forms\Components\Repeater::make('fire_door_guard_battery_replaced')
                            ->label('Fire Door Guard Battery Replacement History')
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
                            ->columns(3), // Increased columns for better control
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

                // Print action - shows PDF in browser for preview (using URL)
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(function (Maintain $record) {
                        return route('maintain.print', ['record' => $record->id]);
                    })
                    ->openUrlInNewTab(true),

        

                Tables\Actions\Action::make('print')
                     ->label('Download')
               ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Maintain $record) {
                        // Generate PDF
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.maintain', ['record' => $record]);

                        // Return the PDF for download
                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            "room-{$record->room_number}.pdf"
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Generate PDF for printing (inline display)
     */
    public static function printPdf($recordId)
    {
        $record = static::getModel()::findOrFail($recordId);

        // Ensure user can only access their own records
        if ($record->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.maintain', ['record' => $record]);

            return response($pdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="room-' . $record->room_number . '.pdf"')
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public');
        } catch (\Exception $e) {
            \Log::error('PDF Print Error: ' . $e->getMessage());
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for download
     */
    public static function downloadPdf($recordId)
    {
        $record = static::getModel()::findOrFail($recordId);

        // Ensure user can only access their own records
        if ($record->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.maintain', ['record' => $record]);

            $filename = "room-{$record->room_number}-" . now()->format('Y-m-d') . '.pdf';

            return response($pdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdf->output()));
        } catch (\Exception $e) {
            Log::error('PDF Download Error: ' . $e->getMessage());
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
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
