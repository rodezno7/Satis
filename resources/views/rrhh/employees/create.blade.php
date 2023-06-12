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
    {!! Form::open(['url' => action('HumanResourceEmployeeController@store'), 'method' => 'post', 'id' => 'form_add',
    'files' => true ]) !!}
    <div class="box-body">
      <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
            {!! Form::text("name", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.name'), 'id' => 'name', 'required'])
            !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
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
            ['class' => 'form-control form-control-sm select2', 'style' =>
            'width: 100%;', 'required']) !!}
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@lang('rrhh.birthdate')</label> <span class="text-danger">*</span>
            {!! Form::date("birthdate", null,
            ['class' => 'form-control form-control-sm', 'id' => 'birthdate',
            'required'])!!}
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
            <label>@lang('rrhh.tax_number')</label> <span class="text-danger">*</span>
            {!! Form::text("tax_number", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => '0000-000000-000-0', 'id' => 'tax_number',
            'required'])!!}
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
            {!! Form::text("mobile_phone", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'mobile_phone']) !!}
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
            {!! Form::date("date_admission", null,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.date_admission'), 'id' =>
            'date_admission']) !!}
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
            <button id="cancel_product" type="button"
              class="btn btn-danger">@lang('messages.cancel')</button>
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
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    $("#name").focus();
    $('.select2').select2(); 
    let dui = document.getElementById("dni");
    $(dui).mask("00000000-0");

    let nit = document.getElementById('tax_number');
    $(nit).mask('0000-000000-000-0');
  });

  $('#dni').on('change', function() {
    let valor = $(this).val();
    let route = '/rrhh-employees/verified_document/'+'dni'+'/'+valor;
    $.get(route, function(data, status) {
      if (data.success == true) {
        Swal.fire({ title: data.msg, icon: "error", timer: 3000, showConfirmButton: true, });
      } else {
        Swal.fire({ title: data.msg, icon: "success", timer:3000, });
      }
    });
  });

  $('#tax_number').on('change', function() {
    let valor = $(this).val();
    let route = '/rrhh-employees/verified_document/'+'tax_number'+'/'+valor;
    $.get(route, function(data, status) {
      if (data.success == true) {
        Swal.fire({ title: data.msg, icon: "error", timer: 3000, showConfirmButton: true, });
      } else {
        Swal.fire({ title: data.msg, icon: "success", timer:3000, });
      }
    });
  });
  

  $(document).on( 'click', '.submit_employee_form', function(e){
		e.preventDefault();
    var submit_type = $(this).attr('value');
		$('#submit_type').val(submit_type);
		if($("form#form_add").valid()) {
			$("form#form_add").submit();
		}
	});

  // $("#btn_add_item").click(function() {

  //   $("#btn_add_item").prop("disabled", true);
  //   $("#btn_add_other").prop("disabled", true);
  //   $("#btn_add_complete").prop("disabled", true);
  //   route = "/rrhh-employees";
  //   datastring = $("#form_add").serialize();
  //   token = $("#token").val();
  //   $.ajax({
  //     url: route,
  //     headers: {'X-CSRF-TOKEN': token},
  //     type: 'POST',
  //     dataType: 'json',
  //     data: datastring,
  //     success:function(result) {
  //       if(result.success == true) {
  //         $("#btn_add_item").prop("disabled", false);
  //         $("#btn_add_other").prop("disabled", false);
  //         $("#btn_add_complete").prop("disabled", false);
  //         Swal.fire
  //         ({
  //           title: result.msg,
  //           icon: "success",
  //           timer: 2000,
  //           showConfirmButton: false,
  //         });          
  //         $("#employees-table").DataTable().ajax.reload(null, false);

          
  //         $('#modal').modal('hide');


  //       } else {
  //         $("#btn_add_item").prop("disabled", false);
  //         $("#btn_add_other").prop("disabled", false);
  //         $("#btn_add_complete").prop("disabled", false);
  //         Swal.fire
  //         ({
  //           title: result.msg,
  //           icon: "error",
  //         });
  //       }
  //     },
  //     error:function(msj){
  //       $("#btn_add_item").prop("disabled", false);
  //       $("#btn_add_other").prop("disabled", false);
  //       $("#btn_add_complete").prop("disabled", false);
  //       errormessages = "";
  //       $.each(msj.responseJSON.errors, function(i, field){
  //         errormessages+="<li>"+field+"</li>";
  //       });
  //       Swal.fire
  //       ({
  //         title: "@lang('rrhh.error_list')",
  //         icon: "error",
  //         html: "<ul>"+ errormessages+ "</ul>",
  //       });
  //     }
  //   });
  // });

  $("#btn_add_other").click(function() {

    $("#btn_add_item").prop("disabled", true);
    $("#btn_add_other").prop("disabled", true);
    $("#btn_add_complete").prop("disabled", true);
    route = "/rrhh-employees";
    datastring = $("#form_add").serialize();
    token = $("#token").val();
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: 'POST',
      dataType: 'json',
      data: datastring,
      success:function(result) {
        if(result.success == true) {
          $("#btn_add_item").prop("disabled", false);
          $("#btn_add_other").prop("disabled", false);
          $("#btn_add_complete").prop("disabled", false);
          Swal.fire
          ({
            title: result.msg,
            icon: "success",
            timer: 2000,
            showConfirmButton: false,
          });

          $("#employees-table").DataTable().ajax.reload(null, false);

          $("#modal_content").html('');
          var url = '{!!URL::to('/rrhh-employees/create')!!}';
          $.get(url, function(data) {
            $("#modal_content").html(data);

          });



        } else {
          $("#btn_add_item").prop("disabled", false);
          $("#btn_add_other").prop("disabled", false);
          $("#btn_add_complete").prop("disabled", false);
          Swal.fire
          ({
            title: result.msg,
            icon: "error",
          });
        }
      },
      error:function(msj){
        $("#btn_add_item").prop("disabled", false);
        $("#btn_add_other").prop("disabled", false);
        $("#btn_add_complete").prop("disabled", false);
        errormessages = "";
        $.each(msj.responseJSON.errors, function(i, field){
          errormessages+="<li>"+field+"</li>";
        });
        Swal.fire
        ({
          title: "@lang('rrhh.error_list')",
          icon: "error",
          html: "<ul>"+ errormessages+ "</ul>",
        });
      }
    });
  });

  $("#btn_add_complete").click(function() {

    $("#btn_add_item").prop("disabled", true);
    $("#btn_add_other").prop("disabled", true);
    $("#btn_add_complete").prop("disabled", true);
    route = "/rrhh-employees";
    datastring = $("#form_add").serialize();
    token = $("#token").val();
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: 'POST',
      dataType: 'json',
      data: datastring,
      success:function(result) {
        if(result.success == true) {
          $("#btn_add_item").prop("disabled", false);
          $("#btn_add_other").prop("disabled", false);
          $("#btn_add_complete").prop("disabled", false);
          Swal.fire
          ({
            title: result.msg,
            icon: "success",
            timer: 2000,
            showConfirmButton: false,
          });

          $("#employees-table").DataTable().ajax.reload(null, false);

          $('#modal').modal('hide');
          
          $("#div_content").html('');
          var url = '{!!URL::to('/rrhh-employees/:id/edit')!!}';
          url = url.replace(':id', result.id);
          $.get(url, function(data) {
            
            

            $("#div_content").html(data);
            
            
          });



        } else {
          $("#btn_add_item").prop("disabled", false);
          $("#btn_add_other").prop("disabled", false);
          $("#btn_add_complete").prop("disabled", false);
          Swal.fire
          ({
            title: result.msg,
            icon: "error",
          });
        }
      },
      error:function(msj){
        $("#btn_add_item").prop("disabled", false);
        $("#btn_add_other").prop("disabled", false);
        $("#btn_add_complete").prop("disabled", false);
        errormessages = "";
        $.each(msj.responseJSON.errors, function(i, field){
          errormessages+="<li>"+field+"</li>";
        });
        Swal.fire
        ({
          title: "@lang('rrhh.error_list')",
          icon: "error",
          html: "<ul>"+ errormessages+ "</ul>",
        });
      }
    });
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
</script>
@endsection