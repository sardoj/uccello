<div class="form-group">
    <div class="form-line">
        <?php
            $autocompleteSearch = false;

            $entities = [ ];
            $relatedModule =  ucmodule('user');
            $modelClass = $relatedModule->model_class;

            if (isset($column['data']->autocomplete_search) && $column['data']->autocomplete_search === true) {
                $autocompleteSearch = true;
            } else {
                $entities = $modelClass::all();
            }
        ?>
        <select class="field-search" multiple  data-constrain-width="false" data-close-on-click="false">
            @foreach ($entities as $entity)
            <option value="{{ $entity->getKey() }}" @if($searchValue && in_array($entity->getKey(), (array)$searchValue))selected="selected"@endif>{{ $entity->recordLabel }}</option>
            @endforeach
        </select>
    </div>
</div>