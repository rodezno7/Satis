{!! Form::open(['method' => 'post', 'id' => 'form_edit_personnel_action' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('rrhh.personnel_actions')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.types_personnel_actions')</label> <span class="text-danger">*</span>
        <select name="rrhh_type_personnel_action_id" id="rrhh_type_personnel_action_id" class="form-control form-control-sm select2" 
          style="width: 100%;" required disabled>
          <option value="{{ $personnelAction[0]->type_id }}">{{ $personnelAction[0]->type }}</option>
        </select>
      </div>
    </div>

    <div id="div_salary_history">
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.previous_salary')</label>
          {!! Form::number("previous_salary", $previousSalary->salary,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.previous_salary'), 'id' =>
            'previous_salary', 'step' => '0.01', 'min' => '0.01', 'disabled' => true]) !!}
        </div>
      </div>
  
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.new_salary')</label> <span class="text-danger">*</span>
          {!! Form::number("new_salary", $newSalary->salary,
            ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.new_salary'), 'id' => 'new_salary', 'step'
            => '0.01', 'min' => '0.01']) !!}
        </div>
      </div>
    </div>

    <div id="div_period">
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.start_date')</label> <span class="text-danger">*</span>
          {!! Form::text("start_date", @format_date($personnelAction[0]->start_date), ['class' => 'form-control form-control-sm', 'id' => 'start_date'])!!}
        </div>
      </div>
  
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.end_date')</label> <span class="text-danger">*</span>
          {!! Form::text("end_date", @format_date($personnelAction[0]->end_date), ['class' => 'form-control form-control-sm', 'id' => 'end_date'])!!}
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12" id="div_effective_date">
      <div class="form-group">
        <label>@lang('rrhh.in_force_from')</label> <span class="text-danger">*</span>
        {!! Form::text("effective_date", @format_date($personnelAction[0]->effective_date), ['class' => 'form-control form-control-sm', 'id' => 'effective_date',
        'required'])!!}
      </div>
    </div>

    <div id="div_position_history">
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.department')</label> <span class="text-danger">*</span>
          {!! Form::select("department_id", $departments, ($positionHistory != null) ? $positionHistory->department_id : null,
          ['id' => 'department_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.department'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>
  
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
          <label>@lang('rrhh.position')</label> <span class="text-danger">*</span>
          {!! Form::select("position1_id", $positions, ($positionHistory != null) ? $positionHistory->position1_id : null,
          ['id' => 'position1_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.position'), 'style' => 'width: 100%;']) !!}
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12" id="div_payment">
      <div class="form-group">
        <label>@lang('rrhh.way_to_pay')</label> <span class="text-danger">*</span>
        {!! Form::select("payment_id", $payments,($employee->payment_id != null) ? $employee->payment_id : null,
        ['id' => 'payment_id', 'class' => 'form-control form-control-sm select2', 'placeholder' =>  __('rrhh.way_to_pay'), 
        'style' => 'width: 100%;']) !!}
      </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12" id="div_bank">
      <div class="form-group">
        <label>@lang('rrhh.bank')</label> <span class="text-danger">*</span>
        {!! Form::select("bank_id", $banks, ($employee->bank_id != null) ? $employee->bank_id : null,
        ['id' => 'bank_id', 'class' => 'form-control form-control-sm select2', 'placeholder' => __('rrhh.bank'), 'style' => 'width: 100%;']) !!}
      </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12" id="div_bank_account">
      <div class="form-group">
        <label>@lang('rrhh.bank_account')</label> <span class="text-danger">*</span>
        {!! Form::number("bank_account", ($employee->bank_account != null) ? $employee->bank_account : null,
        ['class' => 'form-control form-control-sm', 'placeholder' =>  __('rrhh.bank_account'), 'id' => 'bank_account']) !!}
      </div>
    </div>

    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12" id="div_authorizer">
      <div class="form-group">
        <label>@lang('rrhh.authorizer')</label> <span class="text-danger">*</span> @show_tooltip(__('rrhh.message_authorizer'))
        <select name="user_id[]" id="user_id" class="form-control form-control-sm select2" style="width: 100%;" multiple>
          @foreach ($users as $user)
						@php
							$exist = 0;
						@endphp
						@foreach ($authorizers as $authorizer)
							@if($user->id == $authorizer->user_id)
								@php
									$exist = $user->id;
								@endphp
							@endif
						@endforeach
						@if ($exist == $user->id)
              <option value="{{ $user->id }}" selected>{{ $user->first_name }} {{ $user->last_name }} - {{ $user->email }}</option>
						@else
              <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} - {{ $user->email }}</option>
						@endif
					@endforeach
        </select>
      </div>
    </div>
    
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.description')</label> <span class="text-danger">*</span>
        {!! Form::textarea('description', $personnelAction[0]->description, ['id' => 'description', 'placeholder' => __('rrhh.description'), 'class' => 'form-control', 'rows' => 4]);
        !!}
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <input type="hidden" name="employee_id" value="{{ $employee->id }}" id="employee_id">
  <input type="hidden" name="id" value="{{ $personnelAction[0]->id }}" id="id">
  <button type="button" class="btn btn-primary" id="btn_edit_personnel_action">@lang('rrhh.update')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang('messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
  $( document ).ready(function() {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    select2 = $('.select2').select2();
      
    $('#start_date').datepicker({
      autoclose: true,
      format: datepicker_date_format,
    });

    $('#end_date').datepicker({
      autoclose: true,
      format: datepicker_date_format,
    });

    var fechaMaxima = new Date();
    fechaMaxima = fechaMaxima.toLocaleDateString("es-ES", { day: '2-digit', month: '2-digit', year: 'numeric' });

    if($('#effective_date').val != ''){
      $('#effective_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
      });
    }else{
      $('#effective_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
        startDate: fechaMaxima,
      });
      $("#effective_date").datepicker("setDate", fechaMaxima);
    }

    typePersonnelAction();
  });
      
  $('#rrhh_type_personnel_action_id').on('change', function() {
		typePersonnelAction();
	});

  function typePersonnelAction(){
    let type_personnel_action = $('#rrhh_type_personnel_action_id').val();
    let required_authorization = {!! json_encode($personnelAction[0]->required_authorization) !!};
    var actions = {!! json_encode($actions) !!};

    $('#div_position_history').hide();
    $("#department_id").prop('required', false);
		$("#position1_id").prop('required', false);

    $('#div_salary_history').hide();
    $("#new_salary").prop('required', false);

    $('#div_period').hide();
    $("#start_date").prop('required', false);
		$("#end_date").prop('required', false);

    $('#div_bank_account').hide();
    $("#bank_account").prop('required', false);

    $('#div_payment').hide();
    $("#payment_id").prop('required', false);

    $('#div_bank').hide();
    $("#bank_id").prop('required', false);

    $('#div_effective_date').hide();
		$("#effective_date").prop('required', false);
		
    //Evaluando si la accion de personal requiere autorizacion
    if(required_authorization == 1){ // requiere autorizacion
			$('#div_authorizer').show();
			$("#user_id").prop('required', true);
		}else{
			$('#div_authorizer').hide();
			$("#user_id").prop('required', false);
		}

    //Evaluaciones las acciones que requiere realizar el tipo de accion de personal
    actions.forEach(function(action) {
			if (action.rrhh_type_personnel_action_id == type_personnel_action) {
				if(action.rrhh_required_action_id == 2){ // Cambiar departamento/puesto
					$('#div_position_history').show();
          $("#department_id").prop('required', true);
					$("#position1_id").prop('required', true);
          $('#div_effective_date').show();
					$("#effective_date").prop('required', true);
				}

        if(action.rrhh_required_action_id == 3){ // Cambiar salario
				  $('#div_salary_history').show();
					$("#new_salary").prop('required', true);
          $('#div_effective_date').show();
					$("#effective_date").prop('required', true);
				}

        if(action.rrhh_required_action_id == 4){ // Seleccionar un periodo en específico
				  $('#div_period').show();
          $("#start_date").prop('required', true);
					$("#end_date").prop('required', true);
				}

        if(action.rrhh_required_action_id == 5){ // Cambiar cuenta bancaria
				  $('#div_bank_account').show();
          $("#bank_account").prop('required', true); 
				}

        if(action.rrhh_required_action_id == 6){ // Cambiar forma de pago
				  $('#div_payment').show();
          $("#payment_id").prop('required', true);
          showBankInformation();
				}

        if(action.rrhh_required_action_id == 7){ // Seleccionar la fecha en que entra en vigor
				  $('#div_effective_date').show();
					$("#effective_date").prop('required', true);
				}
			}
		});
  }


  function showBankInformation() 
  {
		selected_option = $( "#payment_id option:selected" ).text();

		if (selected_option == 'Transferencia bancaria') {
      $('#div_bank').show();
      $("#bank_id").prop('required', true);
      $('#div_bank_account').show();
      $("#bank_account").prop('required', true);
		} else {
			$('#div_bank').hide();
      $("#bank_id").prop('required', false);
      $('#div_bank_account').hide();
      $("#bank_account").prop('required', false);
			$('#bank_id').val('').change();
			$('#bank_account').val('');
		}
	}

	$('#payment_id').change(function() {
		showBankInformation();
	});
      

  $("#btn_edit_personnel_action").click(function() {
    $('#btn_edit_personnel_action').attr('disabled', 'disabled');
    route = "/rrhh-personnel-action-update";    
    token = $("#token").val();
      
    var form = $("#form_edit_personnel_action");
    var formData = new FormData(form[0]);
    
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: 'POST',
      processData: false,
      contentType: false,       
      data: formData,
      success:function(result) {
        if(result.success == true) {
          getPersonnelActions($('#employee_id').val());
          Swal.fire
          ({
            title: result.msg,
            icon: "success",
            timer: 1000,
            showConfirmButton: false,
          });
          $('#modal_personnel_action').modal('hide').data( 'bs.modal', null );
        }
        else {
          $('#btn_edit_personnel_action').removeAttr('disabled');
          Swal.fire
          ({
            title: result.msg,
            icon: "error",
          });
        }
      },
      error:function(msj){
        $('#btn_edit_personnel_action').removeAttr('disabled');
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
  
  function closeModal(){
    $('#modal_action').modal({backdrop: 'static'});
    $('#modal_personnel_action').modal( 'hide' ).data( 'bs.modal', null );
  }
</script>