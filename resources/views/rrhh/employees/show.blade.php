
<div class="table-responsive">
    <font size="2">
        <table class="table table-striped table-bordered table-condensed table-hover" width="100%">

            <tr>

                <td rowspan="4" style="align-content: center;">
                    <img src="{{ asset($route) }}" width="125px" height="150px">
                </td>

            </tr>
            <tr>
                <td colspan="2">

                    <strong>@lang('rrhh.code')</strong><br>
                    {{ $employee->code }}


                </td>
                
            </tr>

            <tr>
                
                <td colspan="2">

                    <strong>@lang('rrhh.names'):</strong><br>
                    {{ $employee->name }}


                </td>
               
            </tr>

            <tr>
              
                <td colspan="2">

                    <strong>@lang('rrhh.last_name'):</strong><br>
                    {{ $employee->last_name }}



                </td>
            </tr>

            <tr>
                <td style="width: 33%;">

                    <strong>@lang('rrhh.gender'):</strong><br>

                    @if ($employee->gender == 'M')
                    @lang('rrhh.male')
                    @else
                    @lang('rrhh.female')
                    @endif

                </td>
                <td style="width: 34%;">

                    <strong>@lang('rrhh.status'):</strong><br>
                    
                    @if($employee->status == 1)

                    @lang('rrhh.active')

                    @else

                    @lang('rrhh.inactive')

                    @endif
                    


                </td>
                <td style="width: 33%;">

                    <strong>@lang('rrhh.birthdate'):</strong><br>
                    

                    {{ @format_date($employee->birthdate) }}


                </td>
            </tr>

            <tr>
                <td>

                    <strong>@lang('rrhh.nationality'):</strong><br>
                    
                    {{ $employee->nationality->value }}
                    


                </td>
                <td>

                    <strong>@lang('rrhh.marital_status'):</strong><br>
                    
                    {{ $employee->civilStatus->value }}
                    


                </td>
                <td>

                    <strong>@lang('rrhh.phone'):</strong><br>
                    {{ $employee->phone }}


                </td>
            </tr>

            <tr>
                <td>

                    <strong>Whatsapp:</strong><br>
                    {{ $employee->mobile }}


                </td>
                <td>

                    <strong>@lang('rrhh.email'):</strong><br>
                    {{ $employee->email }}


                </td>
                <td>

                    <strong>@lang('rrhh.check_payment'):</strong><br>
                    
                    @if($employee->check_payment == 1)

                    @lang('rrhh.yes')

                    @else

                    @lang('rrhh.no')

                    @endif
                    


                </td>
            </tr>

            <tr>
                <td>

                    <strong>@lang('rrhh.extra_hours'):</strong><br>
                    
                    @if($employee->extra_hours == 1)

                    @lang('rrhh.yes')

                    @else

                    @lang('rrhh.no')

                    @endif
                    


                </td>
                <td>

                    <strong>@lang('rrhh.foreign_tax'):</strong><br>
                    
                    @if($employee->foreign_tax == 1)

                    @lang('rrhh.yes')

                    @else

                    @lang('rrhh.no')

                    @endif
                    


                </td>
                <td>

                    <strong>@lang('rrhh.state'):</strong><br>

                    @if( isset($employee->state->name))
                    
                    {{ $employee->state->name }}
                    
                    @else 

                    N/A

                    @endif

                </td>
            </tr>

            <tr>
                <td>

                    <strong>@lang('rrhh.city'):</strong><br>

                    @if( isset($employee->city->name))
                    
                    {{ $employee->city->name }}
                    
                    @else 

                    N/A

                    @endif

                    


                </td>
                <td colspan="2">

                    <strong>@lang('rrhh.address'):</strong><br>
                    {{ $employee->address }}


                </td>
            </tr>

            <tr>
                <td>

                    <strong>@lang('rrhh.profession_occupation'):</strong><br>

                    @if( isset($employee->profession->value))
                    
                    {{ $employee->profession->value }}
                    
                    @else 

                    N/A

                    @endif
                    

                    


                </td>
                <td>

                    <strong>@lang('rrhh.date_admission'):</strong><br>
                    
                    {{ @format_date($employee->date_admission) }}


                </td>
                <td>

                    <strong>@lang('rrhh.salary'):</strong><br>
                    
                    
                    <span class="display_currency" data-currency_symbol="true">{{ $employee->salary }}</span>


                </td>
            </tr>

            <tr>
                <td>

                    <strong>@lang('rrhh.department'):</strong><br>
                    @if( isset($employee->department->value))
                    
                    {{ $employee->department->value }}
                    
                    @else 

                    N/A

                    @endif



                </td>
                <td>

                    <strong>@lang('rrhh.position'):</strong><br>

                    @if( isset($employee->position->value))
                    
                    {{ $employee->position->value }}
                    
                    @else 

                    N/A

                    @endif
                    


                </td>
                <td>

                    <strong>@lang('rrhh.afp'):</strong><br>

                    @if( isset($employee->afp->value))
                    
                    {{ $employee->afp->value }}
                    
                    @else 

                    N/A

                    @endif
                    


                </td>
            </tr>

            <tr>
                <td>

                    <strong>@lang('rrhh.type'):</strong><br>

                    @if( isset($employee->type->value))
                    
                    {{ $employee->type->value }}
                    
                    @else 

                    N/A

                    @endif
                    


                </td>
                <td>

                    <strong>@lang('rrhh.bank'):</strong><br>
                    @if( isset($employee->bank->name))
                    
                    {{ $employee->bank->name }}
                    
                    @else 

                    N/A

                    @endif
                    


                </td>
                <td>

                    <strong>@lang('rrhh.bank_account'):</strong><br>
                    {{ $employee->bank_account }}


                </td>
            </tr>
        </table>
    </font>
</div>
