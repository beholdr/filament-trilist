<x-filament-panels::page>
    <div
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-trilist', package: 'beholdr/filament-trilist'))]"
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-trilist', package: 'beholdr/filament-trilist'))]"
        class="flex flex-col gap-y-6"
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook('filament-trilist::page.before', scopes: $this->getRenderHookScopes()) }}

        <x-filament::section>
            <trilist-view
                x-data="{
                    async init() {
                        const items = await $wire.$call('getTreeOptions')
                        $el.init({
                            items,
                            labelHook: {!! $this->getLabelHook() !!}
                        })
                    }
                }"

                @if ($this->isAnimated()) animated @endif
                @if ($this->isSearchable())
                    filter
                    filter-placeholder="{{ $this->getSearchPrompt() }}"
                @endif

                field-id="{{ $this->getFieldId() }}"
                field-label="{{ $this->getFieldLabel() }}"
                field-children="{{ $this->getFieldChildren() }}"
            ></trilist-view>
        </x-filament::section>

        {{ \Filament\Support\Facades\FilamentView::renderHook('filament-trilist::page.after', scopes: $this->getRenderHookScopes()) }}
    </div>
</x-filament-panels::page>
