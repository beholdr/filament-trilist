<?php

namespace Beholdr\FilamentTrilist\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrilistSelect extends Field
{
    use Concerns\CanBeSearchable;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'filament-trilist::select';

    protected bool | Closure $isAnimated = true;

    protected bool | Closure $isExpandSelected = true;

    protected bool | Closure $isIndependent = false;

    protected bool | Closure $isLeafs = false;

    protected bool | Closure $isMultiple = false;

    protected string | Closure | null $relationship = null;

    protected string | int | array | Closure | null $disabledOptions = null;

    protected string | Htmlable | Closure | null $selectButton = null;

    protected string | Htmlable | Closure | null $cancelButton = null;

    protected string | Closure $fieldId = 'id';

    protected string | Closure $fieldLabel = 'label';

    protected string | Closure $fieldChildren = 'children';

    protected string | Closure $labelHook = <<<'JS'
    (item) => item.label
    JS;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(static fn (TrilistSelect $component): ?array => $component->isMultiple() ? [] : null);

        $this->registerActions([
            Action::make('getTreeOptions')->action($this->getOptions(...)),
        ]);
    }

    public function animated(bool | Closure $condition = true): static
    {
        $this->isAnimated = $condition;

        return $this;
    }

    public function isAnimated(): bool
    {
        return (bool) $this->evaluate($this->isAnimated);
    }

    public function expandSelected(bool | Closure $condition = true): static
    {
        $this->isExpandSelected = $condition;

        return $this;
    }

    public function isExpandSelected(): bool
    {
        return (bool) $this->evaluate($this->isExpandSelected);
    }

    public function independent(bool | Closure $condition = true): static
    {
        $this->isIndependent = $condition;

        return $this;
    }

    public function isIndependent(): bool
    {
        return (bool) $this->evaluate($this->isIndependent);
    }

    public function leafs(bool | Closure $condition = true): static
    {
        $this->isLeafs = $condition;

        return $this;
    }

    public function isLeafs(): bool
    {
        return (bool) $this->evaluate($this->isLeafs);
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function selectButton(string | Htmlable | Closure | null $message): static
    {
        $this->selectButton = $message;

        return $this;
    }

    public function getSelectButton(): string | Htmlable
    {
        return $this->evaluate($this->selectButton) ?? __('filament-forms::components.file_upload.editor.actions.save.label');
    }

    public function cancelButton(string | Htmlable | Closure | null $message): static
    {
        $this->cancelButton = $message;

        return $this;
    }

    public function getCancelButton(): string | Htmlable
    {
        return $this->evaluate($this->cancelButton) ?? __('filament-forms::components.file_upload.editor.actions.cancel.label');
    }

    public function fieldId(string | Closure $value): static
    {
        $this->fieldId = $value;

        return $this;
    }

    public function getFieldId(): string
    {
        return (string) $this->evaluate($this->fieldId);
    }

    public function fieldLabel(string | Closure $value): static
    {
        $this->fieldLabel = $value;

        return $this;
    }

    public function getFieldLabel(): string
    {
        return (string) $this->evaluate($this->fieldLabel);
    }

    public function fieldChildren(string | Closure $value): static
    {
        $this->fieldChildren = $value;

        return $this;
    }

    public function getFieldChildren(): string
    {
        return (string) $this->evaluate($this->fieldChildren);
    }

    public function relationship(
        string | Closure $name = null,
        Closure $modifyQueryUsing = null
    ): static {
        $this->relationship = $name ?? $this->getName();

        $this->loadStateFromRelationshipsUsing(static function (TrilistSelect $component, $state) use ($modifyQueryUsing): void {
            if (filled($state)) {
                return;
            }

            if (! $relationship = $component->getRelationship()) {
                return;
            }

            if ($modifyQueryUsing) {
                $component->modifyRelationshipQuery($relationship, $modifyQueryUsing);
            }

            if (! $relatedModel = $relationship->getResults()) {
                return;
            }

            if ($relationship instanceof BelongsToMany) {
                /** @var Collection $relatedModel */
                $component->state(
                    $relatedModel
                        ->pluck($relationship->getRelatedKeyName())
                        ->toArray()
                );
            } else {
                $component->state(
                    $relatedModel->getAttribute(
                        $relationship->getOwnerKeyName(),
                    ),
                );
            }
        });

        $this->saveRelationshipsUsing(static function (TrilistSelect $component, $state) use ($modifyQueryUsing) {
            $relationship = $component->getRelationship();

            if ($modifyQueryUsing) {
                $component->modifyRelationshipQuery($relationship, $modifyQueryUsing);
            }

            if ($relationship instanceof BelongsToMany) {
                $relationship->detach($relationship->pluck('id')->toArray());
                $relationship->attach($state);
            } else {
                $relationship->associate($state);
            }
        });

        $this->dehydrated(fn (TrilistSelect $component): bool => ! $component->isMultiple());

        return $this;
    }

    public function modifyRelationshipQuery(BelongsTo | BelongsToMany $relationship, Closure $modifyQueryUsing)
    {
        $relationshipQuery = $relationship->getQuery();

        $relationshipQuery = $this->evaluate($modifyQueryUsing, [
            'query' => $relationshipQuery,
        ]) ?? $relationshipQuery;

        $relationship->setQuery($relationshipQuery->getQuery());
    }

    public function getRelationshipName(): ?string
    {
        return (string) $this->evaluate($this->relationship);
    }

    public function getRelationship(): BelongsTo | BelongsToMany | null
    {
        if (blank($this->getRelationshipName())) {
            return null;
        }

        $record = $this->getModelInstance();

        $relationship = null;

        foreach (explode('.', $this->getRelationshipName()) as $nestedRelationshipName) {
            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $relationship;
    }

    public function disabledOptions(string | int | array | Closure | null $value): static
    {
        $this->disabledOptions = $value;

        return $this;
    }

    public function getDisabledOptions(): array
    {
        return (array) $this->evaluate($this->disabledOptions);
    }

    public function labelHook(string | Closure $value): static
    {
        $this->labelHook = $value;

        return $this;
    }

    public function getLabelHook(): string
    {
        return (string) $this->evaluate($this->labelHook);
    }
}
