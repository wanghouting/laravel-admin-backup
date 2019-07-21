<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')
        <?php $i=1;?>
        @foreach($options as $option => $label)

            {!! $inline ? '<span class="icheck">' : '<div class="radio icheck">'  !!}

                <label @if($inline)class="radio-inline"@endif style="position: relative;">
                    <input type="radio" name="{{$name}}" @if($i == $optionsCount) id="{{$id}}" @endif value="{{$option}}" class="minimal {{$class}}" {{ ($option == old($column, $value)) || ($value === null && in_array($label, $checked)) ?'checked':'' }} {!! $attributes !!} />&nbsp;{{$label}}&nbsp;&nbsp;
                    @if($i == $optionsCount && !empty($lastInput))
                        <div id="{{$lastInput['id']}}" class="extra-input-text" style="position: absolute;top: 0;left: 100px; display: none;{{$lastInput['style']}}};">

                        @if($lastInput['type'] == 'number')

                                <input required="1" style="width: 100px; text-align: center;" type="text" id="{{$lastInput['column']}}" name="{{$lastInput['column']}}" value="{{$lastInput['value']}}" min="{{$lastInput['min']}}" max="{{$lastInput['max']}}" class="form-control  {{$lastInput['column']}} " placeholder="{{$lastInput['placeholder']}}">

                                <script src="{{url('vendor/laravel-admin/number-input/bootstrap-number-input.js')}}"></script>
                            <script>

                                $('.{{$lastInput['column']}}:not(.initialized)')
                                    .addClass('initialized')
                                    .bootstrapNumber({
                                        upClass: 'success',
                                        downClass: 'primary',
                                        center: true
                                    });
                            </script>
                        @else
                                <input type="text" required="1" class="form-control" style="display: inline-block;width: 70%;" name="{{$lastInput['column']}}" value="{{$lastInput['value']}}" placeholder="{{$lastInput['placeholder']}}">
                        @endif
                        </div>

                    @endif
                </label>

            {!! $inline ? '</span>' :  '</div>' !!}

          <?php $i++;?>
        @endforeach

        @include('admin::form.help-block')

    </div>
</div>
