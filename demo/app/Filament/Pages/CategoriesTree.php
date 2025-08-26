<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Beholdr\FilamentTrilist\Components\TrilistPage;
use Filament\Actions\Action;

class CategoriesTree extends TrilistPage
{
    protected static string $resource = CategoryResource::class;

    protected static ?string $navigationParentItem = 'Categories';

    public function getTreeOptions(): array
    {
        return Category::tree()->get()->toTree()->toArray();
    }

    public static function getFieldLabel(): string
    {
        return 'name';
    }

    public static function isSearchable(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Create')->url(route('filament.admin.resources.categories.create')),
        ];
    }
}
