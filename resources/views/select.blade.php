<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="fi-input-wrp">
        <trilist-select
            style="overflow: hidden"
            x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-trilist', package: 'beholdr/filament-trilist'))]"
            x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-trilist', package: 'beholdr/filament-trilist'))]"
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},

                async init() {
                    const items = await $wire.mountAction('getTreeOptions', {}, { schemaComponent: @js($getKey()) })
                    $el.init({
                        items,
                        value: this.state,
                        disabled: @js($getDisabledOptions()),
                        onChangeHook: (value) => this.state = value,
                        labelHook: {!! $getLabelHook() !!}
                    })
                    this.$watch('state', () => $el.trilist.setValue(this.state))
                }
            }"

            @if ($isAnimated()) animated @endif
            @if ($isDisabled()) disabled @endif
            @if ($isExpandSelected()) expand-selected @endif
            @if ($isIndependent()) independent @endif
            @if ($isLeafs()) leafs @endif
            @if ($isMultiple()) multiselect @endif
            @if ($getPlaceholder()) placeholder="{{ $getPlaceholder() }}" @endif
            @if ($isSearchable())
                filter
                filter-placeholder="{{ $getSearchPrompt() }}"
                @if ($isAutofocused())
                    filter-autofocus
                @endif
            @endif

            field-id="{{ $getFieldId() }}"
            field-label="{{ $getFieldLabel() }}"
            field-children="{{ $getFieldChildren() }}"

            no-border

            select-button="{{ $getSelectButton() }}"
            cancel-button="{{ $getCancelButton() }}"

            {{
                $attributes
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraAlpineAttributes(), escape: false)
            }}
        ></trilist-select>
    </div>
</x-dynamic-component>
