<div class="boxform_u box-solid_u">
  <div class="box-header">
    <div class="box-tools">
      <button type="button" id="btnUndo" class="btn btn-danger">@lang('messages.cancel')</button>
    </div>
  </div>
  <div class="box-body">
    {!! Form::open(['url' => action('RoleController@store'), 'method' => 'post', 'id' => 'role_add_form' ]) !!}
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <div class="row col-md-12">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
            {!! Form::text('name', null, ['required', 'id' => 'name', 'class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
          </div>
        </div>
      </div>
      @if(in_array('service_staff', $enabled_modules))
      <div class="row">
        <div class="col-md-2">
          <h4>@lang( 'lang_v1.user_type' )</h4>
        </div>
        <div class="col-md-9 col-md-offset-1">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('is_service_staff', 1, false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
              </label>
              @show_tooltip(__('restaurant.tooltip_service_staff'))
            </div>
          </div>
        </div>
      </div>
      @endif
      <div class="row">
        <div class="col-md-3">
          <label>@lang( 'user.permissions' ):</label> 
        </div>
      </div>


      @foreach($modules as $module)
      <div class="form-row check_group">
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label class="form-check-input" style="font-weight: bold; text-transform: uppercase; font-size: 18px;">{{ $module->name }}</label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label class="form-check-input" style="font-weight: bold; font-size: 16px;">
              <input type="checkbox" name="selectores" class="form-check-input custom-control-input"> {{ __('role.select_all')}}
            </label>
          </div>
        </div>
        <div class="col-md-8" style="border-left: 2px solid gray;">
          @foreach($permissions as $permission)
          @if($permission->module_id == $module->id)
          <div class="form-check form-check-inline">
            <label>
              {!! Form::checkbox('permissions[]', $permission->name, false, ['class' => 'form-check-input']); !!} {{ $permission->description }}
            </label>
          </div>
          @endif
          @endforeach
        </div>
      </div>
      <hr>
      @endforeach






      {{-- Permisos Sucursales/Bodegas --}}
      @if(in_array('tables', $enabled_modules) && in_array('service_staff', $enabled_modules) )
      <div class="form-row check_group">
        <div class="col-md-2">
          <h4>@lang('restaurant.bookings')</h4>
        </div>
        <div class="col-md-2"  style="border-right: 2px solid gray;">
          <div class="form-check form-check-inline">
            <label class="form-check-input" style="font-weight: bold; font-size: 16px;">
              <input type="checkbox" name="selectores" class="form-check-input custom-control-input"> {{ __('role.select_all')}}
            </label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label>
              {!! Form::checkbox('permissions[]', 'crud_all_bookings', false, 
              [ 'class' => 'form-check-input']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
            </label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label>
              {!! Form::checkbox('permissions[]', 'crud_own_bookings', false, 
              [ 'class' => 'form-check-input']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
            </label>
          </div>
        </div>
      </div>
      <hr>
      @endif
      {{-- Fin Permisos Sucursales/Bodegas --}}
      {{-- Permisos Ubicaciones --}}
      <div class="form-row check_group">
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label class="form-check-input" style="font-weight: bold; text-transform: uppercase; font-size: 18px;">@lang('role.access_locations') @show_tooltip( __('tooltip.access_locations_permission'))</label>
          </div>
        </div>
        <div class="col-md-2"  style="border-right: 2px solid gray;">
          <div class="form-check form-check-inline">
            <label class="form-check-input" style="font-weight: bold; font-size: 16px;">
              <input type="checkbox" name="selectores" class="form-check-input custom-control-input"> {{ __('role.select_all')}}
            </label>
          </div>
        </div>
        @foreach($locations as $location)
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label>
              {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, true, 
              [ 'class' => 'form-check-input']); !!} {{ $location->name }}
            </label>
          </div>
        </div>
        @endforeach
      </div>
      {{-- Fin Permisos Ubicaciones --}}
      <hr>
      {{-- Permisos Grupos de Precios --}}
      @if(count($selling_price_groups) > 0)
      <hr>
      <div class="form-row check_group">
        <div class="col-md-2">
          <label class="form-check-input" style="font-weight: bold; text-transform: uppercase; font-size: 18px;">@lang('lang_v1.access_selling_price_groups')</label>
        </div>
        <div class="col-md-2"  style="border-right: 2px solid gray;">
          <div class="form-check form-check-inline">
            <label class="form-check-input" style="font-weight: bold; font-size: 16px;">
              <input type="checkbox" name="selectores" class="form-check-input custom-control-input"> {{ __('role.select_all')}}
            </label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label>
              {!! Form::checkbox('permissions[]', 'access_default_selling_price', true, 
              [ 'class' => 'form-check-input']); !!} {{ __('lang_v1.default_selling_price') }}
            </label>
          </div>
        </div>
        @foreach($selling_price_groups as $selling_price_group)
        <div class="col-md-2">
          <div class="form-check form-check-inline">
            <label>
              {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id, false, 
              [ 'class' => 'form-check-input']); !!} {{ $selling_price_group->name }}
            </label>
          </div>
        </div>
        @endforeach   
      </div>
      <hr>
      @endif
      {{-- Fin Permisos Grupos de Precios --}}
      @include('role.partials.module_permissions')
      <div id="content" class="col-md-12" style="display: none;">
        <img src="{{ asset('img/loader.gif') }}" alt="loading" />
      </div>
      <div class="row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary" id="btnStoreRole">@lang( 'messages.save' )</button>
          <button type="button" class="btn btn-danger" id="btnBack">@lang('messages.cancel')</button>
        </div>
      </div>
      <hr>
    </div>
    {!! Form::close() !!}
  </div>
</div>