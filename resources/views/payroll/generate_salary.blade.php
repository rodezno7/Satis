<div class="box-body">
    <div class="table-responsive">
        <table class="table table-hover table-condensed table-text-center" style="font-size: inherit; width: 100%"
            id="payroll-detail-table">
            <thead>
                <tr class="active">
                    <th width="5%">@lang('rrhh.code')</th>
                    <th width="11%">@lang('rrhh.employee')</th>
                    <th>@lang('payroll.montly_salary')</th>
                    <th>@lang('payroll.days')</th>
                    {{-- <th>@lang('payroll.hours')</th> --}}
                    <th>@lang('payroll.regular_salary')</th>
                    <th>@lang('payroll.daytime_overtime')</th>
                    <th>@lang('payroll.night_overtime_hours')</th>
                    <th>@lang('payroll.total_hours')</th>
                    <th>@lang('payroll.other_income')</th>
                    <th>@lang('payroll.subtotal')</th>
                    <th>ISSS</th>
                    <th>AFP</th>
                    <th>@lang('payroll.rent')</th>
                    <th>@lang('payroll.other_deductions')</th>
                    <th>@lang('payroll.total_to_pay')</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr class="bg-gray font-14 footer-total text-center">
                    <td colspan="4"><strong>@lang('report.grand_total')</strong></td>
                    <td>
                        <span class="display_currency" id="total_regular_salary" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_daytime_overtime" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_night_overtime_hours" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_overtime" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="other_income" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_subtotal" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_isss" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_afp" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="tota_rent" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_other_deductions" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_total_to_pay" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
