@if ($showLabel && $showField)
    @if($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!} >
            @endif
            @endif
            @if ($showLabel && $options['label'] !== false && $options['label_show'])
                {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
            @endif
            @if ($showField)
                @php($options['attr']['data-target'] = "#data-{$name}")
                @php($options['attr']['class'] = "form-control datetimepicker-input")
                <div class="input-group date" id="data-{{$name}}" data-target-input="nearest">
                    {!! Form::input("text", $name, $options['value'], $options['attr']) !!}
                    <div class="input-group-append" data-target="#data-{{$name}}" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                     </div>
                </div>
            @endif
            @if ($showLabel && $showField)
                @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
