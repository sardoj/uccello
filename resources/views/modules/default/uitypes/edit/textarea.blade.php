<?php $isLarge = $field->data->large ?? false; ?>
<?php $isError = form_errors($form->{$field->name}) ?? false; ?>
<div class="{{ $isLarge ? 'col-md-12' : 'col-sm-6 col-xs-12' }}">
    <div class="form-group form-float">

            <div class="input-field">
                {{-- Add icon if defined --}}
                @if($field->icon ?? false)
                <i class="material-icons prefix">{{ $field->icon }}</i>
                @endif

                <div class="form-line {{ $isError ? 'focused error' : ''}}" style="padding-top: 2px">
                    {{-- Label --}}
                    {!! form_label($form->{$field->name}) !!}

                    {{-- Field --}}
                    {!! form_widget($form->{$field->name}) !!}
                </div>
            </div>

            @if($isError)
            <div class="help-info m-l-5">
                {!! form_errors($form->{$field->name}) !!}
            </div>
            @endif

            {{-- Add help info if defined --}}
            @if($field->data->info ?? false)
            <div class="help-info m-l-5">
                {{ uctrans($field->data->info, $module) }}
            </div>
            @endif

            {{-- New line info --}}
            <div class="help-info">
                {{ uctrans('field.info.new_line', $module) }}
            </div>
        </div>
</div>