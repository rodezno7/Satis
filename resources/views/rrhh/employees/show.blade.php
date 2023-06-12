@extends('layouts.app')
@section('title', __('rrhh.rrhh'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="box-title">@lang( 'rrhh.employee_information' )</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-header">
            <h3 class="box-title">@lang('rrhh.employee'): {{ $employee->code }}</h3>
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
                                    {{ $employee->code }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.names'):</strong>
                                    {{ $employee->name }} {{ $employee->last_name }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 33%;">
                                    <strong>@lang('rrhh.gender'):</strong>
                                    @if ($employee->gender == 'M')
                                    @lang('rrhh.male')
                                    @else
                                    @lang('rrhh.female')
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
                                    {{ $employee->civilStatus->value }}
                                </td>
                                <td style="width: 33%;">
                                    <strong>@lang('rrhh.birthdate'):</strong>
                                    {{ @format_date($employee->birthdate) }}
                                </td>
                                <td>
                                    <strong>@lang('rrhh.nationality'):</strong>
                                    {{ $employee->nationality->value }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 34%;">
                                    <strong>@lang('rrhh.status'):</strong>
                                    @if($employee->status == 1)
                                    @lang('rrhh.active')
                                    @else
                                    @lang('rrhh.inactive')
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.phone'):</strong>
                                    {{ $employee->phone }}
                                </td>
                                <td>
                                    <strong>@lang('rrhh.mobile_phone'):</strong>
                                    {{ $employee->mobile }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <strong>@lang('rrhh.email'):</strong>
                                    {{ $employee->email }}
                                </td>
                                <td>
                                    <strong>@lang('rrhh.check_payment'):</strong>
                                    @if($employee->check_payment == 1)
                                    @lang('rrhh.yes')
                                    @else
                                    @lang('rrhh.no')
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.extra_hours'):</strong>
                                    @if($employee->extra_hours == 1)
                                    @lang('rrhh.yes')
                                    @else
                                    @lang('rrhh.no')
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.foreign_tax'):</strong>
                                    @if($employee->foreign_tax == 1)
                                    @lang('rrhh.yes')
                                    @else
                                    @lang('rrhh.no')
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.state'):</strong>
                                    @if( isset($employee->state->name))
                                    {{ $employee->state->name }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.city'):</strong>
                                    @if( isset($employee->city->name))
                                    {{ $employee->city->name }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td colspan="2">
                                    <strong>@lang('rrhh.address'):</strong>
                                    {{ $employee->address }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.profession_occupation'):</strong>
                                    @if( isset($employee->profession->value))
                                    {{ $employee->profession->value }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.date_admission'):</strong>
                                    {{ @format_date($employee->date_admission) }}
                                </td>
                                <td>
                                    <strong>@lang('rrhh.salary'):</strong>
                                    <span class="display_currency" data-currency_symbol="true">{{ $employee->salary }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.department'):</strong>
                                    @if( isset($employee->department->value))
                                    {{ $employee->department->value }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.position'):</strong>
                                    @if( isset($employee->position->value))
                                    {{ $employee->position->value }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.afp'):</strong>
                                    @if( isset($employee->afp->value))
                                    {{ $employee->afp->value }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@lang('rrhh.type'):</strong>
                                    @if( isset($employee->type->value))
                                    {{ $employee->type->value }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.bank'):</strong>
                                    @if( isset($employee->bank->name))
                                    {{ $employee->bank->name }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>@lang('rrhh.bank_account'):</strong>
                                    {{ $employee->bank_account }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection