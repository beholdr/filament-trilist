<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Beholdr\FilamentTrilist\Components\TrilistSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var ?Category */
        $record = $schema->getRecord();

        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TrilistSelect::make('parent_id')
                    ->label('Parent Category')
                    ->fieldLabel('name')
                    ->options(fn () => Category::tree()->get()->toTree())
                    ->disabledOptions($record?->id)
                    ,
            ]);
    }
}
