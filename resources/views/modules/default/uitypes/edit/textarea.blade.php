<?php $isLarge = $field->data->large ?? false; ?>
<?php $isError = form_errors($form->{$field->name}) ?? false; ?>
<div class="col {{ $isLarge ? 's12' : 's12 m6' }} input-field @if($isError)invalid @endif">

    {{-- Add icon if defined --}}
    @if($field->icon ?? false)
    <i class="material-icons prefix">{{ $field->icon }}</i>
    @endif

    {!! form_label($form->{$field->name}) !!}
    {!! form_widget($form->{$field->name}) !!}

    @if ($isError)
        <span class="helper-text red-text">
            {!! form_errors($form->{$field->name}) !!}
        </span>
    @endif

    <span class="helper-text">
        {{ uctrans('field.info.new_line', $module) }}
    </span>
</div>