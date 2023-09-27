<div class="box-body">
    <div class="table-responsive">
        <table class="table table-hover table-condensed table-text-center" style="font-size: inherit; width: 100%"
            id="payroll-detail-table">
            <thead>
                <tr class="active">
                    <th width="11%">@lang('rrhh.code')</th>
                    <th width="20%">@lang('rrhh.employee')</th>
                    <th>@lang('payroll.montly_salary')</th>
                    <th width="12%">@lang('rrhh.start_date')</th>
                    <th width="11%">@lang('rrhh.end_date')</th>
                    <th width="10%">@lang('payroll.vacation')</th>
                    <th>@lang('payroll.biweekly_salary')</th>
                    <th>@lang('payroll.vacation_bonus')</th>
                    <th>@lang('payroll.total_to_pay')</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr class="bg-gray font-14 footer-total text-center">
                <td colspan="6"><strong>@lang('payroll.totals')</strong></td>
                    <td>
                        <span class="display_currency" id="total_regular_salary" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_vacation_bonus" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_to_pay" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
