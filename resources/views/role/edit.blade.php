@extends('layouts.app')
@section('title', __('role.edit_role'))
<style>
  hr {
    border: 0;
    clear:both;
    display:block;
    width: 96%;               
    background-color:#FFFF00;
    height: 12px;
  }
</style>
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'role.edit_role' )</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="boxform_u box-solid_u">
    <div class="box-header">
      <div class="box-tools">
        <a href="{!!URL::to('/roles')!!}" >
          <button id="btnUndo" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
        </a>
      </div>
    </div>
    <div class="box-body">
      {!! Form::open(['url' => action('RoleController@update', [$role->id]), 'method' => 'PUT', 'id' => 'role_form' ]) !!}      
      <div class="row col-md-12">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
              {!! Form::text('name', str_replace( '#' . auth()->user()->business_id, '', $role->name) , ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
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
              <div class="form-check form-check-inline">
                <label>
                  {!! Form::checkbox('is_service_staff', 1, $role->is_service_staff, 
                  [ 'class' => 'form-check-input']); !!} {{ __( 'restaurant.service_staff' ) }}
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
                <input type="checkbox" class="check_all form-check-input custom-control-input"> {{ __('role.select_all')}}
              </label>
            </div>
          </div>
          <div class="col-md-8" style="border-left: 2px solid gray;">

            @foreach($permissions as $permission)
            @if($permission->module_id == $module->id)

            
            <div class="form-check form-check-inline">
              <label>
                {!! Form::checkbox('permissions[]', $permission->name, in_array($permission->name, $role_permissions), 
                [ 'class' => 'form-check-input']); !!} {{ $permission->description }}
              </label>
            </div>


            @endif
            @endforeach


          </div> 
        </div>       
        <hr>
        
        @endforeach



        <!-- Permisos Sucursales -->
        @if(in_array('tables', $enabled_modules) && in_array('service_staff', $enabled_modules) )
        <div class="form-row check_group">
          <div class="col-md-2">
            <h4>@lang( 'restaurant.bookings' )</h4>
          </div>
          <div class="col-md-2" style="border-right: 2px solid gray">
            <div class="form-check form-check-inline">
              <label class="form-check-input" style="font-weight: bold; font-size: 16px;">
                <input type="checkbox" class="check_all form-check-input custom-control-input"> {{ __('role.select_all')}}
              </label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-check form-check-inline">
              <label>
                {!! Form::checkbox('permissions[]', 'crud_all_bookings', in_array('crud_all_bookings', $role_permissions), 
                [ 'class' => 'form-check-input']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-check form-check-inline">
              <label>
                {!! Form::checkbox('permissions[]', 'crud_own_bookings', in_array('crud_own_bookings', $role_permissions), 
                [ 'class' => 'form-check-input']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
              </label>
            </div>
          </div>  
        </div>
        <hr>
        @endif
        <!-- Fin Permisos Sucursales -->        
        <!-- Permisos Ubicaciones -->
        <div class="form-row check_group">
          <div class="col-md-2">
            <div class="form-check form-check-inline">
              <label class="form-check-input" style="font-weight: bold; text-transform: uppercase; font-size: 18px;">@lang('role.access_locations') @show_tooltip( __('tooltip.access_locations_permission'))</label>
            </div>
          </div>
          <div class="col-md-2" style="border-right: 2px solid gray">
            <div class="form-check form-check-inline">
              <label class="form-check-input" style="font-weight: bold; font-size: 16px;">
                <input type="checkbox" class="check_all form-check-input custom-control-input"> {{ __('role.select_all')}}
              </label>
            </div>
          </div>    
          @foreach($locations as $location)
          <div class="col-md-2">
            <div class="form-check form-check-inline">
              <label>
                {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, in_array('location.' . $location->id, $role_permissions), 
                [ 'class' => 'form-check-input']); !!} {{ $location->name }}
              </label>
            </div>
          </div>
          @endforeach  
        </div>
        <!-- Fin Permisos Ubicaciones -->
        <hr>
        <!-- Permisos Grupos de Venta -->
        @if(count($selling_price_groups) > 0)
        <div class="form-row check_group">
          <div class="col-md-2">
            <div class="form-check form-check-inline">
              <label class="form-check-input" style="font-weight: bold; text-transform: uppercase; font-size: 18px;">@lang( 'lang_v1.access_selling_price_groups' )</label>
            </div>
          </div>
          <div class="col-md-2" style="border-right: 2px solid gray">
            <div class="form-check form-check-inline">
              <label class="form-check-input" style="font-weight: bold; font-size: 16px;">
                <input type="checkbox" class="check_all form-check-input custom-control-input"> {{ __('role.select_all')}}
              </label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-check form-check-inline">
              <label>
                {!! Form::checkbox('permissions[]', 'access_default_selling_price', in_array('access_default_selling_price', $role_permissions), 
                [ 'class' => 'form-check-input']); !!} {{ __('lang_v1.default_selling_price') }}
              </label>
            </div>
          </div>
          @foreach($selling_price_groups as $selling_price_group)
          <div class="col-md-2">
            <div class="form-check form-check-inline">
              <label>
                {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id, in_array('selling_price_group.' . $selling_price_group->id, $role_permissions), 
                [ 'class' => 'form-check-input']); !!} {{ $selling_price_group->name }}
              </label>
            </div>
          </div>          
          @endforeach          
        </div>
        <hr>
        @endif
        <!-- Fin Permisos Grupos de venta -->
        <hr>
      </div>
      <div class="form-row check_group">
        @include('role.partials.module_permissions')
      </div>
      <hr>
      <div class="form-row check_group">
        <div class="col-md-9">
        </div>
        <div class="col-md-2">
          <button type="submit" id="btnEditRole" class="btn btn-primary pull-right">@lang('messages.save')</button>
        </div>
        <div class="col-md-1">
          <a href="{!!URL::to('/roles')!!}" >
            <button id="btnBack" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
          </a>
        </div>        
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</div>
</div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">
 $(document).on('submit', 'form#role_form', function(e) {
  $(this).find('button[type="submit"]').attr('disabled', true);
  $("#btnBack").prop('disabled', true);
  $("#btnUndo").prop('disabled', true);
});
</script>
@endsection