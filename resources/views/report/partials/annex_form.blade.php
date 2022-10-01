<div class="row">
    <div class="col-sm-12">
        <h3 style="margin-top: 0; margin-bottom: 30px;">
            {{ $title }}
        </h3>
    </div>
</div>

{!! Form::open(['action' => $action, 'method' => 'post']) !!}
    <div class="row">
        {{-- initial_date --}}
        <div class="col-md-3">
            <div class="form-group">
                <label for="from">@lang('accounting.from')</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('initial_date', @format_date($month['first_day']),
                        ['class' => 'form-control _date', 'required', 'readonly', 'style' => 'width: 100%']) !!}
                </div>
            </div>
        </div>

        {{-- final_date --}}
        <div class="col-md-3">
            <div class="form-group">
                <label for="account">@lang('accounting.to')</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('final_date', @format_date($month['last_day']),
                        ['class' => 'form-control _date', 'required', 'readonly', 'style' => 'width: 100%']) !!}
                </div>
            </div>
        </div>

        {{-- location_id --}}
        <div class="col-md-4" @if ($hide_location) style="display: none;" @endif>
            <div class="form-group">
                <label>@lang('accounting.location')</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-building"></i>
                    </span>
                    {!! Form::select('location_taxpayer', $locations, '',
                    ['id' => 'location_id', 'class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                </div>
            </div>
        </div>

        {{-- annex_number --}}
        <div class="col-md-2">
            <div class="form-group">
                <label for="account">@lang('accounting.no_annex')</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-hashtag"></i>
                    </span>
                    {!! Form::text('annex_number', $annex_number,
                        ['class' => 'form-control', 'required', 'placeholder' => '#']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- report_type --}}
        <div class="col-md-3">
            <div class="form-group">
                <label for="account">@lang('accounting.format')</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-file-o"></i>
                    </span>
                    {!! Form::select('report_type', ['csv' => 'CSV', 'xlsx' => 'Excel'], null,
                        ['class' => 'form-control select2', 'required', 'style' => 'width: 100%']) !!}
                </div>
            </div>
        </div>

        {{-- submit --}}
        <div class="col-md-4">
            <div class="form-group" style="margin-top: 25px;">
                <input type="submit" class="btn btn-success" value="@lang('credit.download')">
            </div>
        </div>
    </div>
{!! Form::close() !!}

@section('javascript')
<script>
    $(document).ready(function() {
        // Datetime picker
        $('input._date').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true
        });
    });
</script>
@endsection