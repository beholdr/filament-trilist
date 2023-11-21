# Filament Trilist ☘️

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beholdr/filament-trilist.svg?style=flat-square)](https://packagist.org/packages/beholdr/filament-trilist)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/beholdr/filament-trilist/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/beholdr/filament-trilist/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/beholdr/filament-trilist/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/beholdr/filament-trilist/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/beholdr/filament-trilist.svg?style=flat-square)](https://packagist.org/packages/beholdr/filament-trilist)

Filament plugin for working with tree data: **treeselect input** and **treeview page**. Based on [Trilist package](https://github.com/beholdr/trilist/).

## Support

Do you like **Filament Trilist**? Please support me via [Boosty](https://boosty.to/beholdr).

## Features

- Treeselect input and treeview page
- Tree items can have multiple parents
- Works with relationship or custom hierarchical data

## Installation

You can install the package via composer:

```bash
composer require beholdr/filament-trilist
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-trilist-views"
```

## Treeselect input

![Treeselect input](https://github.com/beholdr/filament-trilist/assets/741973/fcb8803a-dc92-4c6b-a140-cf3bb12deb0b)

Import `TrilistSelect` class and use it on your Filament form:

``` php
use Beholdr\FilamentTrilist\Components\TrilistSelect

// with custom tree data (see below about tree data)
TrilistSelect::make('fieldname')
    ->options($optionsArray),

// or with relationship
TrilistSelect::make('category_id')
    ->relationship('category', fn () => Category::tree()->get()->toTree()),
```

Full options list:

``` php
TrilistSelect::make(string $fieldName)
    ->label(string $fieldLabel)
    ->placeholder(string | Closure $placeholder)
    ->disabled(bool | Closure $condition)

    // array of tree items (see below about tree data), not necessary if using relationship() option
    ->options(array | Closure $options),

    // first argument defines name of the relationship, second should provide array of tree items (see below about tree data)
    ->relationship(string | Closure $relationshipName, Closure $getTreeOptions)

    // array of ids (or single id) of disabled items
    ->disabledOptions(string | int | array | Closure $value)

    // multiple selection mode, default: false
    ->multiple(bool | Closure $condition)

    // animate expand/collapse, default: true
    ->animated(bool | Closure $condition)

    // expand initial selected options, default: true
    ->expandSelected(bool | Closure $condition)

    // in independent mode children auto selected when parent is selected, default: false
    ->independent(bool | Closure $condition)

    // in leafs mode, the selected value is not grouped as the parent when all child elements are selected, default: false
    ->leafs(bool | Closure $condition)

    // tree item id field name, default: 'id'
    ->fieldId(string | Closure $value)

    // tree item label field name, default: 'label'
    ->fieldLabel(string | Closure $value)

    // tree item children field name, default: 'children'
    ->fieldChildren(string | Closure $value)

    // enable filtering of items, default: false
    ->searchable(bool | Closure $condition)

    // search input placeholder
    ->searchPrompt(string | Htmlable | Closure $message)
```

## Treeview page

![Treeview page](https://github.com/beholdr/filament-trilist/assets/741973/4d0f92d6-aca8-42bd-896a-0eb94cb32858)

Import `FilamentTrilistPlugin` class and use it on your Filament panel provider:

```php
use Beholdr\FilamentTrilist\FilamentTrilistPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->plugins([
                FilamentTrilistPlugin::make(),
            ]);
    }
}
```

1. Generate custom page for a treeview, following instructions on [Filament docs](https://filamentphp.com/docs/3.x/panels/resources/custom-pages).

2. Change newly generated class to extend `Beholdr\FilamentTrilist\Components\TrilistPage`

3. Add `getTreeOptions()` method to load tree items:

``` php
namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Beholdr\FilamentTrilist\Components\TrilistPage;

class TrilistPosts extends TrilistPage
{
    protected static string $resource = PostResource::class;

    // optional page and tab title
    protected static ?string $title = 'Posts Tree';

    // return array of tree items (see below about tree data)
    public function getTreeOptions(): array
    {
        return Post::root()->get()->toArray();
    }
}
```

### Treeview as index page

If you want to show treeview page as a first page of a given resource, you need to modify `getPages()` method:

``` diff
class PostResource extends Resource
{
    public static function getPages(): array
    {
        return [
            // change 'index' to custom trilist page
+           'index' => Pages\TrilistPosts::route('/'),
            // change default index page to 'table' (change both key and route)
-           'index' => Pages\ListPosts::route('/'),
+           'table' => Pages\ListPosts::route('/table'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
```

Then add `$tableRoute` property to the trilist page class:

``` php
class TrilistPosts extends TrilistPage
{
    public static string $tableRoute = 'table';
}
```

### Custom tab icons

To change default tab icons you can use given methods on plugin initialization:

``` php
FilamentTrilistPlugin::make()
    ->tabIconTree('heroicon-o-bars-3-bottom-left')
    ->tabIconTable('heroicon-o-table-cells')
```

## Tree data

You can use hierarchical data from any source while it follows format:

``` php
[
    ['id' => 'ID', 'label' => 'Item label', 'children' => [
        ['id' => 'ID', 'label' => 'Item label', 'children' => [...]],
        ...
    ]
]
```

### Relationships

You can use special library like [staudenmeir/laravel-adjacency-list](https://github.com/staudenmeir/laravel-adjacency-list) to generate tree items data, for example:

``` php
Category::tree()->get()->toTree()
```

Or use your own relationship methods, even with `ManyToMany` (multiple parents) relationship. Example for self-referencing entity:

<details>
<summary>Migrations</summary>

``` php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
        });

        Schema::create('profession_profession', function (Blueprint $table) {
            $table->primary(['parent_id', 'child_id']);
            $table->foreignId('parent_id')->constrained('professions')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('professions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professions');
        Schema::dropIfExists('profession_profession');
    }
};
```
</details>

<details>
<summary>Model</summary>

``` php
namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $with = ['children'];

    public function parents()
    {
        return $this->belongsToMany(Profession::class, 'profession_profession', 'child_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(Profession::class, 'profession_profession', 'parent_id', 'child_id');
    }

    public function scopeRoot(Builder $builder)
    {
        $builder->doesntHave('parents');
    }
}
```
</details>

With given model you can generate tree data like this:

``` php
Profession::root()->get();
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
