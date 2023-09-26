<div class="box-body">
    <div class="table-responsive">
        <table class="table table-hover table-condensed table-text-center" style="font-size: inherit; width: 100%"
            id="payroll-detail-table">
            <thead>
                <tr class="active">
                    <th width="7%">@lang('rrhh.code')</th>
                    <th width="15%">@lang('rrhh.employee')</th>
                    <th width="11%">@lang('rrhh.date_admission')</th>
                    <th width="11%">@lang('rrhh.end_date')</th>
                    <th>@lang('payroll.montly_salary')</th>
                    <th>@lang('payroll.days') <br> <small>A considerar para el calculo</small> </th>
                    <th>@lang('payroll.bonus')</th>
                    <th>@lang('payroll.rent')</th>
                    <th>@lang('payroll.total_to_pay')</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr class="bg-gray font-14 footer-total text-center">
                    <td colspan="4"><strong>@lang('payroll.totals')</strong></td>
                    <td>
                        <span class="display_currency" id="total_montly_salary" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                    <td></td>
                    <td>
                        <span class="display_currency" id="total_bonus" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="tota_rent" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_total_to_pay" data-currency_symbol="true" style="font-weight: bold;"></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
