@extends('layouts.app')
@section('title', __('rrhh.rrhh'))
@section('content')

<!-- Main content -->
<section class="content">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist" id="myTab">
        <li class="active"><a href="#details" role="tab" data-toggle="tab" style="font-size: 15px !important;">{{ __('rrhh.personal_data') }}</a></li>
        <li><a href="#history" role="tab" data-toggle="tab" style="font-size: 15px !important;">{{ __('rrhh.history') }}</a></li>
        <li><a href="#documents" role="tab" data-toggle="tab" style="font-size: 15px !important;">{{ __('rrhh.documents') }}</a></li>
        <li><a href="#contracts" role="tab" data-toggle="tab" style="font-size: 15px !important;">{{ __('rrhh.contracts') }}</a></li>
        <li><a href="#absence_inability" role="tab" data-toggle="tab" style="font-size: 15px !important;">{{ __('rrhh.absence_inability') }}</a></li>
        <li><a href="#personnel_action" role="tab" data-toggle="tab" style="font-size: 15px !important;">{{ __('rrhh.personnel_action') }}</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="details">
            <div class="boxform_u box-solid_u" style="border-top: 0px solid #d2d6de !important; border-radius: 0px !important;">
                <div class="box-body">
                    <div class="row">
                        @if ($employee->curriculum_vitae != null)
                        <div class="col-lg-12">
                            <a href="/rrhh-employees-downloadCv/{{ $employee->id }}" class="btn btn-primary pull-right"><i class="fa fa-file" aria-hidden="true"></i> {{ __('messages.download_cv') }}</a>
                        </div>
                        
                        @endif
                        <div class="col-lg-4">
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed" width="100%">
                                    <tr class="text-center">
                                        <td>
                                            <img src="{{ asset($route) }}" width="150px" height="150px" alt="@lang('employees.employee_photo')">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>@lang('rrhh.code'):</strong>
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
                                    <tr>
                                        <td style="width: 33%;">
                                            <strong>@lang('rrhh.dni'):</strong>
                                            @if( !empty($employee->dni))
                                            {{ $employee->dni }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 33%;">
                                            <strong>@lang('rrhh.tax_number'):</strong>
                                            @if( !empty($employee->tax_number))
                                            {{ $employee->tax_number }}
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
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed" width="100%">
                                    <tr>
                                        <td>
                                            <strong>@lang('rrhh.marital_status'):</strong><br>
                                            @if( !empty($employee->civil_status_id))
                                            {{ $employee->civilStatus->value }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td style="width: 33%;">
                                            <strong>@lang('rrhh.birthdate'):</strong><br>
                                            @if( !empty($employee->birth_date))
                                            {{ @format_date($employee->birth_date) }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@lang('rrhh.nationality'):</strong><br>
                                            @if( !empty($employee->nationality_id))
                                            {{ $employee->nationality->value }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>@lang('rrhh.personal_email'):</strong><br>
                                            @if( !empty($employee->email))
                                            {{ $employee->email }}
                                            @else
                                            N/A
                                            @endif
                                            <br>
                                            <strong>@lang('rrhh.institutional_email'):</strong><br>
                                            @if( !empty($employee->institutional_email))
                                                {{ $employee->institutional_email }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@lang('rrhh.phone'):</strong><br>
                                            @if( !empty($employee->phone))
                                            {{ $employee->phone }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@lang('rrhh.mobile_phone'):</strong><br>
                                            @if( !empty($employee->mobile))
                                            {{ $employee->mobile }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>@lang('rrhh.country'):</strong><br>
                                            @if( !empty($employee->country_id))
                                            @if( isset($employee->country->name))
                                            {{ $employee->country->name }}
                                            @else
                                            N/A
                                            @endif
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@lang('rrhh.state'):</strong><br>
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
                                        <td>
                                            <strong>@lang('rrhh.city'):</strong><br>
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
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <strong>@lang('rrhh.address'):</strong><br>
                                            @if( !empty($employee->address))
                                            {{ $employee->address }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>@lang('rrhh.profession_occupation'):</strong><br>
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
                                            <strong>@lang('rrhh.isss'):</strong><br>
                                            @if( !empty($employee->social_security_number))
                                            {{ $employee->social_security_number }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@lang('rrhh.afp'):</strong><br>
                                            @if( !empty($employee->afp_id))
                                            @if( isset($employee->afp->value))
                                            {{ $employee->afp->value }} | {{ $employee->afp_number }}
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
                                            <strong>@lang('rrhh.type_employee'):</strong><br>
                                            @if( !empty($employee->type_id))
                                            @if( isset($employee->rrhhTypeWage->name))
                                            {{ $employee->rrhhTypeWage->name }}
                                            @else
                                            N/A
                                            @endif
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@lang('rrhh.date_admission'):</strong><br>
                                            @if( !empty($employee->date_admission))
                                            {{ @format_date($employee->date_admission) }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>@lang('rrhh.way_to_pay'):</strong>
                                            @if( !empty($employee->payment_id))
                                                @if( isset($employee->payment->value))
                                                {{ $employee->payment->value }}
                                                @else
                                                N/A
                                                @endif
                                            @else
                                                N/A
                                            @endif

                                            @if( !empty($employee->bank_id))
                                                <br><strong>@lang('rrhh.bank'):</strong>
                                                @if( isset($employee->bank->name))
                                                    {{ $employee->bank->name }}
                                                @else
                                                    N/A
                                                @endif
                                            @endif
                                            
                                            @if( !empty($employee->bank_account))
                                                <br><strong>@lang('rrhh.bank_account'):</strong>
                                                {{ $employee->bank_account }}
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="box-title text-center"><b>{{ __('rrhh.studies') }}</b></h4>
                            @include('rrhh.studies.table')
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="box-title text-center"><b>{{ __('rrhh.economic_dependencies') }}</b></h4>
                            @include('rrhh.economic_dependences.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="history">
            <div class="boxform_u box-solid_u" style="border-top: 0px solid #d2d6de !important; border-radius: 0px !important;">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="box-title text-center"><b>@lang('rrhh.position_history')</b></h4>
                            <table class="table table-responsive table-bordered table-condensed table-text-center"
                                style="font-size: inherit;" id="documents-table">
                                <thead>
                                    <tr class="active">
                                        <th>@lang('rrhh.effective_date')</th>
                                        <th>@lang('rrhh.department')</th>
                                        <th>@lang('rrhh.position')</th>
                                        <th>@lang('rrhh.description')</th>
                                        <th>@lang('rrhh.status')</th>
                                    </tr>
                                </thead>
                                <tbody id="referencesItems">
                                    @if (count($positions) > 0)
                                    @foreach($positions as $index => $item)
                                    <tr>
                                        <td>
                                            @if ($item->rrhhPersonnelAction != null)
                                            {{ @format_date($item->rrhhPersonnelAction->effective_date) }}
                                            @else
                                            {{ @format_date($employee->date_admission) }}
                                            @endif
                                        </td>
                                        <td>{{ $item->newDepartment->value }}</td>
                                        <td>{{ $item->newPosition1->value }}</td> 
                                        <td>
                                            @if ($item->rrhhPersonnelAction != null)
                                            {{ $item->rrhhPersonnelAction->description }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->current == 1)
                                                <span class="badge" style="background: #367FA9">Vigente</span>
                                            @else
                                            <span class="badge">No vigente</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                            <h4 class="box-title text-center"><b>@lang('rrhh.salary_history')</b></h4>
                            <table class="table table-responsive table-bordered table-condensed table-text-center"
                                style="font-size: inherit;" id="documents-table">
                                <thead>
                                    <tr class="active">
                                        <th>@lang('rrhh.effective_date')</th>
                                        <th>@lang('rrhh.salary')</th>
                                        <th>@lang('rrhh.description')</th>
                                        <th>@lang('rrhh.status')</th>
                                    </tr>
                                </thead>
                                <tbody id="referencesItems">
                                    @if (count($salaries) > 0)
                                    @foreach($salaries as $index => $item)
                                    <tr>
                                        <td>
                                            @if ($item->rrhhPersonnelAction != null)
                                                {{ @format_date($item->rrhhPersonnelAction->effective_date) }}
                                            @else
                                                {{ @format_date($employee->date_admission) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($business->currency_symbol_placement == 'after')
                                                {{ @num_format($item->new_salary) }} {{ $business->currency->symbol }}
                                            @else
                                                {{ $business->currency->symbol }} {{ @num_format($item->new_salary) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->rrhhPersonnelAction != null)
                                                {{ $item->rrhhPersonnelAction->description }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->current == 1)
                                                <span class="badge" style="background: #367FA9">Vigente</span>
                                            @else
                                                <span class="badge">No vigente</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="documents">
            <div class="boxform_u box-solid_u" style="border-top: 0px solid #d2d6de !important; border-radius: 0px !important;">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @include('rrhh.documents.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="contracts">
            <div class="boxform_u box-solid_u" style="border-top: 0px solid #d2d6de !important; border-radius: 0px !important;">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @include('rrhh.contract.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="absence_inability">
            <div class="boxform_u box-solid_u" style="border-top: 0px solid #d2d6de !important; border-radius: 0px !important;">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @include('rrhh.absence_inabilities.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="personnel_action">
            <div class="boxform_u box-solid_u" style="border-top: 0px solid #d2d6de !important; border-radius: 0px !important;">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @include('rrhh.personnel_actions.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div tabindex="-1" class="modal fade" id="file_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"></div>

	<div class="modal fade" id="modal_personnel_action" tabindex="-1">
		<div class="modal-dialog modal-lg" role="document">
		  <div class="modal-content" id="modal_content_personnel_action">
	
		  </div>
		</div>
	</div>

    <div class="modal fade" id="modal_edit_action" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="modal_content_edit_document">
    
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_photo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="modal_content_photo">

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_show" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content" id="modal_content_show">
    
          </div>
        </div>
    </div>
</section>
@endsection