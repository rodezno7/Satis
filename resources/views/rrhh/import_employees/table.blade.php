<div class="row">
    <div class="col-lg-12 table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th width="15%">@lang('rrhh.col_no')</th>
                    <th width="25%">@lang('lang_v1.col_name')</th>
                    <th>@lang('lang_v1.instruction')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>@lang('rrhh.name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.first_name_empl')</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>@lang('rrhh.last_name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.last_name_empl')</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>@lang('rrhh.gender') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>
                        @lang('rrhh.gender_empl')<br>
                        <strong><small>@lang('lang_v1.available_options'): M -> @lang('rrhh.male'), F -> @lang('rrhh.female')</small></strong>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td>@lang('rrhh.nationality') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.nationality_empl') <br><small class="text-muted">(@lang('rrhh.write_catalog_listing'))</small></td>
                </tr>
                <tr>
                    <td class="text-center">5</td>
                    <td>@lang('rrhh.birthdate') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.birthdate')<br> <strong><small>@lang('rrhh.date_format')</small></strong></td>
                </tr>
                <tr>
                    <td class="text-center">6</td>
                    <td>@lang('rrhh.dni') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.dni')<br> <strong><small>@lang('rrhh.dni_format')</small></strong></td>
                </tr>
                <tr>
                    <td class="text-center">7</td>
                    <td>@lang('rrhh.tax_number') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.tax_number') <br><strong><small>@lang('rrhh.general_format')</small></strong></td>
                </tr>
                <tr>
                    <td class="text-center">8</td>
                    <td>@lang('rrhh.marital_status') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.marital_status') <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">9</td>
                    <td>@lang('rrhh.phone') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.phone')<br> <strong><small>@lang('rrhh.general_format')</small></strong></td>
                </tr>
                <tr>
                    <td class="text-center">10</td>
                    <td>@lang('rrhh.mobile_phone') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>
                        @lang('rrhh.mobile_phone') <br> <strong><small>@lang('rrhh.general_format')</small></strong>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">11</td>
                    <td>@lang('rrhh.personal_email') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.personal_email')</td>
                </tr>
                <tr>
                    <td class="text-center">12</td>
                    <td>@lang('rrhh.institutional_email') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>@lang('rrhh.institutional_email')</td>
                </tr>
                <tr>
                    <td class="text-center">13</td>
                    <td>@lang('rrhh.address') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.address')</td>
                </tr>
                <tr>
                    <td class="text-center">14</td>
                    <td>@lang('rrhh.country') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
                    <td>{!! __('rrhh.country_empl') !!} <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">15</td>
                    <td>@lang('rrhh.state') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
                    <td>@lang('rrhh.state_empl') <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">16</td>
                    <td>@lang('rrhh.city') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
                    <td>
                        @lang('rrhh.city_empl') <br>
                        <small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">17</td>
                    <td>@lang('rrhh.social_security_number') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>@lang('rrhh.social_security_number')</td>
                </tr>
                <tr>
                    <td class="text-center">18</td>
                    <td>@lang('rrhh.afp') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>{!! __('rrhh.afp') !!} <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">19</td>
                    <td>@lang('rrhh.afp_number') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>@lang('rrhh.afp_number')</td>
                </tr>
                <tr>
                    <td class="text-center">20</td>
                    <td>@lang('rrhh.date_admission') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.date_admission') <br> <strong><small>@lang('rrhh.date_format')</small></strong> </td>
                </tr>
                <tr>
                    <td class="text-center">21</td>
                    <td>@lang('rrhh.department') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>{!! __('rrhh.department') !!} <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">22</td>
                    <td>@lang('rrhh.position') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.position') <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">23</td>
                    <td>@lang('rrhh.type_employee') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>@lang('rrhh.type_employee') <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">24</td>
                    <td>@lang('rrhh.salary') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>{!! __('rrhh.salary') !!}</td>
                </tr>
                <tr>
                    <td class="text-center">25</td>
                    <td>@lang('rrhh.profession_occupation') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
                    <td>@lang('rrhh.profession_occupation') <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">26</td>
                    <td>@lang('rrhh.way_to_pay') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('rrhh.way_to_pay') <br><small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small></td>
                </tr>
                <tr>
                    <td class="text-center">27</td>
                    <td>@lang('rrhh.bank')<small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>
                        {!! __('rrhh.bank') !!} <br>
                        <small class="text-muted">({!! __('rrhh.write_catalog_listing') !!})</small><br>
                        <strong><small>@lang('rrhh.payment_required')</small></strong>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">28</td>
                    <td>@lang('rrhh.bank_account') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>
                        @lang('rrhh.bank_account')<br>
                        <strong><small>@lang('rrhh.payment_required')</small></strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>