<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * The newest enquiries, so a submission is one click from the dashboard
 * rather than something you have to remember to go and look for.
 */
class RecentEnquiries extends TableWidget
{
    // Rendered with the page rather than fetched afterwards. The dashboard
    // is the first thing seen on signing in, and a row of empty cards that
    // fill a moment later reads as something being broken.
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            // The limit belongs on the query: Table has no limit() in v5.
            ->query(fn (): Builder => Lead::query()->latest()->limit(5))
            ->heading('Latest enquiries')
            ->description('Unread ones are in bold. Opening an enquiry marks it read.')
            ->emptyStateHeading('No enquiries yet')
            ->emptyStateDescription('Anything sent through the contact form arrives here.')
            ->emptyStateIcon('heroicon-o-inbox')
            ->paginated(false)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->tooltip(fn (Lead $record): string => $record->created_at->format('d M Y, H:i')),

                TextColumn::make('name')
                    ->description(fn (Lead $record): string => $record->email)
                    ->weight(fn (Lead $record): ?string => $record->read_at === null ? 'bold' : null),

                TextColumn::make('organisation')
                    ->placeholder('Not given')
                    ->toggleable(),

                TextColumn::make('subject')
                    ->placeholder('None')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Lead $record): string => LeadResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
