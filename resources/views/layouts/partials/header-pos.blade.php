@inject('request', 'Illuminate\Http\Request')

<div class="col-md-12 no-print pos-header">
  <div class="row">
    @if (config('app.business') == 'optics')
    <div class="col-md-10 col-md-offset-1" style="padding: 0 20px;">
      {{-- Go back --}}
      <a
        href="{{ action('SellController@index') }}"
        title="{{ __('lang_v1.go_back') }}"
        data-toggle="tooltip"
        data-placement="bottom"
        class="btn btn-info btn-flat m-6 btn-xs m-5 pull-right btn-header-pos">
        <strong>
          <i class="fa fa-backward fa-lg"></i>&nbsp;
          @lang('lang_v1.go_back')
        </strong>
      </a>

      {{-- Close register --}}
      <button
        type="button"
        id="close_register"
        title="{{ __('cash_register.close_register') }}"
        data-toggle="tooltip"
        data-placement="bottom"
        class="btn btn-danger btn-flat m-6 btn-xs m-5 pull-right btn-header-pos"
        data-container=".close_register_modal" 
        data-href="{{ action('CashRegisterController@getCloseRegister') }}"
        @if (! empty($is_edit)) disabled @endif>
        <strong>
          <i class="fa fa-window-close fa-lg"></i>&nbsp;
          @lang('cash_register.close_register')
        </strong>
      </button>
    </div>

    @else
    <div class="col-md-10">
      {{-- Go back --}}
      <a
        href="{{ action('SellController@index')}}"
        title="{{ __('lang_v1.go_back') }}"
        data-toggle="tooltip"
        data-placement="bottom"
        class="btn btn-info btn-flat m-6 btn-xs m-5 pull-right">
        <strong>
          <i class="fa fa-backward fa-lg"></i>
        </strong>
      </a>

      {{-- Close register --}}
      <button
        type="button"
        id="close_register"
        title="{{ __('cash_register.close_register') }}"
        data-toggle="tooltip"
        data-placement="bottom"
        class="btn btn-danger btn-flat m-6 btn-xs m-5 pull-right"
        data-container=".close_register_modal" 
        data-href="{{ action('CashierClosureController@getCashierClosure') }}">
        <strong>
          <i class="fa fa-window-close fa-lg"></i>
        </strong>
      </button>

      {{-- Register details --}}
      <button
        type="button"
        id="register_details"
        title="{{ __('cash_register.register_details') }}"
        data-toggle="tooltip"
        data-placement="bottom"
        class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right"
        data-container=".register_details_modal" 
        data-href="{{ action('CashRegisterController@getRegisterDetails') }}">
        <strong>
          <i class="fa fa-briefcase fa-lg" aria-hidden="true"></i>
        </strong>
      </button>

      {{-- Calculator --}}
      <button
        title="@lang('lang_v1.calculator')"
        id="btnCalculator"
        type="button"
        class="btn btn-success btn-flat pull-right m-5 btn-xs mt-10 popover-default"
        data-toggle="popover"
        data-trigger="click"
        data-content='@include("layouts.partials.calculator")'
        data-html="true"
        data-placement="bottom">
        <strong>
          <i class="fa fa-calculator fa-lg" aria-hidden="true"></i>
        </strong>
      </button>

      {{-- Full screen --}}
      <button
        type="button"
        title="{{ __('lang_v1.full_screen') }}"
        data-toggle="tooltip"
        data-placement="bottom"
        class="btn btn-primary btn-flat m-6 hidden-xs btn-xs m-5 pull-right"
        id="full_screen">
        <strong>
          <i class="fa fa-window-maximize fa-lg"></i>
        </strong>
      </button>

      {{-- View suspended sales --}}
      <button
        type="button"
        id="view_suspended_sales"
        title="{{ __('lang_v1.view_suspended_sales') }}"
        data-toggle="tooltip"
        data-placement="bottom"
        class="btn bg-yellow btn-flat m-6 btn-xs m-5 btn-modal pull-right"
        data-container=".view_modal" 
        data-href="{{ action('SellController@index') }}?suspended=1">
        <strong>
          <i class="fa fa-pause-circle-o fa-lg"></i>
        </strong>
      </button>
    </div>

    {{-- Date --}}
    <div class="col-md-2">
      <div class="m-6 pull-right mt-15 hidden-xs">
        <strong>
          {{ @format_date('now') }}
        </strong>
      </div>
    </div>
    @endif
  </div>
</div>
