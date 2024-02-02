# Filament Trilist ☘️

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beholdr/filament-trilist.svg?style=flat-square)](https://packagist.org/packages/beholdr/filament-trilist)
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

```php
use Beholdr\FilamentTrilist\Components\TrilistSelect

// with custom tree data (see below about tree data)
TrilistSelect::make('fieldname')
    ->options($optionsArray),

// or with relationship
TrilistSelect::make('category_id')
    ->relationship('category', fn () => Category::tree()->get()->toTree()),
```

Full options list:

```php
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

    // select button label
    ->selectButton(string | Htmlable | Closure $message)

    // cancel button label
    ->cancelButton(string | Htmlable | Closure $message)
```

### Usage in filters

You can use treeselect in [custom filter](https://filamentphp.com/docs/3.x/tables/filters#custom-filter-forms):

```php
use App\Models\Category;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

Filter::make('category')
    ->form([
        TrilistSelect::make('category_id')
            ->multiple()
            ->independent()
            ->options(Category::tree()->get()->toTree())
    ])
    ->query(function (Builder $query, array $data) {
        $query->when(
            $data['category_id'],
            function (Builder $query, $values) {
                $ids = Category::whereIn('id', $values)
                    ->get()
                    ->map(fn (Category $category) => $category
                        ->descendantsAndSelf()
                        ->pluck('id')
                        ->toArray()
                    )->flatten();
                $query->whereIn('category_id', $ids);
            }
        );
    })
    ->indicateUsing(function (array $data) {
        if (! $data['category_id']) return null;

        return Category::whereIn('id', $data['category_id'])->pluck('name')->toArray();
    }),
```

## Treeview page

![Treeview page](https://github.com/beholdr/filament-trilist/assets/741973/225ea768-3c42-45c3-a80d-88bdb159a4e5)

Create custom page class inside `Pages` directory of your resource directory. Note that page class extends `Beholdr\FilamentTrilist\Components\TrilistPage`:

```php
namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Beholdr\FilamentTrilist\Components\TrilistPage;

class TreePosts extends TrilistPage
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

Register created page in the static `getPages()` method of your resource:

```php
public static function getPages(): array
{
    return [
        // ...
        'tree' => Pages\TreePosts::route('/tree'),
    ];
}
```

Add link for the page to a panel navigation:

```php
use Beholdr\FilamentTrilist\FilamentTrilistPlugin;
use Filament\Navigation\NavigationItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->navigationItems([
                NavigationItem::make('Tree')
                    ->url(fn () => route('filament.admin.resources.posts.tree'))
                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.posts.tree'))
                    ->parentItem('Posts'),
            ])
    }
}
```

### Treeview as index page

If you want to show treeview page as a first page of a resource, modify `getPages()` method:

```diff
class PostResource extends Resource
{
    public static function getPages(): array
    {
        return [
            // change 'index' to custom trilist page
-           'tree' => Pages\TreePosts::route('/tree'),
+           'index' => Pages\TreePosts::route('/'),
            // change default index page to 'table' (change both key and route)
-           'index' => Pages\ListPosts::route('/'),
+           'table' => Pages\ListPosts::route('/table'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
```

Then change navigation link to point to the table page instead of tree:

```php
NavigationItem::make('Table')
    ->url(fn () => route('filament.admin.resources.posts.table'))
    ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.posts.table'))
    ->parentItem('Posts'),
```

### Treeview options

You can set some tree options by overriding static methods in the custom page class:

```php
class TreeCategories extends TrilistPage
{
    public static function getFieldLabel(): string
    {
        return 'name';
    }
}
```

- `getFieldId()`: tree item id field name
- `getFieldLabel()`: tree item label field name
- `getFieldChildren()`: tree item children field name
- `isAnimated()`: animate expand/collapse, default: true
- `isSearchable()`: enable filtering of items, default: false
- `getSearchPrompt()`: search input placeholder

## Tree data

You can use hierarchical data from any source when it follows format:

```php
[
    ['id' => 'ID', 'label' => 'Item label', 'children' => [
        ['id' => 'ID', 'label' => 'Item label', 'children' => [...]],
        ...
    ]
]
```

### Relationships

You can use special library like [staudenmeir/laravel-adjacency-list](https://github.com/staudenmeir/laravel-adjacency-list) to generate tree items data, for example:

```php
Category::tree()->get()->toTree()
```

Or use your own relationship methods, even with `ManyToMany` (multiple parents) relationship. Example for self-referencing entity:

<details>
<summary>Migrations</summary>

```php
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

```php
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

```php
Profession::root()->get();
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
