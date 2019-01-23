<ul class="nav nav-tabs m-b-25" role="tablist">
    {{-- Tabs --}}
    @foreach ($module->tabs as $i => $tab)
    <li role="presentation" @if ((empty($selectedTabId) && empty($selectedRelatedlistId) && $i === 0) || $selectedTabId === $tab->id)class="active"@endif>
        <a href="#{{ $tab->id }}" data-toggle="tab">
            <i class="material-icons">{{ $tab->icon ?? 'view_headline' }}</i> {{ uctrans($tab->label, $module) }}
        </a>
    </li>
    @endforeach

    {{-- One tab by related list --}}
    @foreach ($module->relatedlists as $relatedlist)
    @continue(!empty($relatedlist->tab_id) || !Auth::user()->canRetrieve($domain, $relatedlist->relatedModule))
    <li role="presentation" @if ($selectedRelatedlistId === $relatedlist->id)class="active"@endif>
        <a href="#relatedlist_{{ $relatedlist->relatedModule->name }}_{{ $relatedlist->id }}" data-toggle="tab">
            {{-- Icon --}}
            <i class="material-icons">{{ $relatedlist->icon ?? $relatedlist->relatedModule->icon }}</i>

            {{-- Label --}}
            {{ uctrans($relatedlist->label, $module) }}

            {{-- Badge --}}
            <?php
                $relatedModule = $relatedlist->relatedModule;
                $countMethod = $relatedlist->method . 'Count';

                $model = new $relatedModule->model_class;
                $count = $model->$countMethod($relatedlist, $record->id);
            ?>
            @if ($count > 0)
            <span class="badge bg-green">{{ $count }}</span>
            @endif
        </a>
    </li>
    @endforeach
</ul>