<div class="modal-header">
  <h3 class="modal-title" id="formModal">@lang('rrhh.add') @lang('rrhh.employee')</h3>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <form id="form_add" method="post">

    <div class="row">

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.name')</label>
          {!! Form::text("name", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.name'), 'id' => 'name']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.last_name')</label>
          {!! Form::text("last_name", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.last_name'), 'id' => 'last_name']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.gender')</label>
          {!! Form::select("gender", ['M' => __('rrhh.male'), 'F' => __('rrhh.female')], null,
          ['class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.gender'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.nationality')</label>
          {!! Form::select("nationality_id", $nationalities, null,
          ['class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.nationality'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.birthdate')</label>
          {!! Form::date("birthdate", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.birthdate'), 'id' => 'birthdate']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.dni')</label>
          {!! Form::text("dni", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => '00000000-0', 'id' => 'dni']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.tax_number')</label>
          {!! Form::text("tax_number", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => '0000-000000-000-0', 'id' => 'tax_number']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.marital_status')</label>
          {!! Form::select("civil_status_id", $civil_statuses, null,
          ['class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.marital_status'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.phone')</label>
          {!! Form::text("phone", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'phone']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>Whatsapp</label>
          {!! Form::text("whatsapp", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'whatsapp']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.email')</label>
          @show_tooltip(__('rrhh.tooltip_email'))
          {!! Form::email("email", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.email'), 'id' => 'email']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.social_security_number')</label>
          {!! Form::text("social_security_number", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.social_security_number'), 'id' => 'social_security_number']) !!}
        </div>
      </div>

      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.address')</label>
          {!! Form::text("address", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.address'), 'id' => 'address']) !!}
        </div>
      </div>
      

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.afp')</label>
          {!! Form::select("afp_id", $afps, null,
          ['id' => 'afp_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.afp'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.afp_number')</label>
          {!! Form::text("afp_number", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.afp_number'), 'id' => 'afp_number']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.date_admission')</label>
          {!! Form::date("date_admission", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.date_admission'), 'id' => 'date_admission']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.department')</label>
          {!! Form::select("department_id", $departments, null,
          ['id' => 'department_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.department'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.position')</label>
          {!! Form::select("position_id", $positions, null,
          ['id' => 'position_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.position'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.salary')</label>
          {!! Form::number("salary", null,
          ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.salary'), 'id' => 'salary', 'step' => '0.01', 'min' => '0.01']) !!}
        </div>
      </div>




    </div>
  </div>

</form> 
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-sm btn-success" id="btn_add_item" value="add">@lang('rrhh.save')</button>
  <button type="button" class="btn btn-sm btn-primary" id="btn_add_other" value='other'>@lang('rrhh.save_and_other')</button>
  <button type="button" class="btn btn-sm btn-danger" id="btn_add_complete" value='complete'>@lang('rrhh.save_and_complete')</button>
</div>

<script>

  $( document ).ready(function() {

    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    $("#name").focus();
    select2 = $('.select2').select2();
    
  });

  $("#btn_add_item").click(function() {

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
</script>

