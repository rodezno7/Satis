<div class="box-body">
    <div class="table-responsive">
        <table class="table table-hover table-responsive table-condensed table-text-center"
            style="font-size: inherit; width: 100%" id="payroll-detail-table">
            <thead>
                <tr class="active">
                    <th width="18%">@lang('rrhh.code')</th>
                    <th width="30%">@lang('rrhh.employee')</th>
                    <th>@lang('payroll.total_calculation')</th>
                    <th>@lang('payroll.rent')</th>
                    <th>@lang('payroll.total_to_pay')</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr class="bg-gray font-14 footer-total text-center" style="font-weight: bold;">
                    <td colspan="2"><strong>@lang('payroll.totals')</strong></td>
                    <td>
                        <span class="display_currency" id="total_regular_salary" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="tota_rent" data-currency_symbol="true"></span>
                    </td>
                    <td>
                        <span class="display_currency" id="total_total_to_pay" data-currency_symbol="true"></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>