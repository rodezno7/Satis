@extends('layouts.app')
@section('title', __('rrhh.rrhh'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('rrhh.add') @lang('rrhh.employee')</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="boxform_u box-solid_u">
    {!! Form::open(['url' => action('EmployeesController@store'), 'method' => 'post', 'id' => 'form_add',
    'files' => true ]) !!}
    <div class="box-body">
      <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
            {!! Form::text("first_name", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.name'), 'id' => 'first_name', 'required'])
            !!}
          </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.last_name')</label> <span class="text-danger">*</span>
            {!! Form::text("last_name", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.last_name'), 'id' => 'last_name',
            'required'])!!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.gender')</label> <span class="text-danger">*</span>
            {!! Form::select("gender", ['M' => __('rrhh.male'), 'F' => __('rrhh.female')], null,
            ['class' => 'form-control form-control-sm select2', 'style' =>
            'width:100%;', 'required']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.nationality')</label> <span class="text-danger">*</span>
            {!! Form::select("nationality_id", $nationalities, null,
            ['class' => 'form-control form-control-sm select2', 'style' => 'width: 100%;', 'required']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.birthdate')</label> <span class="text-danger">*</span>
            {!! Form::text("birth_date", null, ['class' => 'form-control form-control-sm', 'id' => 'birth_date', 'required'])!!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.dni')</label> <span class="text-danger">*</span>
            {!! Form::text("dni", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => '00000000-0', 'id' => 'dni', 'required']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.tax_number')</label>
            {!! Form::text("tax_number", null,
            ['class' => 'form-control form-control-sm', 'id' => 'tax_number'])!!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.marital_status')</label> <span class="text-danger">*</span>
            {!! Form::select("civil_status_id", $civil_statuses, null,
            ['class' => 'form-control form-control-sm select2', 'style' =>
            'width: 100%;', 'required']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.phone')</label>
            {!! Form::text("phone", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'phone']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.mobile_phone')</label>
            {!! Form::text("mobile", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'mobile']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.email')</label> <span class="text-danger">*</span>
            @show_tooltip(__('rrhh.tooltip_email'))
            {!! Form::email("email", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.email'), 'id' => 'email', 'required'])
            !!}
          </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.address')</label> <span class="text-danger">*</span>
            {!! Form::text("address", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.address'), 'id' => 'address',
            'required']) !!}
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.social_security_number')</label>
            {!! Form::text("social_security_number", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.social_security_number'), 'id' =>
            'social_security_number']) !!}
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.afp')</label>
            {!! Form::select("afp_id", $afps, null,
            ['id' => 'afp_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.afp'),
            'style'
            => 'width: 100%;']) !!}
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.afp_number')</label>
            {!! Form::text("afp_number", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.afp_number'), 'id' => 'afp_number'])
            !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.date_admission')</label>
            {!! Form::text("date_admission", @format_date('now'), ['class' => 'form-control form-control-sm', 'id' => 'date_admission'])!!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.department')</label>
            {!! Form::select("department_id", $departments, null,
            ['id' => 'department_id', 'class' => 'form-control form-control-sm select2', 'placeholder' =>
            __('rrhh.department'), 'style' => 'width: 100%;']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.position')</label>
            {!! Form::select("position_id", $positions, null,
            ['id' => 'position_id', 'class' => 'form-control form-control-sm select2', 'placeholder' =>
            __('rrhh.position'), 'style' => 'width: 100%;']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.salary')</label>
            {!! Form::number("salary", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.salary'), 'id' => 'salary', 'step' =>
            '0.01', 'min' => '0.01']) !!}
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="form-group">
            {!! Form::label('photo', __('rrhh.photo') . ':') !!}
            {!! Form::file('photo', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
            <small class="help-block">@lang('purchase.max_file_size', ['size' =>
              (config('constants.document_size_limit') / 1000000)]).</small>
          </div>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>
              {{ __('employees.has_user') }} </label>
              {!! Form::checkbox('chk_has_user', '0', false, ['id' => 'chk_has_user', 'onClick' => 'showUserOption()']) !!}
            </label>
          </div>
        </div>
      </div>
      <div id="user_modal_option" style="display: none">
        <div class="row">
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="form-group">
              {!! Form::label('username', __('employees.username') . ' : ') !!}
              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' =>
              __('employees.username')]) !!}
            </div>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="form-group">
              {!! Form::label('role', __('employees.role') . ' : ') !!}
              {!! Form::select('role', $roles, null, ['id' => 'role', 'class' => 'form-control form-control-sm select2', 'style' => 'width: 100%;']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-check form-check-inline">
              <label class="form-check-input">
                {{ __('employees.pass_manual') }} {!! Form::radio('rdb_pass_mode', '0', true, ['class'
                => 'form-check-input', 'id' => 'rdb_pass_manual', 'onClick' => 'showPassMode()']) !!}
                @show_tooltip(__('lang_v1.tooltip_enable_password_manual'))
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check form-check-inline">
              <label class="form-check-input">
                {{ __('employees.pass_auto') }} {!! Form::radio('rdb_pass_mode', 'generated', false,
                ['class' => 'form-check-input', 'id' => 'rdb_pass_auto', 'onClick' => 'showPassMode()'])
                !!}
                @show_tooltip(__('lang_v1.tooltip_enable_password_generated'))
              </label>
            </div>
          </div>
        </div>
        <div id="pass_mode">
          <br>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label('username', __('business.password') . ' : ') !!}
                <input id="password" name="password" type="password" class="form-control" ,
                  placeholder="{{ __('business.password') }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label('username', __('business.confirm_password') . ' : ') !!}
                <input id="password_confirm" type="password" class="form-control" ,
                  placeholder="{{ __('business.confirm_password') }}">
              </div>
            </div>
          </div>
        </div>
        {{-- <br> --}}
        <div class="row">
          <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="form-group">
              <label class="form-check-input">
                {{ __('employees.commission') }} 
                {!! Form::checkbox('commission', '0', false, ['id' => 'chk_commission', 'onClick' => 'commision_enable()']) !!}
              </label>
            </div>
          </div>
        </div>
        <div class="row" id="commision_div" style="display: none">
          <div class="col-lg-3 col-md-3 col-sm-6">
            <div class="form-group">
              {!! Form::number('commision_amount', null, 
              ['class' => 'form-control', 'id' => 'commision_amount', 'placeholder' => __('employees.commision_amount')]) !!}
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12 text-right">
          <input type="hidden" name="submit_type" id="submit_type">
          <div class="btn-group">
            <div class="btn-group dropleft" role="group">
              <button type="button" class="btn btn-primary submit_employee_form" value="add">@lang('rrhh.save')</button>
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="fa fa-sort-desc"></i>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="btn-primary dropdown-menu dropdown-menu-right" role="menu">
                <li>
                  <a href="#" type="button" class="submit_employee_form" value='other'>
                    @lang('rrhh.save_and_other')
                  </a>
                </li>
                <li>
                  <a href="#" type="button" class="submit_employee_form" value='complete'>
                    @lang('rrhh.save_and_complete')
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <a href="{!!URL::to('/rrhh-employees')!!}">
            <button id="cancel_product" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
          </a>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
</section>
@endsection
@section('css')
<style>
  .dropdown-menu>li>a:hover {
    background-color: #e1e3e9;
    color: black;
  }

  .dropdown-menu>li>a {
    color: white;
  }
</style>
@endsection
@section('javascript')
<script>
  $( document ).ready(function() {
    //$.fn.modal.Constructor.prototype.enforceFocus = function() {};
    //$("#first_name").focus();
    $('.select2').select2(); 

    let dui = document.getElementById("dni");
    $(dui).mask("00000000-0");

    var fechaMaxima = new Date();
    fechaMaxima.setFullYear(fechaMaxima.getFullYear() - 18);
    console.log(fechaMaxima);
    var fechaMinima = new Date();
    fechaMinima.setFullYear(fechaMinima.getFullYear() - 99);
    console.log(fechaMinima);

    $('#birth_date').datepicker({
      autoclose: true,
      format: datepicker_date_format,
      minDate: fechaMinima,
      maxDate: fechaMaxima,
    });

    $( "#birth_date" ).datepicker( "setDate", fechaMaxima );

    $('#date_admission').datepicker({
      autoclose: true,
      format: datepicker_date_format
    });

    $("#dni").keyup(function () {
      var value = $(this).val();
      $("#tax_number").val(value);
    });

    showUserOption();
    commision_enable();
    showPassMode();
  });

  $('#dni').on('change', function() {
    let valor = $(this).val();
    let route = '/rrhh-employees/verified_document/'+'dni'+'/'+valor;
    $.get(route, function(data, status) {
      if (data.success == true) {
        Swal.fire({ title: data.msg, icon: "error", timer: 3000, showConfirmButton: true, });
      }
    });
  });

  $('#tax_number').on('change', function() {
    let tax_number = $(this).val();
    let dni = $('#dni').val();
    if(dni != tax_number){
      let route = '/rrhh-employees/verified_document/'+'tax_number'+'/'+tax_number;
      $.get(route, function(data, status) {
        if (data.success == true) {
          Swal.fire({ title: data.msg, icon: "error", timer: 3000, showConfirmButton: true, });
        }
      });
    }
  });


  $(document).on( 'click', '.submit_employee_form', function(e){
		e.preventDefault();
    var submit_type = $(this).attr('value');
		$('#submit_type').val(submit_type);
		if($("form#form_add").valid()) {
			$("form#form_add").submit();
		}
	});

  var img_fileinput_setting = {
		'showUpload': false,
		'showPreview': true,
		'browseLabel': LANG.file_browse_label,
		'removeLabel': LANG.remove,
		'previewSettings': {
			image: {
				width: "100%",
				height: "100%",
				'max-width': "100%",
				'max-height': "100%",
			}
		}
	};
  $("#upload_image").fileinput(img_fileinput_setting);


  function showUserOption() {
    if ($("#chk_has_user").is(":checked")) {
      $("#chk_has_user").val('has_user');
      $("#user_modal_option").show();
      $("#username").prop('required', true);
      $("#role").prop('required', true);
    } else {
      $("#chk_has_user").val('0');
      $("#user_modal_option").hide();
      $("#username").prop('required', false);
      $("#role").prop('required', false);
    }
  }

  function commision_enable() {
    if ($("#chk_commission").is(":checked")) {
      $("#chk_commission").val('has_commission');
      $("#commision_div").show();
      $("#commision_amount").prop('required', true);
      $("#commision_amount").focus();
    } else {
      $("#chk_commission").val('0');
      $("#commision_div").hide();
      $("#commision_amount").prop('required', false);
      $("#commision_amount").val('');
    }
  }

  function showPassMode() {
    if ($("#rdb_pass_manual").is(":checked")) {
      $("#pass_mode").show();
    } else if ($("#rdb_pass_auto").is(":checked")) {
      $("#pass_mode").hide();
    }
  }
  
</script>
@endsection