@extends('layouts.app')
@section('title', __('rrhh.rrhh'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="box-title">@lang( 'rrhh.employee')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title">@lang( 'rrhh.employee_information' )</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover" width="100%">
                            <tr class="text-center">
                                <td>
                                    <img src="{{ asset($route) }}" width="125px" height="150px">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.code')</strong>
                                    @if( !empty($employee->agent_code))
                                        {{ $employee->agent_code }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.names'):</strong>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 33%;">
                                    <strong>@lang('rrhh.gender'):</strong>
                                    @if( !empty($employee->gender))
                                        @if ($employee->gender == 'M')
                                        @lang('rrhh.male')
                                        @else
                                        @lang('rrhh.female')
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                    
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover" width="100%">
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.marital_status'):</strong>
                                    @if( !empty($employee->civil_status_id))
                                        {{ $employee->civilStatus->value }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td style="width: 33%;">
                                    <strong>@lang('rrhh.birthdate'):</strong>
                                    @if( !empty($employee->birth_date))
                                        {{ @format_date($employee->birth_date) }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.nationality'):</strong>
                                    @if( !empty($employee->nationality_id))
                                        {{ $employee->nationality->value }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 34%;">
                                    <strong>@lang('rrhh.status'):</strong>
                                    @if( !empty($employee->status))
                                        @if($employee->status == 1)
                                        @lang('rrhh.active')
                                        @else
                                        @lang('rrhh.inactive')
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.phone'):</strong>
                                    @if( !empty($employee->phone))
                                        {{ $employee->phone }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.mobile_phone'):</strong>
                                    @if( !empty($employee->mobile))
                                        {{ $employee->mobile }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <strong>@lang('rrhh.email'):</strong>
                                    @if( !empty($employee->email))
                                        {{ $employee->email }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.check_payment'):</strong>
                                    @if( !empty($employee->check_payment))
                                        @if($employee->check_payment == 1)
                                        @lang('rrhh.yes')
                                        @else
                                        @lang('rrhh.no')
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.extra_hours'):</strong>
                                    @if( !empty($employee->extra_hours))
                                        @if($employee->extra_hours == 1)
                                        @lang('rrhh.yes')
                                        @else
                                        @lang('rrhh.no')
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.foreign_tax'):</strong>
                                    @if( !empty($employee->foreign_tax))
                                        @if($employee->foreign_tax == 1)
                                        @lang('rrhh.yes')
                                        @else
                                        @lang('rrhh.no')
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.state'):</strong>
                                    @if( !empty($employee->state_id))
                                        @if( isset($employee->state->name))
                                        {{ $employee->state->name }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.city'):</strong>
                                    @if( !empty($employee->city_id))
                                        @if( isset($employee->city->name))
                                        {{ $employee->city->name }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td colspan="2">
                                    <strong>@lang('rrhh.address'):</strong>
                                    @if( !empty($employee->address))
                                        {{ $employee->address }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.profession_occupation'):</strong>
                                    @if( !empty($employee->profession_id))
                                        @if( isset($employee->profession->value))
                                        {{ $employee->profession->value }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.date_admission'):</strong>
                                    @if( !empty($employee->date_admission))
                                        {{ @format_date($employee->date_admission) }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.salary'):</strong>
                                    <span class="display_currency" data-currency_symbol="true">{{ $employee->salary }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.department'):</strong>
                                    @if( !empty($employee->department_id))
                                        @if( isset($employee->department->value))
                                        {{ $employee->department->value }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.position'):</strong>
                                    @if( !empty($employee->position1_id))
                                        @if( isset($employee->position->value))
                                        {{ $employee->position->value }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.afp'):</strong>
                                    @if( !empty($employee->afp_id))
                                        @if( isset($employee->afp->value))
                                        {{ $employee->afp->value }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.type_employee'):</strong>
                                    @if( !empty($employee->type_id))
                                        @if( isset($employee->type->value))
                                        {{ $employee->type->value }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.bank'):</strong>
                                    @if( !empty($employee->bank_id))
                                        @if( isset($employee->bank->name))
                                        {{ $employee->bank->name }}
                                        @else
                                        N/A
                                        @endif
                                    @else 
                                        N/A
                                    @endif
                                    
                                </td>
                                <td>
                                    <strong>@lang('rrhh.bank_account'):</strong>
                                    @if( !empty($employee->bank_account))
                                        {{ $employee->bank_account }}
                                    @else 
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title">@lang( 'rrhh.documents' )</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="documents-table">
                        <thead>
                          <tr class="active">
                            <th width="20%">@lang('rrhh.document_type')</th>
                            <th width="20%">@lang('rrhh.state_expedition')</th>
                            <th width="20%">@lang('rrhh.city_expedition')</th>
                            <th width="15%">@lang('rrhh.number')</th>
                            <th width="15%">@lang('rrhh.file')</th>
                          </tr>
                        </thead>
                        <tbody id="referencesItems">
                          @if (count($documents) > 0)
                              @foreach($documents as $item)
                                <tr>
                                  <td>{{ $item->type }}</td>
                                  <td>{{ $item->state }}</td>
                                  <td>{{ $item->city }}</td>
                                  <td>{{ $item->number }}</td>
                                  <td><button type="button" onClick="viewFile({{ $item->id }})" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></button></td>
                                </tr>
                              @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_photo" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content" id="modal_content_photo">
      
          </div>
        </div>
      </div>
</section>
@endsection
@section('javascript')
<script type="text/javascript">
        function viewFile(id) 
	{
		$("#modal_content_photo").html('');
		var url = "{!!URL::to('/rrhh-documents-viewFile/:id')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_photo").html(data);
			$('#modal_photo').modal({backdrop: 'static'});
		});
	}
    </script>
@endsection