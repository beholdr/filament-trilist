<?php

namespace Beholdr\FilamentTrilist\Components;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;

class TrilistPage extends Page
{
    public static string $tableRoute = 'index';

    public static string $editRoute = 'edit';

    protected static string $view = 'filament-trilist::page';

    public function getTreeOptions(): array
    {
        return []; // override this
    }

    public function getEditRoute(): ?string
    {
        /** @var Resource */
        $resource = $this::$resource;

        $pages = $resource::getPages();

        if (empty($pages[$this::$editRoute])) {
            return null;
        }

        /** @var Page */
        $editPage = $pages[$this::$editRoute]->getPage();

        return $editPage::getRouteName();
    }

    public function getFieldId(): string
    {
        return 'id';
    }

    public function getFieldLabel(): string
    {
        return 'label';
    }

    public function getFieldChildren(): string
    {
        return 'children';
    }

    public function isAnimated(): bool
    {
        return true;
    }

    public function isSearchable(): bool
    {
        return true;
    }

    public function getSearchPrompt(): string
    {
        return __('filament-forms::components.select.search_prompt');
    }
}
