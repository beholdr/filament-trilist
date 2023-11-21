<?php

namespace Beholdr\FilamentTrilist;

use Beholdr\FilamentTrilist\Components\TrilistPage;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Blade;

class FilamentTrilistPlugin implements Plugin
{
    protected string $tabIconTree = 'heroicon-o-bars-3-bottom-left';

    protected string $tabIconTable = 'heroicon-o-table-cells';

    public function getId(): string
    {
        return 'filament-trilist';
    }

    public function register(Panel $panel): void
    {
        /** @var Resource $resource */
        foreach ($panel->getResources() as $resource) {
            $pages = $resource::getPages();

            foreach ($pages as $page) {
                if (is_subclass_of($page->getPage(), TrilistPage::class, true)) {
                    /** @var TrilistPage */
                    $trilistPage = $page->getPage();
                    $treeTitle = $trilistPage::getNavigationLabel();
                    $treeRoute = $trilistPage::getRouteName($panel->getId());
                    $treeIcon = $this->tabIconTree;

                    /** @var Page */
                    $tablePage = $pages[$trilistPage::$tableRoute]->getPage();
                    $tableTitle = $tablePage::getNavigationLabel();
                    $tableRoute = $tablePage::getRouteName($panel->getId());
                    $tableIcon = $this->tabIconTable;

                    $props = 'treeTitle="' . $treeTitle . '" treeIcon="' . $treeIcon . '" treeRoute="' . $treeRoute . '" tableTitle="' . $tableTitle . '" tableIcon="' . $tableIcon . '" tableRoute="' . $tableRoute . '"';

                    $panel->renderHook(
                        'filament-trilist::page.before',
                        fn (): string => Blade::render('<x-filament-trilist::tabs ' . $props . ' tree />'),
                        $trilistPage::getResource(),
                    );
                    $panel->renderHook(
                        'panels::resource.pages.list-records.table.before',
                        fn (): string => Blade::render('<x-filament-trilist::tabs ' . $props . ' />'),
                        $trilistPage::getResource(),
                    );
                }
            }
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function tabIconTree(string $icon): static
    {
        $this->tabIconTree = $icon;

        return $this;
    }

    public function tabIconTable(string $icon): static
    {
        $this->tabIconTable = $icon;

        return $this;
    }
}
