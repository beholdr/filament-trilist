<?php

namespace Beholdr\FilamentTrilist\Components;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;

class TrilistPage extends Page
{
    protected static string $view = 'filament-trilist::page';

    protected static string $editRoute = 'edit';

    public function getTreeOptions(): array
    {
        return []; // override this
    }

    public function getLabelHook(): string
    {
        if (! $editRoute = $this->getEditRoute()) {
            return 'undefined';
        }

        $template = route($editRoute, ['record' => '#ID#'], false);

        return <<<JS
        (item) => `<a href='\${'{$template}'.replace('#ID#', item.id)}'>\${item.label}</a>`
        JS;
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

    protected function getEditRoute(): ?string
    {
        /** @var Resource */
        $resource = static::$resource;
        $pages = $resource::getPages();

        if (empty($pages[static::$editRoute])) {
            return null;
        }

        /** @var Page */
        $editPage = $pages[static::$editRoute]->getPage();

        return $editPage::getRouteName();
    }

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $pageName = '.' . static::getResourcePageName();

        return [
            NavigationItem::make(static::getNavigationLabel())
                ->url(fn () => route(static::getResource()::getRouteBaseName() . $pageName))
                ->isActiveWhen(fn () => request()->routeIs(static::getResource()::getRouteBaseName() . $pageName))
                ->parentItem(fn () => static::getResource()::getNavigationLabel()),
        ];
    }
}
