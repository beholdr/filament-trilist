<?php

namespace Beholdr\FilamentTrilist;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTrilistServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-trilist';

    public static string $viewNamespace = 'filament-trilist';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasViews(static::$viewNamespace)
            ->hasViewComponent(static::$viewNamespace, 'tabs');
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );
    }

    protected function getAssetPackageName(): ?string
    {
        return 'beholdr/filament-trilist';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Css::make('filament-trilist', __DIR__ . '/../resources/dist/filament-trilist.css')->loadedOnRequest(),
            Js::make('filament-trilist', __DIR__ . '/../resources/dist/filament-trilist.js')->loadedOnRequest(),
        ];
    }
}
