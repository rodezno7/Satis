<tr>
    {{-- license_plate --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][license_plate]', null,
            ['class' => 'form-control input-sm vehicle_license_plate']
        ) !!}
    </td>

    {{-- brand_id --}}
    <td>
        {!! Form::select(
            'vehicles[' . $row_count . '][brand_id]', $brands, '',
            ['class' => 'form-control input-sm vehicle_brand_id', 'placeholder' => __('messages.please_select')]
        ) !!}
    </td>

    {{-- model --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][model]', null,
            ['class' => 'form-control input-sm vehicle_model']
        ) !!}
    </td>

    {{-- year --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][year]', null,
            ['class' => 'form-control input-sm input_number vehicle_year']
        ) !!}
    </td>

    {{-- color --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][color]', null,
            ['class' => 'form-control input-sm vehicle_color']
        ) !!}
    </td>

    {{-- responsible --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][responsible]', null,
            ['class' => 'form-control input-sm vehicle_responsible']
        ) !!}
    </td>

    {{-- engine_number --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][engine_number]', null,
            ['class' => 'form-control input-sm vehicle_engine_number']
        ) !!}
    </td>

    {{-- vin_chassis --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][vin_chassis]', null,
            ['class' => 'form-control input-sm vehicle_vin_chassis']
        ) !!}
    </td>

    {{-- mi_km --}}
    <td>
        {!! Form::text(
            'vehicles[' . $row_count . '][mi_km]', null,
            ['class' => 'form-control input-sm vehicle_mi_km']
        ) !!}
    </td>

    {{-- Remove button --}}
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-xs remove_vehicle_row" title="{{ __('lang_v1.remove') }}">
            <i class="fa fa-times"></i>
        </button>
    </td>
</tr>

<input type="hidden" id="row_count_veh" value="{{ $row_count }}">