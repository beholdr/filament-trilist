<?php

namespace Beholdr\FilamentTrilist\Components;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;

class TrilistPage extends Page
{
    protected static string $view = 'filament-trilist::page';

    protected static string $tableRoute = 'index';

    protected static string $editRoute = 'edit';

    public function getTreeOptions(): array
    {
        return []; // override this
    }

    public static function getTableRoute(): string
    {
        return static::$tableRoute;
    }

    public static function getEditRoute(): ?string
    {
        /** @var Resource */
        $resource = static::$resource;

        /** @var Page */
        $editPage = $resource::getPages()[static::$editRoute]->getPage();

        return $editPage::getRouteName();
    }

    public static function getFieldId(): string
    {
        return 'id';
    }

    public static function getFieldLabel(): string
    {
        return 'label';
    }

    public static function getFieldChildren(): string
    {
        return 'children';
    }

    public static function isAnimated(): bool
    {
        return true;
    }

    public static function isSearchable(): bool
    {
        return false;
    }

    public static function getSearchPrompt(): string
    {
        return __('filament-forms::components.select.search_prompt');
    }
}
