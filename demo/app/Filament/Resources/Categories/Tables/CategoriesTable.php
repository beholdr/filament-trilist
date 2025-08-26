<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Beholdr\FilamentTrilist\Components\TrilistSelect;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('parent_id')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('category')
                    ->schema([
                        TrilistSelect::make('category_id')
                            ->label('Parent Category')
                            ->fieldLabel('name')
                            ->multiple()
                            ->independent()
                            ->options(Category::tree()->get()->toTree()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query->when($data['category_id'], function (Builder $query, $values) {
                            $ids = Category::query()
                                ->whereIn('id', $values)
                                ->get()
                                ->map
                                ->descendantsAndSelf
                                ->flatten()
                                ->pluck('id')
                                ->toArray();

                            $query->whereIn('parent_id', $ids);
                        });
                    })
                    ->indicateUsing(function (array $data): array {
                        return ! empty($data['category_id'])
                            ? Category::whereIn('id', $data['category_id'])->pluck('name')->toArray()
                            : [];
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
