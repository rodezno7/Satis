@inject('request', 'Illuminate\Http\Request')

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
      <!-- Call superadmin module if defined -->
      @if(Module::has('Superadmin'))
      @include('superadmin::layouts.partials.sidebar')
      @endif

      <!-- call Essentials module if defined -->
      @if(Module::has('Essentials'))
      @include('essentials::layouts.partials.sidebar')
      @endif
      <!-- <li class="header">HEADER</li> -->
      <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
        <a href="{{action('HomeController@index')}}">
          <i class="fa fa-dashboard"></i> <span>
            @lang('home.home')</span>
        </a>
      </li>

      {{-- modulo de usuarios --}}
      @if (
      auth()->user()->can('user.view') ||
      auth()->user()->can('user.create') ||
      auth()->user()->can('roles.view') ||
      auth()->user()->can('binnacle.view')
      )
      <li class="treeview {{ in_array($request->segment(1), [
          'roles',
          'modules',
          'employees',
          'positions',
          'users',
          'sales-commission-agents',
          'binnacle'
        ]) ? 'active active-sub' : '' }}">
        <a href="#">
          <i class="fa fa-users"></i>
          <span class="title">@lang('user.user_management')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @can( 'user.view' )
          <li class="{{ $request->segment(1) == 'users' ? 'active active-sub' : '' }}">
            <a href="{{action('ManageUserController@index')}}">
              <i class="fa fa-user"></i>
              <span class="title">
                @lang('user.users')
              </span>
            </a>
          </li>
          @endcan
          {{-- @can( 'employees.view' )
          <li class="{{ $request->segment(1) == 'employees' ? 'active active-sub' : '' }}">
            <a href="{{action('ManageEmployeesController@index')}}">
              <i class="fa fa-user"></i>
              <span class="title">
                @lang('employees.employees')
              </span>
            </a>
          </li>
          @endcan --}}
          {{-- @can('positions.view')
          <li class="{{ $request->segment(1) == 'positions' ? 'active active-sub' : '' }}">
            <a href="{{action('ManagePositionsController@index')}}">
              <i class="fa fa-briefcase"></i>
              <span class="title">
                @lang('positions.positions')
              </span>
            </a>
          </li>
          @endcan --}}
          @can('roles.view')
          <li class="{{ $request->segment(1) == 'roles' ? 'active active-sub' : '' }}">
            <a href="{{action('RoleController@index')}}">
              <i class="fa fa-briefcase"></i>
              <span class="title">
                @lang('user.roles')
              </span>
            </a>
          </li>

          <li class="{{ $request->segment(1) == 'modules' ? 'active active-sub' : '' }}">
            <a href="{{action('ModuleController@index')}}">
              <i class="fa fa-briefcase"></i>
              <span class="title">
                @lang('user.modules_permissions')
              </span>
            </a>
          </li>
          @endcan

          {{-- Binnacle --}}
          @can('binnacle.view')
          <li class="{{ $request->segment(1) == 'binnacle' ? 'active active-sub' : '' }}">
            <a href="{{ action('BinnacleController@index') }}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('binnacle.binnacle')
              </span>
            </a>
          </li>
          @endcan
        </ul>
      </li>
      @endif
      {{-- fin modulo de usuarios --}}

      
      @if(in_array('module_rrhh', $enabled_modules))
        {{-- Inicio Recurso humano --}}
        @if(auth()->user()->can('rrhh_employees.view') || auth()->user()->can('rrhh_import_employees.create') || auth()->user()->can('rrhh_catalogues.view') || auth()->user()->can('rrhh_personnel_action.authorize') || auth()->user()->can('rrhh_assistance.view') || auth()->user()->can('rrhh_setting.access'))
        <li
          class="treeview {{ in_array($request->segment(1), ['rrhh-employees', 'rrhh-import-employees', 'rrhh-contracts-masive', 'rrhh-personnel-action-masive', 'rrhh-assistances', 'rrhh-catalogues', 'rrhh-setting']) ? 'active active-sub' : '' }}"
          id="tour_step4">
          <a href="#" id="tour_step4_menu"><i class="fa fa-address-book" aria-hidden="true"></i><span>RRHH</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" id="rrhh_over">
            @can('rrhh_employees.view')
            <li class="treeview {{ in_array($request->segment(1), ['rrhh-employees', 'rrhh-import-employees', 'rrhh-personnel-action-masive', 'rrhh-contracts-masive']) ? 'active active-sub' : '' }}">
              <a href="#">
                <i class="fa fa-user"></i>
                <span class="title">
                  @lang('rrhh.employee')
                </span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="{{ $request->segment(1) == 'rrhh-employees' ? 'active' : '' }}">
                  <a href="{{ action('EmployeesController@index') }}">
                    <i class="fa fa-newspaper-o"></i>
                    <span class="title">
                      @lang('rrhh.general_payroll')
                    </span>
                  </a>
                </li>
                <li class="{{ $request->segment(1) == 'rrhh-import-employees' ? 'active' : '' }}">
                  <a href="{{ action('RrhhImportEmployeesController@create') }}">
                    <i class="fa fa-download"></i>
                    <span class="title">
                      @lang('rrhh.import_employees')
                    </span>
                  </a>
                </li>
                {{-- <li class="{{ $request->segment(1) == 'rrhh-import-employees' ? 'active' : '' }}">
                  <a href="{{ action('RrhhImportEmployeesController@edit') }}">
                    <i class="fa fa-download"></i>
                    <span class="title">
                      @lang('rrhh.edit_employees')
                    </span>
                  </a>
                </li> --}}
                <li class="{{ $request->segment(1) == 'rrhh-personnel-action-masive' ? 'active' : '' }}">
                  <a href="{{ action('RrhhPersonnelActionController@createMasive') }}">
                    <i class="fa fa-drivers-license"></i>
                    <span class="title">
                      @lang('rrhh.personnel_actions')
                    </span>
                  </a>
                </li>
                <li class="{{ $request->segment(1) == 'rrhh-contracts-masive' ? 'active' : '' }}">
                  <a href="{{ action('RrhhContractController@createMassive') }}">
                    <i class="fa fa-file-text"></i>
                    <span class="title">
                     @lang('rrhh.massive_contract')
                    </span>
                  </a>
                </li>
              </ul>
            </li>
            @endcan
            @can('rrhh_personnel_action.authorize')
            <li class="{{ $request->segment(1) == 'rrhh-personnel-action' ? 'active' : '' }}">
              <a href="{{action('RrhhPersonnelActionController@index')}}" id="tour_step2"><i class="fa fa-check"></i>
                @lang('rrhh.authorizations')
              </a>
            </li>
            @endcan
            @can('rrhh_assistance.view')
            <li class="{{ $request->segment(1) == 'rrhh-assistances' ? 'active' : '' }}">
              <a href="{{action('AssistanceEmployeeController@index')}}" id="tour_step2"><i class="fa fa-list"></i>
                @lang('rrhh.assistance')
              </a>
            </li>
            @endcan
            @can('rrhh_catalogues.view')
            <li class="{{ $request->segment(1) == 'rrhh-catalogues' ? 'active' : '' }}">
              <a href="{{action('RrhhHeaderController@index')}}" id="tour_step2"><i class="fa fa-table"></i>
                @lang('rrhh.catalogues')
              </a>
            </li>
            @endcan
            @can('rrhh_setting.access')
            <li class="{{ $request->segment(1) == 'rrhh-setting' ? 'active' : '' }}">
              <a href="{{action('SettingController@index')}}" id="tour_step2"><i class="fa fa-cogs"></i>
                @lang('rrhh.settings')
              </a>
            </li>
            @endcan
          </ul>
        </li>
        @endif
        {{-- Fin Recurso humano --}}

        {{-- Inicio Payroll --}}
        @if(auth()->user()->can('payroll.view') || auth()->user()->can('payroll-catalogues.view'))
          <li
            class="treeview {{ in_array($request->segment(1), ['payroll', 'institution-law', 'law-discount', 'bonus-calculation']) ? 'active active-sub' : '' }}"
            id="tour_step4">
            <a href="#" id="tour_step4_menu"><i class="fa fa-list" aria-hidden="true"></i><span>{{ __('payroll.payroll') }}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu" id="rrhh_over">
              @can('payroll.view')
              <li class="{{ $request->segment(1) == 'payroll' ? 'active' : '' }}">
                <a href="{{action('PayrollController@index')}}" id="tour_step2"><i class="fa fa-list"></i>
                  @lang('payroll.payroll')
                </a>
              </li>
              @endcan
              @can('payroll-catalogues.view')
              <li class="treeview {{ in_array($request->segment(1), ['institution-law', 'law-discount', 'bonus-calculation']) ? 'active active-sub' : '' }}">
                <a href="#">
                  <i class="fa fa-table"></i>
                  <span class="title">
                    @lang('payroll.catalogues')
                  </span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                  <li class="{{ $request->segment(1) == 'institution-law' ? 'active' : '' }}">
                    <a href="{{ action('InstitutionLawController@index') }}">
                      <i class="fa fa-newspaper-o"></i>
                      <span class="title">
                        @lang('payroll.institution_laws')
                      </span>
                    </a>
                  </li>
                  <li class="{{ $request->segment(1) == 'law-discount' ? 'active' : '' }}">
                    <a href="{{ action('LawDiscountController@index') }}">
                      <i class="fa fa-newspaper-o"></i>
                      <span class="title">
                        @lang('payroll.discounts_table')
                      </span>
                    </a>
                  </li>
                  <li class="{{ $request->segment(1) == 'bonus-calculation' ? 'active' : '' }}">
                    <a href="{{ action('BonusCalculationController@index') }}">
                      <i class="fa fa-newspaper-o"></i>
                      <span class="title">
                        @lang('payroll.bonus_table')
                      </span>
                    </a>
                  </li>
                </ul>
              </li>
              @endcan
            </ul>
          </li>
        @endif
        {{-- Fin Payroll --}}
      @endif
      

      <!-- Accounting Menu -->
      @if (auth()->user()->can('catalogue')
      || auth()->user()->can('entries')
      || auth()->user()->can('banks')
      || auth()->user()->can('auxiliars')
      || auth()->user()->can('ledgers')
      || auth()->user()->can('balances')
      || auth()->user()->can('iva_book.access')
      || auth()->user()->can('iva_book.book_final_consumer')
      || auth()->user()->can('iva_book.book_taxpayer')
      || auth()->user()->can('iva_book.purchases_book')
      || auth()->user()->can('treasury_annexes.view')
      || auth()->user()->can('retentions.view')
      || auth()->user()->can('retentions.create')
      )
      <li class="treeview {{ in_array($request->segment(1), [
        'catalogue',
        'entries',
        'auxiliars',
        'ledgers',
        'journal-book',
        'balances',
        'banks',
        'result',
        'book-final-consumer',
        'book-taxpayer',
        'purchases-book',
        'fixed-assets',
        'fixed-asset-types',
        'treasury-annexes',
        'retentions'
        ]) ? 'active active-sub' : '' }}">
        <a href="#">
          <i class="fa fa-cubes"></i>
          <span class="title">@lang('accounting.accounting_menu')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @can('catalogue')
          <li class="{{ $request->segment(1) == 'catalogue' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/catalogue')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.catalogue_menu')
              </span>
            </a>
          </li>
          @endcan
          @can('entries')
          <li class="{{ $request->segment(1) == 'entries' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/entries')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.entries_menu')
              </span>
            </a>
          </li>
          @endcan
          @can('banks')
          <li class="{{ $request->segment(1) == 'banks' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/banks')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.banks_menu')
              </span>
            </a>
          </li>
          @endcan
          @can('auxiliars')
          <li class="{{ $request->segment(1) == 'auxiliars' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/auxiliars')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.auxiliars_menu')
              </span>
            </a>
          </li>
          @endcan
          @can('ledgers')
          <li class="{{ $request->segment(1) == 'ledgers' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/ledgers')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.general_ledge')
              </span>
            </a>
          </li>
          <li class="{{ $request->segment(1) == 'journal-book' ? 'active active-sub' : '' }}">
            <a href="{{ action('ReporterController@getGralJournalBook') }}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.general_journal_book')
              </span>
            </a>
          </li>
          @endcan
          @can('balances')
          <li class="{{ $request->segment(1) == 'balances' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/balances')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.balances_er_menu')
              </span>
            </a>
          </li>

          @endcan

          {{-- @can('balances')
          <li class="{{ $request->segment(1) == 'iva_books' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/iva_books')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('accounting.iva_books')
              </span>
            </a>
          </li>

          @endcan --}}

          {{-- IVA Books --}}
          @can('iva_book.access')
          <li class="treeview @if (in_array($request->segment(1), [
      'book-final-consumer',
      'book-taxpayer',
      'purchases-book'
      ])) {{'active active-sub'}} @endif">
            <a href="#">
              <i class="fa fa-book"></i> <span>@lang('accounting.iva_books')</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>

            <ul class="treeview-menu">
              @can('iva_book.book_final_consumer')
              <li class="{{ $request->segment(1) == 'book-final-consumer' ? 'active' : '' }}">
                <a href="{{ action('ReporterController@viewBookFinalConsumer') }}">
                  <i class="fa fa-table"></i> @lang('accounting.consumer_sales')
                </a>
              </li>
              @endcan

              @can('iva_book.book_taxpayer')
              <li class="{{ $request->segment(1) == 'book-taxpayer' ? 'active' : '' }}">
                <a href="{{ action('ReporterController@viewBookTaxpayer') }}">
                  <i class="fa fa-table"></i> @lang('accounting.taxpayer_sales')
                </a>
              </li>
              @endcan

              @can('iva_book.purchases_book')
              <li class="{{ $request->segment(1) == 'purchases-book' ? 'active' : '' }}">
                <a href="{{ action('ReporterController@viewPurchasesBook') }}">
                  <i class="fa fa-table"></i> @lang('accounting.purchases_book')
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endcan

          @can('treasury_annexes.view')
          <li class="{{ $request->segment(1) == 'treasury-annexes' ? 'active active-sub' : '' }}">
            <a href="{{ action('ReporterController@getTreasuryAnnexes') }}">
              <i class="fa fa-file-text-o"></i>
              <span class="title">
                @lang('report.annexes')
              </span>
            </a>
          </li>
          @endcan

          @can('retentions.view')
          <li class="{{ $request->segment(1) == 'retentions' ? 'active active-sub' : '' }}">
            <a href="{{ action('RetentionController@index') }}">
              <i class="fa fa-list"></i>
              <span class="title">
                @lang('retention.retention_notes')
              </span>
            </a>
          </li>
          @endcan

          {{-- @if (auth()->user()->can('fixed_asset.view') || auth()->user()->can('fixed_asset_type.view'))
          <li
            class="treeview @if( in_array($request->segment(1), ['fixed-assets', 'fixed-asset-types']) ) {{'active active-sub'}} @endif">
            <a href="#">
              <i class="fa fa-cubes"></i> <span>@lang('fixed_asset.fixed_assets')</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @can('fixed_asset.view')
              <li class="{{ $request->segment(1) == 'fixed-assets' ? 'active' : '' }}">
                <a href="{{ action('FixedAssetController@index') }}">
                  <i class="fa fa-cube"></i> @lang('fixed_asset.fixed_assets')
                </a>
              </li>
              @endcan
              @can('fixed_asset_type.view')
              <li class="{{ $request->segment(1) == 'fixed-asset-types' ? 'active' : '' }}">
                <a href="{{ action('FixedAssetTypeController@index') }}">
                  <i class="fa fa-cube"></i> @lang('fixed_asset.fixed_asset_types')
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endif --}}
        </ul>
      </li>
      @endif
      <!-- /Accounting Menu -->



      <!-- RRHH Menu -->
      {{--
      @if (auth()->user()->can('rrhh_overall_payroll.view') || auth()->user()->can('rrhh_catalogues.view'))
      <li
        class="treeview {{ in_array($request->segment(1), ['rrhh-employees', 'rrhh-catalogues']) ? 'active active-sub' : '' }}">
        <a href="#">
          <i class="fa fa-users"></i>
          <span class="title">@lang('rrhh.rrhh')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">

          @can('rrhh_overall_payroll.view')
          <li class="{{ $request->segment(1) == 'rrhh-employees' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/rrhh-employees')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('rrhh.overall_payroll')
              </span>
            </a>
          </li>
          @endcan

          @can('rrhh_catalogues.view')
          <li class="{{ $request->segment(1) == 'rrhh-catalogues' ? 'active active-sub' : '' }}">
            <a href="{!!URL::to('/rrhh-catalogues')!!}">
              <i class="fa fa-table"></i>
              <span class="title">
                @lang('rrhh.catalogues')
              </span>
            </a>
          </li>
          @endcan

        </ul>
      </li>
      @endif --}}
      <!-- /RRHH Menu -->

      @if (
      auth()->user()->can('crm.view') ||
      auth()->user()->can('customer.view') ||
      auth()->user()->can('claim.access') ||
      auth()->user()->can('quotes.access') ||
      auth()->user()->can('order.view') ||
      auth()->user()->can('credit.access') ||
      auth()->user()->can('customer.create') ||
      auth()->user()->can('oportunities.access') ||
      auth()->user()->can('crm_settings.view') ||
      auth()->user()->can('crm-contactmode.view') ||
      auth()->user()->can('crm-contactreason.view')
      )
      <li class="treeview {{ in_array($request->segment(1), [
    'crm',
    'claims',
    'quotes',
    'orders',
    'quote', 
    'oportunities',
    'customers',
    'crm-settings',
    'crm-contactmode',
    'crm-contactreason',
    'manage-credit-requests',
    'customers-import',
    'import-customer-vehicles'
    ]) ? 'active active-sub' : '' }}">
        <a href="#">
          <i class="fa fa-handshake-o"></i>
          <span class="title">@lang('crm.crm')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @can('crm.dashboard')
          <li class="{{ $request->segment(1) == 'crm' ? 'active' : '' }}">
            <a href="{{action('CRMController@index')}}">
              <i class="fa fa-dashboard"></i> <span>
                @lang('crm.dashboard')</span>
            </a>
          </li>
          @endcan
          {{-- Aca iran los clientes --}}
          @can('customer.view')
          <li class="{{ $request->segment(1) == 'customers' ? 'active' : '' }}"><a
              href="{{action('CustomerController@index')}}"><i class="fa fa-address-book" aria-hidden="true"></i>
              @lang('customer.customers')</a></li>
          @endcan

          @can('claim.access')
          <li class="{{ $request->segment(1) == 'claims' ? 'active' : '' }}">
            <a href="{{action('ClaimController@index')}}"><i class="fa fa-comments-o"></i>
              <span>@lang('crm.claims')</span></a>
          </li>
          @endcan
          @can('quotes.access')
          <li class="{{ $request->segment(1) == 'quotes' ? 'active active-sub' : '' }}">
            <a href="{{action('QuoteController@index')}}">
              <i class="fa fa-pencil-square"></i>
              <span class="title">
                @lang('quote.quotes')
              </span>
            </a>
          </li>
          @endcan
          @can('order.view')
          <li class="treeview @if( in_array($request->segment(1), ['orders']) ) {{'active active-sub'}} @endif">
            {{-- <a href="#" id="ss"><i class="fa fa-book"></i> <span>@lang('order.orders')</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a> --}}
            {{-- <ul class="treeview-menu" id="aas"> --}}
              <li class="{{ $request->segment(1) == 'orders' ? 'active' : '' }}">
                <a href="{{action('OrderController@index')}}" id="tour_step2"><i class="fa fa-pencil-square"></i>
                  @lang('order.orders')
                </a>
              </li>
              {{--
            </ul> --}}
          </li>
          @endcan
          @can('credit.access')
          <li class="{{ $request->segment(1) == 'manage-credit-requests' ? 'active active-sub' : '' }}">
            <a href="{{action('ManageCreditRequestController@index')}}">
              <i class="fa fa-pencil-square"></i>
              <span class="title">
                @lang('crm.credit_requests')
              </span>
            </a>
          </li>
          @endcan

          {{-- Import customers --}}
          @can('customer.create')
          <li class="{{ $request->segment(1) == 'customers-import' ? 'active' : '' }}"><a
              href="{{ action('CustomerController@getImportCustomers') }}"><i class="fa fa-download"></i>
              @lang('customer.import_customers')</a></li>
          @endcan

          {{-- Import customer vehicles --}}
          @if (config('app.business') == 'workshop')
          @can('customer.create')
          <li class="{{ $request->segment(1) == 'import-customer-vehicles' ? 'active' : '' }}">
            <a href="{{ action('CustomerVehicleController@getImporter') }}">
              <i class="fa fa-download"></i>
              @lang('customer.import_vehicles')
            </a>
          </li>
          @endcan
          @endif

          @can('oportunities.access')
          <li class="{{ in_array($request->segment(1), ['oportunities']) ? 'active active-sub' : '' }}">
            <a href="#">
              <i class="fa fa-address-book"></i>
              <span class="title">
                @lang('crm.oportunities')
              </span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="{{ $request->input('type') == 'all_oportunities' ? 'active' : '' }}">
                <a href="{{ action('OportunityController@index', ['type' => 'all_oportunities']) }}">
                  <i class="fa fa-users"></i>
                  <span class="title">
                    @lang('crm.oportunities')
                  </span>
                </a>
              </li>
              <li class="{{ $request->input('type') == 'my_oportunities' ? 'active' : '' }}">
                <a href="{{ action('OportunityController@index', ['type' => 'my_oportunities']) }}">
                  <i class="fa fa-user"></i>
                  <span class="title">
                    @lang('crm.my_oportunities')
                  </span>
                </a>
              </li>
            </ul>
          </li>
          @endcan

          @can('crm_settings.view')
          <li
            class="{{ in_array($request->segment(1), ['crm-settings', 'crm-contactmode', 'crm-contactreason', 'quote', 'reason']) ? 'active active-sub' : '' }}">
            <a href="#">
              <i class="fa fa-cogs"></i>
              <span class="title">
                @lang('crm.settings')
              </span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @can('crm-contactmode.view')
              <li class="{{ $request->segment(1) == 'crm-contactmode' ? 'active active-sub' : '' }}">
                <a href="{{action('CRMContactModeController@index')}}">
                  <i class="fa fa fa-code-fork"></i>
                  <span class="title">
                    @lang('crm.contact_mode')
                  </span>
                </a>
              </li>
              @endcan
              @can('crm-contactreason.view')
              <li class="{{ $request->segment(1) == 'crm-contactreason' ? 'active active-sub' : '' }}">
                <a href="{{action('CRMContactReasonController@index')}}">
                  <i class="fa fa fa-compress"></i>
                  <span class="title">
                    @lang('crm.contact_reason')
                  </span>
                </a>
              </li>
              @endcan
              @can('crm-contactreason.view')
              <li
                class="{{ $request->segment(1) == 'quote' && $request->segment(2) == 'reason' ? 'active active-sub' : '' }}">
                <a href="{{action('ReasonController@index')}}">
                  <i class="fa fa fa-compress"></i>
                  <span class="title">
                    @lang('Motivos de ventas perdidas')
                  </span>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endcan
        </ul>
      </li>
      @endif

      {{-- Laboratory --}}
      @if (config('app.business') == 'optics')
      @if (
      auth()->user()->can('patients.access') ||
      auth()->user()->can('lab_order.access') ||
      auth()->user()->can('lab_order.view') ||
      auth()->user()->can('sell.view') ||
      auth()->user()->can('graduation_card.view') ||
      auth()->user()->can('external_lab.view') ||
      auth()->user()->can('status_lab_order.view') ||
      auth()->user()->can('errors_report.view') ||
      auth()->user()->can('external_labs_report.view') ||
      auth()->user()->can('transfer_sheet.view')
      )
      <li class="treeview {{ in_array($request->segment(1), [
  'patients',
  'lab-orders',
  'lab-orders-by-location',
  'graduation-cards',
  'external-labs',
  'status-lab-orders',
  'lab-order-reports'
  ]) ? 'active active-sub' : '' }}">
        <a href="#">
          <i class="fa fa-eye"></i>
          <span class="title">@lang('lab_order.laboratory')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          {{-- Patients --}}
          @can('patients.access')
          <li class="{{ $request->segment(1) == 'patients' ? 'active' : '' }}">
            <a href="{{ action('Optics\PatientController@index') }}">
              <i class="fa fa-user-md"></i>
              <span>@lang('customer.patients')</span>
            </a>
          </li>
          @endcan

          {{-- Lab orders --}}
          @if (auth()->user()->can('lab_order.update') || auth()->user()->can('sell.view'))
          <li
            class="{{ $request->segment(1) == 'lab-orders' && empty($request->input('opc')) ? 'active active-sub' : '' }}">
            <a href="{{ action('Optics\LabOrderController@index') }}">
              <i class="fa fa-pencil-square"></i>
              <span class="title">
                @if (auth()->user()->can('lab_order.update'))
                @lang('lab_order.lab_orders')
                @else
                @lang('lab_order.orders_shipped')
                @endif
              </span>
            </a>
          </li>
          @endif

          {{-- Graduation cards --}}
          @can('graduation_card.view')
          <li class="{{ $request->segment(1) == 'graduation-cards' ? 'active active-sub' : '' }}">
            <a href="{{ action('Optics\GraduationCardController@index') }}">
              <i class="fa fa-eye"></i>
              <span class="title">
                @lang('graduation_card.graduation_cards')
              </span>
            </a>
          </li>
          @endcan

          {{-- External labs --}}
          @can('external_lab.view')
          <li class="{{ $request->segment(1) == 'external-labs' ? 'active active-sub' : '' }}">
            <a href="{{ action('Optics\ExternalLabController@index') }}">
              <i class="fa fa-flask"></i>
              <span class="title">
                @lang('external_lab.external_labs')
              </span>
            </a>
          </li>
          @endcan

          {{-- Status orders --}}
          @can('status_lab_order.view')
          <li class="{{ $request->segment(1) == 'status-lab-orders' ? 'active active-sub' : '' }}">
            <a href="{{ action('Optics\StatusLabOrderController@index') }}">
              <i class="fa fa-history"></i>
              <span class="title">
                @lang('status_lab_order.status_orders')
              </span>
            </a>
          </li>
          @endcan

          {{-- Reports --}}
          @if (
          auth()->user()->can('errors_report.view') ||
          auth()->user()->can('external_labs_report.view') ||
          auth()->user()->can('transfer_sheet.view')
          )
          <li class="treeview {{ in_array($request->segment(1), ['lab-order-reports']) ? 'active active-sub' : '' }}">
            <a href="#">
              <i class="fa fa-bar-chart-o"></i>
              <span>@lang('report.reports')</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>

            <ul class="treeview-menu">
              {{-- Errors report --}}
              @can('errors_report.view')
              <li class="{{ $request->segment(2) == 'errors-report' ? 'active' : '' }}">
                <a href="{{ action('ReportController@getLabErrorsReport') }}">
                  <i class="fa fa-times-circle" aria-hidden="true"></i> @lang('lab_order.errors_report')
                </a>
              </li>
              @endcan

              {{-- External labs report --}}
              @can('external_labs_report.view')
              <li class="{{ $request->segment(2) == 'external-labs-report' ? 'active' : '' }}">
                <a href="{{ action('ReportController@getExternalLabsReport') }}">
                  <i class="fa fa-external-link-square" aria-hidden="true"></i>
                  @lang('lab_order.external_laboratory_work')
                </a>
              </li>
              @endcan

              {{-- Transfer sheet --}}
              @can('transfer_sheet.view')
              <li class="{{ $request->segment(2) == 'transfer-sheet' ? 'active' : '' }}">
                <a href="{{ action('ReportController@getTransferSheet') }}">
                  <i class="fa fa-truck" aria-hidden="true"></i> @lang('lab_order.transfers_sheet')
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endif
        </ul>
      </li>
      @endif
      @endif

      {{-- Sales --}}
      @if (
      auth()->user()->can('sell.view') ||
      auth()->user()->can('sell.create') ||
      auth()->user()->can('reservation.view') ||
      auth()->user()->can('direct_sell.access')
      )
      <li class="treeview {{ in_array($request->segment(1), [
    'sells',
    'pos',
    'reservations',
    'sell-return',
    'cashiers',
    'terminal'
    ]) ? 'active active-sub' : '' }}" id="tour_step7">
        <a href="#" id="tour_step7_menu">
          <i class="fa fa-arrow-circle-up"></i>
          <span>@lang('lang_v1.management_sells')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          {{-- All sales --}}
          @can('sell.view')
          <li class="{{ $request->segment(1) == 'sells' && $request->segment(2) == null ? 'active' : '' }}">
            <a href="{{ action('SellController@index') }}">
              <i class="fa fa-list"></i>
              @lang('lang_v1.all_sales')
            </a>
          </li>
          @endcan

          {{-- Reservations --}}
          @if (config('app.business') == 'optics')
          @can('reservation.view')
          <li class="{{ $request->segment(1) == 'reservations' && $request->segment(2) == null ? 'active' : '' }}">
            <a href="{{ action('ReservationController@index') }}">
              <i class="fa fa-calendar-check-o"></i>
              @lang('cash_register.reservations')
            </a>
          </li>
          @endcan
          @endif

          {{-- Sale returns --}}
          @can('sell.view')
          <li class="{{ $request->segment(1) == 'sell-return' && $request->segment(2) == null ? 'active' : '' }}">
            <a href="{{ action('SellReturnController@index') }}">
              <i class="fa fa-undo"></i>
              @lang('lang_v1.list_sell_return')
            </a>
          </li>
          @endcan

          {{-- Sales settings --}}
          @can('sales_settings.access')
          <li
            class="treeview @if (in_array($request->segment(1), ['cashiers', 'terminal']) ) {{ 'active active-sub' }} @endif">
            <a href="#">
              <i class="fa fa-cogs"></i>
              <span>@lang('payment.config')</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              {{-- Cashiers --}}
              <li class="{{ $request->segment(1) == 'cashiers' ? 'active' : '' }}">
                <a href="{{action('CashierController@index')}}">
                  <i class="fa fa-shopping-cart"></i>
                  @lang('cashier.cashier')
                </a>
              </li>

              {{-- POS --}}
              <li class="{{ $request->segment(1) == 'terminal' ? 'active' : '' }}">
                <a href="{{action('PosController@index')}}"><i class="fa fa-terminal"></i> @lang('payment.pos')</a>
              </li>
            </ul>
          </li>
          @endcan
        </ul>
      </li>
      @endif

      {{-- Inflows and outflows --}}
      @if (config('app.business') == 'optics')
      @if (
      auth()->user()->can('inflow_outflow.view') ||
      auth()->user()->can('flow_reason.view')
      )
      <li class="treeview {{ in_array($request->segment(1), [
    'inflow-outflow',
    'flow-reason'
    ]) ? 'active active-sub' : '' }}">
        <a href="#">
          <i class="fa fa-exchange"></i> <span>@lang('inflow_outflow.inputs_and_outputs')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @can('inflow_outflow.view')
          <li class="{{ $request->segment(1) == 'inflow-outflow' ? 'active' : '' }}">
            <a href="{{ action('Optics\InflowOutflowController@index') }}">
              <i class="fa fa-list"></i> @lang('inflow_outflow.inputs_and_outputs_list')
            </a>
          </li>
          @endcan

          @if (auth()->user()->can('flow_reason.view'))
          <li class="{{ in_array($request->segment(1), ['flow-reason']) ? 'active active-sub' : '' }}">
            <a href="#">
              <i class="fa fa-cogs"></i> <span class="title">@lang('crm.settings')</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @can('flow_reason.view')
              <li class="{{ $request->segment(1) == 'flow-reason' ? 'active active-sub' : '' }}">
                <a href="{{ action('Optics\FlowReasonController@index') }}">
                  <i class="fa fa-compress"></i> <span class="title">@lang('flow_reason.flow_reasons')</span>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endif
        </ul>
      </li>
      @endif
      @endif

      {{-- Inicio Cuentas por cobrar --}}
      @if (auth()->user()->can('cxc.access'))
      <li class="treeview {{ in_array($request->segment(1), [
        'crm-settings',
        'customer-group',
        'balances_customer',
        'accounts-receivable',
        'collections',
        'payments',
        'portfolios',
        'credit-documents',
        'business_types',
        'payment-terms',
        'sdocs',
        'customers'
        ]) ? 'active active-sub' : ''}}">
        <a href="#">
          <i class="fa fa-usd"></i>
          <span class="title">@lang('lang_v1.credits_and_payments')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
            @can('customer.view')
            <li class="{{ $request->segment(1) == 'balances_customer' ? 'active' : '' }}"><a
              href="{{action('CustomerController@indexBalancesCustomer')}}"><i class="fa fa-star"></i>
              @lang('customer.customer_balances')</a></li></a>
            </li>
            @endcan
            @can('cxc.access')
            <li class="{{ $request->segment(1) == 'accounts-receivable' ? 'active' : '' }}">
                <a href="{{action('CustomerController@accountsReceivable')}}"><i class="fa fa-money"></i> @lang('cxc.cxc')</a>
            </li>
            @endcan
            @can('cxc.collections')
            <li class="{{ $request->segment(1) == 'collections' ? 'active' : '' }}">
                <a href="{{action('ReportController@getCollections')}}"><i class="fa fa-money"></i> @lang('cxc.collections')</a>
            </li>
            @endcan
            @can('sell.create_payments')
            <li class="{{ $request->segment(1) == 'payments' && $request->segment(2) == 'multi-payments' ? 'active' : '' }}">
                <a href="{{action('TransactionPaymentController@multiPayments')}}"><i class="fa fa-money"></i> @lang('payment.multi_payments')</a>
            </li>
            @endcan

      @can('crm_settings.view')
      <li class="{{ in_array($request->segment(1), [
        'crm-settings',
        'customer-group',
        'portfolios',
        'business_types',
        'payment-terms'
        ]) ? 'active active-sub' : '' }}">
        <a href="#">
          <i class="fa fa-cogs"></i>
          <span class="title">
            @lang('customer.config')
          </span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @can('customer_group.create')
          <li class="{{ $request->segment(1) == 'customer-group' ? 'active' : '' }}"><a
              href="{{action('CustomerGroupController@index')}}"><i class="fa fa-users"></i>
              @lang('lang_v1.customer_groups')</a></li>
          @endcan

          @can('portfolios.access')
          <li class="{{ $request->segment(1) == 'portfolios' ? 'active' : '' }}">
            <a href="{{action('CustomerPortfolioController@index')}}">
              <i class="fa fa-briefcase"></i> <span>
                @lang('customer.portfolios')</span>
            </a>
          </li>
          @endcan

          @can('business_type.view')
          <li class="{{ $request->segment(1) == 'business_types' ? 'active' : '' }}"><a
              href="{{ action('BusinessTypeController@index') }}"><i class="fa fa-star"></i>
              @lang('business.business_type')</a></li>
          @endcan

          @can('payment_term.view')
          <li class="{{ $request->segment(1) == 'payment-terms' ? 'active' : '' }}"><a
              href="{{ action('PaymentTermController@index') }}"><i class="fa fa-star"></i>
              @lang('payment.credit_payment_term')</a></li>
          @endcan
        </ul>
      </li>
      @endcan
    </ul>
    </li>
    @endif
    {{-- Fin cuentas por cobrar --}}

    {{-- Inicio seccion control de inventaroi --}}
    @if (
    auth()->user()->can('product.view') ||
    auth()->user()->can('product.create') ||
    auth()->user()->can('brand.view') ||
    auth()->user()->can('unit.view') ||
    auth()->user()->can('category.view') ||
    auth()->user()->can('brand.create') ||
    auth()->user()->can('unit.create') ||
    auth()->user()->can('category.create') ||
    auth()->user()->can('report.kardex') ||
    auth()->user()->can('cost_of_sale_detail_report.view') ||
    auth()->user()->can('kardex.view') ||
    auth()->user()->can('kardex.register') ||
    auth()->user()->can('movement_type.view') ||
    auth()->user()->can('warehouse.view') ||
    auth()->user()->can('physical_inventory.view') ||
    auth()->user()->can('stock_report.view') ||
    auth()->user()->can('input_output_report.view') ||
    auth()->user()->can('list_price_report_pdf.view') ||
    auth()->user()->can('list_price_report_excel.view') ||
    auth()->user()->can('product.update')
    )
    <li class="treeview {{ in_array($request->segment(1), [
        'variation-templates',
        'products',
        'labels',
        'import-products',
        'import-opening-stock',
        'selling-price-group',
        'brands',
        'units',
        'categories',
        'material_type',
        'stock-transfers',
        'warehouses',
        'movement-types',
        'kardex',
        'product-reports',
        'create',
        'stock-adjustments',
        'register-kardex',
        'physical-inventory',
        'import-price-list',
        'edit-products'
      ]) ? 'active active-sub' : '' }}" id="tour_step5">

      <a href="#" id="tour_step5_menu">
        <i class="fa fa-cubes"></i>
        <span>
          @lang('sale.inventory')
        </span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if (config('app.business') == 'optics')
        {{-- List Products --}}
        @can('product.view')
        <li class="{{ $request->input('type') == 'product' && $request->segment(2) == '' ? 'active' : '' }}">
          <a href="{{ action('Optics\ProductController@index', ['type' => 'product']) }}">
            <i class="fa fa-list"></i>
            @lang('lang_v1.list_products')
          </a>
        </li>
        @endcan

        {{-- List materials --}}
        @can('material.access')
        <li class="{{ $request->input('type') == 'material' && $request->segment(2) == '' ? 'active' : '' }}">
          <a href="{{ action('Optics\ProductController@index', ['type' => 'material']) }}">
            <i class="fa fa-list"></i>
            @lang('material.list_materials')
          </a>
        </li>
        @endcan
        @else
        {{-- List Products --}}
        @can('product.view')
        <li class="{{ $request->segment(1) == 'products' && $request->segment(2) == '' ? 'active' : '' }}">
          <a href="{{ action('ProductController@index') }}">
            <i class="fa fa-list"></i>
            @lang('lang_v1.list_products')
          </a>
        </li>
        @endcan
        @endif

        {{-- Print Labels --}}
        @can('product.view')
        <li class="{{ $request->segment(1) == 'labels' && $request->segment(2) == 'show' ? 'active' : '' }}">
          <a href="{{ action('LabelsController@show') }}">
            <i class="fa fa-barcode"></i>
            @lang('barcode.print_labels')
          </a>
        </li>
        @endcan

        {{-- Stock Transfers --}}
        @can('stock_transfer.view')
        <li class="{{ $request->segment(1) == 'stock-transfers' && $request->segment(2) == null ? 'active' : '' }}">
          <a href="{{ action('StockTransferController@index') }}">
            <i class="fa fa-truck" aria-hidden="true"></i>
            @lang('lang_v1.stock_transfers')
          </a>
        </li>
        @endcan

        {{-- Stock Adjustments --}}
        @can('stock_adjustment.view')
        <li class="{{ $request->segment(1) == 'stock-adjustments' && $request->segment(2) == null ? 'active' : '' }}">
          <a href="{{ action('StockAdjustmentController@index') }}">
            <i class="fa fa-database" aria-hidden="true"></i>
            @lang('stock_adjustment.stock_adjustments')
          </a>
        </li>
        @endcan

        {{-- Import Products --}}
        @can('product.create')
        <li class="{{ $request->segment(1) == 'import-products' ? 'active' : '' }}">
          <a href="{{ action('ImportProductsController@index') }}">
            <i class="fa fa-download"></i>
            <span>
              @lang('product.import_products')
            </span>
          </a>
        </li>
        @endcan

        {{-- Edit products --}}
        @can('product.update')
        <li class="{{ $request->segment(1) == 'edit-products' ? 'active' : '' }}">
          <a href="{{ action('ImportProductsController@edit') }}">
            <i class="fa fa-edit"></i>
            <span>
              @lang('product.edit_products')
            </span>
          </a>
        </li>
        @endcan

        {{-- Import Opening Stock --}}
        @can('product.opening_stock')
        <li class="{{ $request->segment(1) == 'import-opening-stock' ? 'active' : '' }}">
          <a href="{{ action('ImportOpeningStockController@index') }}">
            <i class="fa fa-download"></i>
            <span>
              @lang('lang_v1.import_opening_stock')
            </span>
          </a>
        </li>
        @endcan

        {{-- Physical inventory --}}
        @if (auth()->user()->can('physical_inventory.view') || auth()->user()->can('physical_inventory.create'))
        <li class="{{ $request->segment(1) == 'physical-inventory' ? 'active' : '' }}">
          <a href="{{ action('PhysicalInventoryController@index') }}">
            <i class="fa fa-cube"></i>
            <span>
              @lang('physical_inventory.physical_inventory')
            </span>
          </a>
        </li>
        @endif

        {{-- Kardex --}}
        @if (
        auth()->user()->can('kardex.view') ||
        auth()->user()->can('kardex.register') ||
        auth()->user()->can('movement_type.view')
        )
        <li class="treeview
        @if (in_array($request->segment(1), [
        'movement-types',
        'register-kardex',
        'kardex'
        ]))
        {{ 'active active-sub' }}
        @endif">
          <a href="#">
            <i class="fa fa-exchange"></i>
            <span>@lang('kardex.kardex')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            {{-- Kardex --}}
            @if (auth()->user()->can('kardex.view'))
            <li class="{{ $request->segment(1) == 'kardex' ? 'active' : '' }}">
              <a href="{{ action('KardexController@index') }}">
                <i class="fa fa-exchange"></i>
                @lang('kardex.kardex')
              </a>
            </li>
            @endif

            {{-- Generate kardex --}}
            @if (auth()->user()->can('kardex.register'))
            <li class="{{ $request->segment(1) == 'register-kardex' ? 'active' : '' }}">
              <a href="{{ action('KardexController@getRegisterKardex') }}">
                <i class="fa fa-table"></i>
                @lang('kardex.generate_kardex')
              </a>
            </li>
            @endif

            {{-- Movement types --}}
            @if (auth()->user()->can('movement_type.view'))
            <li class="{{ $request->segment(1) == 'movement-types' ? 'active' : '' }}">
              <a href="{{ action('MovementTypeController@index') }}">
                <i class="fa fa-arrows"></i>
                @lang('movement_type.movement_types')
              </a>
            </li>
            @endif
          </ul>
        </li>
        @endif

        {{-- Reports --}}
        @if (
        auth()->user()->can('cost_of_sale_detail_report.view') ||
        auth()->user()->can('stock_report.view') ||
        auth()->user()->can('input_output_report.view') ||
        auth()->user()->can('list_price_report_pdf.view') ||
        auth()->user()->can('list_price_report_excel.view')
        )
        <li class="treeview {{ in_array($request->segment(1), ['product-reports']) ? 'active active-sub' : '' }}">
          <a href="#">
            <i class="fa fa-bar-chart-o"></i>
            <span>@lang('report.reports')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            {{-- Warehouse closure report --}}
            @can('cost_of_sale_detail_report.view')
            <li class="{{ $request->segment(2) == 'warehouse-closure-report' ? 'active' : '' }}">
              <a href="{{ action('ReportController@getCostOfSaleDetailReport') }}">
                <i class="fa fa-industry"></i>
                @lang('report.warehuose_daily_movements_menu')
              </a>
            </li>
            @endcan

            {{-- Stock report --}}
            @can('stock_report.view')
            <li class="{{ $request->segment(2) == 'show-stock-report' ? 'active' : '' }}">
              <a href="{{ action('ReportController@showStockReport') }}">
                <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                @lang('report.stock_report')
              </a>
            </li>
            @endcan

            {{-- Input Output report --}}
            @can('input_output_report.view')
            <li class="{{ $request->segment(2) == 'input-output-report' ? 'active' : '' }}">
              <a href="{{ action('ReportController@getInputOutputReport') }}">
                <i class="fa fa-cubes" aria-hidden="true"></i>
                @lang('report.input_output_report')
              </a>
            </li>
            @endcan

            {{-- List price report --}}
            @if (auth()->user()->can('list_price_report_pdf.view') ||
            auth()->user()->can('list_price_report_excel.view'))
            <li class="{{ $request->segment(2) == 'list-price-report' ? 'active' : '' }}">
              <a href="{{ action('ReportController@getListPriceReport') }}">
                <i class="fa fa-cubes" aria-hidden="true"></i>
                @lang('report.list_price_report')
              </a>
            </li>
            @endif
          </ul>
        </li>
        @endif

        @can('crm_settings.view')
        <li class="{{ in_array($request->segment(1), [
          'selling-price-group',
          'categories',
          'brands',
          'warehouses',
          'variation-templates',
          'units',
          'import-price-list',
          'material_type'
          ]) ? 'active active-sub' : '' }}">
          <a href="#">
            <i class="fa fa-cogs"></i>
            <span class="title">
              @lang('payment.config')
            </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            {{-- Variations --}}
            @can('product.create')
            <li class="{{ $request->segment(1) == 'variation-templates' ? 'active' : '' }}">
              <a href="{{ action('VariationTemplateController@index') }}">
                <i class="fa fa-circle-o"></i>
                <span>
                  @lang('product.variations')
                </span>
              </a>
            </li>

            {{-- Selling price group --}}
            <li class="{{ $request->segment(1) == 'selling-price-group' ? 'active' : '' }}">
              <a href="{{ action('SellingPriceGroupController@index') }}">
                <i class="fa fa-circle-o"></i>
                <span>
                  @lang('lang_v1.selling_price_group')
                </span>
              </a>
            </li>
            @endcan

            {{-- Units groups --}}
            @if (auth()->user()->can('unit.view') || auth()->user()->can('unit.create'))
            <li class="{{ $request->segment(1) == 'units' ? 'active' : '' }}">
              <a href="{{ action('UnitController@index') }}">
                <i class="fa fa-balance-scale"></i>
                <span>
                  @lang('unit.units_groups')
                </span>
              </a>
            </li>
            @endif

            {{-- Categories --}}
            @if (auth()->user()->can('category.view') || auth()->user()->can('category.create'))
            <li class="{{ $request->segment(1) == 'categories' ? 'active' : '' }}">
              <a href="{{ action('CategoryController@index') }}">
                <i class="fa fa-tags"></i>
                <span>
                  @lang('category.categories')
                </span>
              </a>
            </li>
            @endif

            {{-- Brands --}}
            @if (auth()->user()->can('brand.view') || auth()->user()->can('brand.create'))
            <li class="{{ $request->segment(1) == 'brands' ? 'active' : '' }}">
              <a href="{{ action('BrandController@index') }}">
                <i class="fa fa-diamond"></i>
                <span>
                  @lang('brand.brands')
                </span>
              </a>
            </li>
            @endif

            {{-- Warehouses --}}
            @if (auth()->user()->can('warehouse.view') || auth()->user()->can('warehouse.create'))
            <li class="{{ $request->segment(1) == 'warehouses' ? 'active' : '' }}">
              <a href="{{ action('WarehouseController@index') }}">
                <i class="fa fa-industry"></i>
                <span>
                  @lang('warehouse.warehouses')
                </span>
              </a>
            </li>
            @endif

            {{-- Material types --}}
            @if (auth()->user()->can('material_type.view'))
            <li class="{{ $request->segment(1) == 'material_type' ? 'active' : '' }}">
              <a href="{{ action('Optics\MaterialTypeController@index') }}">
                <i class="fa fa-cube"></i>
                @lang('material_type.material_types')
              </a>
            </li>
            @endif

            {{-- Import Prices List --}}
            @if (auth()->user()->can('product.import-price-list'))
            <li class="{{ $request->segment(1) == 'import-price-list' ? 'active' : '' }}">
              @if (config('app.business') == 'optics')
              <a href="{{ action('Optics\ProductController@getPriceList') }}">
                @else
                <a href="{{ action('ProductController@getPriceList') }}">
                  @endif
                  <i class="fa fa-download"></i>
                  <span>
                    @lang('lang_v1.import-price-list')
                  </span>
                </a>
            </li>
            @endif
          </ul>
        </li>
        @endcan
      </ul>
    </li>
    @endif
    {{-- Fin seccion control de inventaroi --}}


    {{-- Inicio compras --}}

    @if (
    auth()->user()->can('purchase.view') ||
    auth()->user()->can('purchase.create') ||
    auth()->user()->can('purchase.update') ||
    auth()->user()->can('contact.create') ||
    auth()->user()->can('contact.import') ||
    auth()->user()->can('payment_commitment.view') ||
    auth()->user()->can('debts_to_pay.view') ||
    auth()->user()->can('suggested_purchase.view') ||
    auth()->user()->can('import_expense.view') ||
    auth()->user()->can('import_expense.create') ||
    auth()->user()->can('apportionment.view') ||
    auth()->user()->can('apportionment.create')
    )
    <li class="treeview {{in_array($request->segment(1), [
          'purchases',
          'purchase-return',
          'international-purchases',
          'purchases-import',
          'contacts',
          'supplier',
          'payment-commitments',
          'debts-to-pay-report',
          'suggested-purchase-report',
          'import-expenses',
          'import',
          'apportionments'
          ]) ? 'active active-sub' : '' }}" id="tour_step6">
      <a href="#" id="tour_step6_menu"><i class="fa fa-arrow-circle-down"></i>
        <span>@lang('lang_v1.purchases_imports')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @can('purchase.view')
        <li
          class="{{ $request->segment(1) == 'purchases' || 
            ($request->segment(1) == 'international-purchases' && $request->segment(2) == 'create') ? 'active' : '' }}">
          <a href="{{action('PurchaseController@index')}}"><i class="fa fa-list"></i>@lang('purchase.list_purchase')</a>
        </li>
        @endcan

        {{-- Apportionment --}}
        @if (auth()->user()->can('apportionment.view') || auth()->user()->can('apportionment.create'))
        <li class="{{ $request->segment(1) == 'apportionments' ? 'active' : '' }}">
          <a href="{{ action('ApportionmentController@index') }}"><i class="fa fa-arrows-alt"
              aria-hidden="true"></i>@lang('apportionment.apportionment')</a>
        </li>
        @endif

        @can('supplier.view')
        <li class="{{ $request->input('type') == 'supplier' ? 'active' : '' }}">
          <a href="{{action('ContactController@index', ['type' => 'supplier'])}}"><i class="fa fa-building-o"
              aria-hidden="true"></i>@lang('report.supplier')</a>
        </li>
        @endcan

        {{-- Payment commitment --}}
        @if(auth()->user()->can('payment_commitment.view'))
        <li class="{{ $request->segment(1) == 'payment-commitments' ? 'active' : '' }}">
          <a href="{{action('PaymentCommitmentController@index')}}"><i class="fa fa-file-text-o"
              aria-hidden="true"></i>@lang('contact.payment_commitment')</a>
        </li>
        @endcan

        {{-- Debts to pay --}}
        @if(auth()->user()->can('debts_to_pay.view'))
        <li
          class="{{ ($request->segment(1) == 'purchases' && $request->segment(2) == 'debts-to-pay-report') ? 'active' : '' }}">
          <a href="{{action('PurchaseController@debtsToPay')}}"><i
              class="fa fa-money"></i>@lang('contact.debts_to_pay')</a>
        </li>
        @endcan

        {{-- Suggested purchase --}}
        @if(auth()->user()->can('suggested_purchase.view'))
        <li
            class="{{ ($request->segment(1) == 'purchases' && $request->segment(2) == 'suggested-purchase-report') ? 'active' : '' }}">
            <a href="{{action('PurchaseController@suggestedPurchase')}}">
                <i class="fa fa-shopping-cart"></i>@lang('contact.suggested_purchase')</a>
        </li>
        @endcan

        {{-- Import expenses --}}
        @if (auth()->user()->can('import_expense.view') || auth()->user()->can('import_expense.create'))
        <li class="{{ $request->segment(1) == 'import-expenses' ? 'active' : '' }}">
          <a href="{{ action('ImportExpenseController@index') }}">
            <i class="fa fa-minus-circle" aria-hidden="true"></i> @lang('import_expense.import_expenses')
          </a>
        </li>
        @endif

        @can('purchase.update')
        <li class="{{ $request->segment(1) == 'purchase-return' ? 'active' : '' }}"><a
            href="{{action('PurchaseReturnController@index')}}"><i class="fa fa-undo"></i>
            @lang('lang_v1.list_purchase_return')</a></li>
        @endcan

        {{-- import suppliers/providers --}}
        @if(auth()->user()->can('contact.import'))
        <li class="{{ ($request->segment(1) == 'contacts' && $request->segment(2) == 'import') ? 'active' : '' }}"><a
            href="{{action('ContactController@getImportContacts')}}"><i class="fa fa-download"></i>
            @lang('contact.import_suppliers')</a></li>
        @endcan

        {{-- Purchases import --}}
        @if(auth()->user()->can('purchase.create'))
        <li class="{{ $request->segment(1) == 'purchases-import' ? 'active' : '' }}">
          <a href="{{ action('PurchaseController@getImportPurchases') }}">
            <i class="fa fa-download"></i> @lang('purchase.import_purchases')
          </a>
        </li>
        @endcan
      </ul>
    </li>
    @endif
    {{-- Fin compras --}}

    {{-- Inicio cuentas por pagar --}}
    {{-- @if(auth()->user()->can('cxc.access'))
    <li class="treeview {{ in_array($request->segment(1), ['debs-pay']) ? 'active active-sub' : ''}}">
      <a href="#">
        <i class="fa fa-usd"></i>
        <span class="title">@lang('lang_v1.debs_to_pay')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @can('purchase.view')
        <li class="{{ $request->segment(1) == 'debs-pay' ? 'active' : '' }}">
          <a href="/debs-pay"><i class="fa fa-credit-card" aria-hidden="true"></i>
            @lang('lang_v1.debs_to_pay')</a>
        </li>
        @endcan
      </ul>
    </li>
    @endif --}}
    {{-- Fin cuentas por pagar --}}

    {{-- Inicio logistica --}}
    @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') )
    <li class="treeview {{ in_array($request->segment(1), ['orders_planner']) ? 'active active-sub' : '' }}"
      id="tour_step4">
      <a href="#" id="tour_step4_menu"><i class="fa fa-line-chart"
          aria-hidden="true"></i><span>@lang('lang_v1.logistic')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      @can('order.view')
      <ul class="treeview-menu" id="aas">
        <li class="{{ $request->segment(1) == 'orders_planner' ? 'active' : '' }}">
          <a href="{{action('OrderController@orderPlanner')}}" id="tour_step2"><i class="fa fa-pencil-square"></i>
            @lang('order.orders_planner')
          </a>
        </li>
      </ul>
      @endcan
    </li>
    @endif
    {{-- Fin logistica --}}

    {{-- @if(auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') )
    <li class="treeview {{ $request->segment(1) == 'stock-transfers' ? 'active active-sub' : '' }}">
      <a href="#"><i class="fa fa-truck" aria-hidden="true"></i> <span>@lang('lang_v1.stock_transfers')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>

    </li>
    @endif --}}

    {{-- @if(auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') )
    <li class="treeview {{ $request->segment(1) == 'stock-adjustments' ? 'active active-sub' : '' }}">
      <a href="#"><i class="fa fa-database" aria-hidden="true"></i>
        <span>@lang('stock_adjustment.stock_adjustment')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @can('purchase.view')
        <li class="{{ $request->segment(1) == 'stock-adjustments' && $request->segment(2) == null ? 'active' : '' }}"><a
            href="{{action('StockAdjustmentController@index')}}"><i
              class="fa fa-list"></i>@lang('stock_adjustment.list')</a></li>
        @endcan
        @can('purchase.create')
        <li
          class="{{ $request->segment(1) == 'stock-adjustments' && $request->segment(2) == 'create' ? 'active' : '' }}">
          <a href="{{action('StockAdjustmentController@create')}}"><i
              class="fa fa-plus-circle"></i>@lang('stock_adjustment.add')</a>
        </li>
        @endcan
      </ul>
    </li>
    @endif --}}

    {{-- Expenses --}}
    @if (auth()->user()->can('expense.access'))
    <li
      class="treeview {{ in_array( $request->segment(1), ['expense-categories', 'expenses']) ? 'active active-sub' : '' }}">
      <a href="#"><i class="fa fa-minus-circle"></i> <span>@lang('expense.expenses')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        {{-- List expenses --}}
        <li class="{{ $request->segment(1) == 'expenses' && empty($request->segment(2)) ? 'active' : '' }}">
          <a
            href="{{ config('app.business') == 'optics' ? action('Optics\ExpenseController@index') : action('ExpenseController@index') }}">
            <i class="fa fa-list"></i>
            @lang('lang_v1.list_expenses')
          </a>
        </li>

        {{-- Expense categories --}}
        <li class="{{ $request->segment(1) == 'expense-categories' ? 'active' : '' }}">
          <a
            href="{{ config('app.business') == 'optics' ? action('Optics\ExpenseCategoryController@index') : action('ExpenseCategoryController@index') }}">
            <i class="fa fa-circle-o"></i>
            @lang('expense.expense_categories')
          </a>
        </li>
      </ul>
    </li>
    @endif

    {{-- Reports --}}
    @if (auth()->user()->can('purchase_n_sell_report.view')
    || auth()->user()->can('contacts_report.view')
    || auth()->user()->can('tax_report.view')
    || auth()->user()->can('trending_product_report.view')
    || auth()->user()->can('sales_representative.view')
    || auth()->user()->can('cash_register_report.view')
    || auth()->user()->can('daily_z_cut_report.view')
    || auth()->user()->can('expense_report.view')
    || auth()->user()->can('sale_summary_by_seller.view')
    || auth()->user()->can('sales_by_seller_report.view')
    || auth()->user()->can('dispatched_products_report.view')
    || auth()->user()->can('connect_report.view')
    || auth()->user()->can('price_lists_report.view')
    || auth()->user()->can('sell_n_adjustment_report.view')
    || auth()->user()->can('cost_of_sale_detail_report.view')
    || auth()->user()->can('all_sales_report.view')
    || auth()->user()->can('glasses_consumption_report.view')
    || auth()->user()->can('stock_report_by_location.view')
    || auth()->user()->can('sales_per_seller_report.view')
    )
    <li class="treeview {{  in_array( $request->segment(1), [
              'reports',
              'sales-reports'
              ]) ? 'active active-sub' : '' }}" id="tour_step8">
      <a href="#" id="tour_step8_menu">
        <i class="fa fa-bar-chart-o"></i>
        <span>@lang('report.reports')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        {{-- All sales report --}}
        @can('all_sales_report.view')
        <li class="{{ $request->segment(2) == 'all-sales-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getAllSalesReport') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @lang('report.all_sales_report_menu')
          </a>
        </li>
        @endcan

        {{-- Sales per seller report --}}
        @can('sales_per_seller_report.view')
        <li class="{{ $request->segment(2) == 'sales-per-seller' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getSalesPerSellerReport') }}">
            <i class="fa fa-user" aria-hidden="true"></i>
            @lang('report.sales_per_seller')
          </a>
        </li>
        @endcan

        {{-- Payment report --}}
        @can('payment_report.view')
        <li class="{{ $request->segment(2) == 'payment' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getPaymentReport') }}">
            <i class="fa fa-usd" aria-hidden="true"></i>
            @lang('report.payment_report')
          </a>
        </li>
        @endcan

        {{-- All sales with utility report --}}
        @can('all_sales_with_utility_report.view')
        <li class="{{ $request->segment(2) == 'all-sales-with-utility-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getAllSalesWithUtilityReport') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @lang('report.all_sales_with_utility_report_menu')
          </a>
        </li>
        @endcan

        {{-- Sales summary report --}}
        @can('sales_summary_by_seller.view')
        <li class="{{ $request->segment(2) == 'sales-summary-report' ? 'active' : '' }}">
          <a href="{{ action('ReporterController@getSalesSummarySellerReport') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @lang('report.sales_summary')
          </a>
        </li>
        @endcan

        {{-- Sales by seller report --}}
        @can('sales_by_seller_report.view')
        <li class="{{ $request->segment(2) == 'sales-by-seller-report' ? 'active' : '' }}">
          <a href="{{ action('ReporterController@getSalesBySellerReport') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @lang('report.sales_by_seller')
          </a>
        </li>
        @endcan

        {{-- Dispatched products report --}}
        @can('dispatched_products_report.view')
        <li class="{{ $request->segment(2) == 'dispatched-products-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getDispatchedProducts') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @lang('report.dispatched_products_report')
          </a>
        </li>
        @endcan

        {{-- Connect report --}}
        @can('connect_report.view')
        <li class="{{ $request->segment(2) == 'connect-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getConnectReport') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @lang('report.connect_report')
          </a>
        </li>
        @endcan

        {{-- Price list report --}}
        @can('price_lists_report.view')
        <li class="{{ $request->segment(2) == 'price-lists-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getPriceListsReport') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @lang('report.price_lists_report')
          </a>
        </li>
        @endcan

        {{-- Expense purchase report --}}
        @can('expense_purchase_report.view')
        <li class="{{ $request->segment(2) == 'expense-purchase-report' ? 'active' : '' }}">
          <a href="{{ action('ReporterController@getExpensePurchaseReport') }}">
            <i class="fa fa-money" aria-hidden="true"></i>
            @lang('report.expense_report')
          </a>
        </li>
        @endcan

        {{-- Profit loss report --}}
        {{--@can('profit_loss_report.view')
        <li class="{{ $request->segment(2) == 'profit-loss' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getProfitLoss') }}">
            <i class="fa fa-money"></i>
            @lang('report.profit_loss')
          </a>
        </li>
        @endcan--}}

        {{-- Purchase sell report --}}
        {{--@can('purchase_n_sell_report.view')
        <li class="{{ $request->segment(2) == 'purchase-sell' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getPurchaseSell') }}">
            <i class="fa fa-exchange"></i>
            @lang('report.purchase_sell_report')
          </a>
        </li>
        @endcan--}}

        {{-- Tax report --}}
        {{--@can('tax_report.view')
        <li class="{{ $request->segment(2) == 'tax-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getTaxReport') }}">
            <i class="fa fa-tumblr" aria-hidden="true"></i>
            @lang('report.tax_report')
          </a>
        </li>
        @endcan--}}

        {{-- Customer suppliers report --}}
        {{--@can('contacts_report.view')
        <li class="{{ $request->segment(2) == 'customer-supplier' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getCustomerSuppliers') }}">
            <i class="fa fa-address-book"></i>
            @lang('report.contacts')
          </a>
        </li>

        {{-- Customer group report --}}
        {{--<li class="{{ $request->segment(2) == 'customer-group' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getCustomerGroup') }}">
            <i class="fa fa-users"></i>
            @lang('report.customer_groups_report')
          </a>
        </li>
        @endcan--}}

        {{-- Stock expiry report --}}
        @can('stock_expiry_report.view')
        @if(session('business.enable_product_expiry') == 1)
        <li class="{{ $request->segment(2) == 'stock-expiry' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getStockExpiryReport') }}">
            <i class="fa fa-calendar-times-o"></i>
            @lang('report.stock_expiry_report')
          </a>
        </li>
        @endif
        @endcan

        {{-- Lot report --}}
        {{--@can('lot_report.view')
        <li class="{{ $request->segment(2) == 'lot-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getLotReport') }}">
            <i class="fa fa-hourglass-half" aria-hidden="true"></i>
            @lang('lang_v1.lot_report')
          </a>
        </li>
        @endcan--}}

        {{-- Trending products report --}}
        @can('trending_product_report.view')
        <li class="{{ $request->segment(2) == 'trending-products' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getTrendingProducts') }}">
            <i class="fa fa-line-chart" aria-hidden="true"></i>
            @lang('report.trending_products')
          </a>
        </li>
        @endcan

        {{-- Stock adjustment report --}}
        {{--@can('stock_adjustment_report.view')
        <li class="{{ $request->segment(2) == 'stock-adjustment-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getStockAdjustmentReport') }}">
            <i class="fa fa-sliders"></i>
            @lang('report.stock_adjustment_report')
          </a>
        </li>
        @endcan--}}

        {{-- Product purchase report --}}
        {{--@can('product_purchase_report.view')
        <li class="{{ $request->segment(2) == 'product-purchase-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getproductPurchaseReport') }}">
            <i class="fa fa-arrow-circle-down"></i>
            @lang('lang_v1.product_purchase_report')
          </a>
        </li>--}}

        {{-- Product sell report --}}
        {{--<li class="{{ $request->segment(2) == 'product-sell-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getproductSellReport') }}">
            <i class="fa fa-arrow-circle-up"></i>
            @lang('lang_v1.report_small_business_owner')
          </a>
        </li>--}}

        {{-- Purchase payment report --}}
        {{--<li class="{{ $request->segment(2) == 'purchase-payment-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@purchasePaymentReport') }}">
            <i class="fa fa-money"></i>
            @lang('lang_v1.purchase_payment_report')
          </a>
        </li>--}}

        {{-- Sell payment report --}}
        {{--<li class="{{ $request->segment(2) == 'sell-payment-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@sellPaymentReport') }}">
            <i class="fa fa-money"></i>
            @lang('lang_v1.sell_payment_report')
          </a>
        </li>
        @endcan--}}

        {{-- Cash register report --}}
        @can('cash_register_report.view')
        <li class="{{ $request->segment(2) == 'register-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getRegisterReport') }}">
            <i class="fa fa-briefcase"></i>
            @lang('report.register_report')
          </a>
        </li>
        @endcan

        {{-- Daily Z cut report --}}
        @can('daily_z_cut_report.view')
        <li class="{{ $request->segment(2) == 'daily-z-cut-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getDailyZCutReport') }}">
            <i class="fa fa-briefcase"></i>
            @lang('report.daily_z_cut_report')
          </a>
        </li>
        @endcan

        {{-- Sales representative report --}}
        @can('sales_representative.view')
        <li class="{{ $request->segment(2) == 'sales-representative-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getSalesRepresentativeReport') }}">
            <i class="fa fa-user" aria-hidden="true"></i>
            @lang('report.sales_representative')
          </a>
        </li>
        @endcan

        {{-- Sales and stock adjustments report --}}
        {{--@can('sell_n_adjustment_report.view')
        <li class="{{ $request->segment(2) == 'sales-n-adjustments-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getSalesAndAdjustmentsReport') }}">
            <i class="fa fa-exchange" aria-hidden="true"></i>@lang('report.consumption_report')
          </a>
        </li>
        @endcan--}}

        {{-- History of clients that comprise a product report --}}
        @can('purchase.view')
        <li class="{{ $request->segment(2) == 'history_purchase_clients' ? 'active' : '' }}">
          <a href="{{ action('ReporterController@getHistoryPurchaseClients') }}">
            <i class="fa fa-history" aria-hidden="true"></i>
            @lang('report.history_purchase')
          </a>
        </li>
        @endcan

        {{-- Sales tracking report --}}
        @can('sales_tracking_report.view')
        <li class="{{ $request->segment(2) == 'sales-tracking-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getSalesTrackingReport') }}">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            @lang('report.sales_tracking_report_menu')
          </a>
        </li>
        @endcan

        {{-- Lost sale report --}}
        @can('sales_tracking_report.view')
        <li class="{{ $request->segment(2) == 'lost-sales' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getLostSalesReport') }}">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            @lang('Ventas perdidas')
          </a>
        </li>
        @endcan

        {{-- Detailed sales report --}}
        @can('detailed_commissions_report.view')
        <li class="{{ $request->segment(2) == 'detailed-commissions-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getDetailedCommissionsReport') }}">
            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
            @if (config('app.business') == 'optics')
            @lang('report.optics_detailed_commissions_report_menu')
            @else
            @lang('report.detailed_commissions_report_menu')
            @endif
          </a>
        </li>
        @endcan

        {{-- Table report --}}
        @if (in_array('tables', $enabled_modules))
        @can('table_report.view')
        <li class="{{ $request->segment(2) == 'table-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getTableReport') }}">
            <i class="fa fa-table"></i>
            @lang('restaurant.table_report')
          </a>
        </li>
        @endcan
        @endif

        {{-- Service staff report --}}
        @if(in_array('service_staff', $enabled_modules))
        @can('service_staff_report.view')
        <li class="{{ $request->segment(2) == 'service-staff-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getServiceStaffReport') }}">
            <i class="fa fa-user-secret"></i>
            @lang('restaurant.service_staff_report')
          </a>
        </li>
        @endcan
        @endif

        @if (config('app.business') == 'optics')
        {{-- Payment notes report --}}
        @can('payment_note_report.view')
        <li class="{{ $request->segment(2) == 'payment-note-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getPaymentNoteReport') }}">
            <i class="fa fa-money" aria-hidden="true"></i>
            @lang('report.payment_notes_report')
          </a>
        </li>
        @endcan

        {{-- Lab orders report --}}
        @can('lab_orders_report.view')
        <li class="{{ $request->segment(2) == 'lab-orders-report' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getLabOrdersReport') }}">
            <i class="fa fa-pencil-square" aria-hidden="true"></i>
            @lang('report.lab_orders_report_menu')
          </a>
        </li>
        @endcan

        {{-- Glasses consumption report --}}
        @can('glasses_consumption_report.view')
        <li class="{{ $request->segment(2) == 'glasses-consumption' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getGlassesConsumptionReport') }}">
            <i class="fa fa-eye" aria-hidden="true"></i>
            @lang('report.glasses_consumption_report_menu')
          </a>
        </li>
        @endcan

        {{-- Stock report by location --}}
        @can('stock_report_by_location.view')
        <li class="{{ $request->segment(2) == 'stock-by-location' ? 'active' : '' }}">
          <a href="{{ action('ReportController@getStockReportByLocation') }}">
            <i class="fa fa-hourglass-half" aria-hidden="true"></i>
            @lang('report.stock_report_by_location_menu')
          </a>
        </li>
        @endcan
        @endif
      </ul>
    </li>
    @endif
  
    @can('backup')
    <li class="treeview {{  in_array( $request->segment(1), ['backup']) ? 'active active-sub' : '' }}">
      <a href="{{action('BackUpController@index')}}"><i class="fa fa-dropbox"></i> <span>@lang('lang_v1.backup')</span>
      </a>
    </li>
    @endcan

    <!-- Call restaurant module if defined -->
    @if(in_array('tables', $enabled_modules) && in_array('service_staff', $enabled_modules) )
    @if(auth()->user()->can('crud_all_bookings') || auth()->user()->can('crud_own_bookings') )
    <li class="treeview {{ $request->segment(1) == 'bookings'? 'active active-sub' : '' }}">
      <a href="{{action('Restaurant\BookingController@index')}}"><i class="fa fa-calendar-check-o"></i>
        <span>@lang('restaurant.bookings')</span></a>
    </li>
    @endif
    @endif

    @if(in_array('kitchen', $enabled_modules))
    <li
      class="treeview {{ $request->segment(1) == 'modules' && $request->segment(2) == 'kitchen' ? 'active active-sub' : '' }}">
      <a href="{{action('Restaurant\KitchenController@index')}}"><i class="fa fa-fire"></i>
        <span>@lang('restaurant.kitchen')</span></a>
    </li>
    @endif
    @if(in_array('service_staff', $enabled_modules))
    <li
      class="treeview {{ $request->segment(1) == 'modules' && $request->segment(2) == 'orders' ? 'active active-sub' : '' }}">
      <a href="{{action('Restaurant\OrderController@index')}}"><i class="fa fa-list-alt"></i>
        <span>@lang('restaurant.orders')</span></a>
    </li>
    @endif

    {{-- @can('send_notifications')
    <li class="treeview {{  $request->segment(1) == 'notification-templates' ? 'active active-sub' : '' }}">
      <a href="{{action('NotificationTemplateController@index')}}"><i class="fa fa-envelope"></i>
        <span>@lang('lang_v1.notification_templates')</span>
      </a>
    </li>
    @endcan --}}

    

    @if (
    auth()->user()->can('business_settings.access') ||
    auth()->user()->can('barcode_settings.access') ||
    auth()->user()->can('invoice_settings.access') ||
    auth()->user()->can('tax_rate.view') ||
    auth()->user()->can('tax_rate.create') ||
    auth()->user()->can('document_type.view') ||
    auth()->user()->can('document_type.create') ||
    auth()->user()->can('correlatives.view') ||
    auth()->user()->can('correlatives.create') ||
    auth()->user()->can('alert.view')
    )


    <li
      class="treeview @if( in_array($request->segment(1), ['business', 'tax-rates', 'cashiers', 'barcodes', 'invoice-schemes', 'business-location', 'business-accounting', 'geography', 'invoice-layouts', 'printers', 'subscription', 'documents', 'diagnostic', 'carrousel', 'correlatives']) || in_array($request->segment(2), ['tables', 'modifiers']) ) {{'active active-sub'}} @endif">

      <a href="#" id="tour_step2_menu"><i class="fa fa-cog"></i> <span>@lang('business.settings')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu" id="tour_step3">
        @can('business_settings.access')

        <li class="{{ $request->segment(1) == 'business' ? 'active' : '' }}">
          <a href="{{action('BusinessController@getBusinessSettings')}}" id="tour_step2"><i class="fa fa-cogs"></i>
            @lang('business.business_settings')</a>
        </li>

        @if (auth()->user()->can('location.view') || auth()->user()->can('location.create'))
        <li class="{{ $request->segment(1) == 'business-location' ? 'active' : '' }}">
          <a href="{{action('BusinessLocationController@index')}}"><i class="fa fa-map-marker"></i>
            @lang('business.business_locations')</a>
        </li>
        @endif

        <li class="{{ $request->segment(1) == 'business-accounting' ? 'active' : '' }}">
          <a href="{{action('BusinessController@getAccountingSettings')}}"><i class="fa fa-cogs"></i>
            @lang('accounting.accounting_menu')</a>
        </li>

        <li class="{{ $request->segment(1) == 'geography' ? 'active' : '' }}">
          <a href="{{action('CountryController@index')}}"><i class="fa fa-map-marker"></i>
            @lang('geography.geography')</a>
        </li>

        @endcan

        @if (
        auth()->user()->can('document_type.view') ||
        auth()->user()->can('document_type.create') ||
        auth()->user()->can('correlatives.view') ||
        auth()->user()->can('correlatives.create')
        )
        <li
          class="treeview @if( in_array($request->segment(1), ['documents', 'correlatives']) ) {{'active active-sub'}} @endif">
          <a href="#" id="ss"><i class="fa fa-book"></i> <span>@lang('document_type.documents')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" id="aas">
            @if (auth()->user()->can('document_type.view') || auth()->user()->can('document_type.create'))
            <li class="{{ $request->segment(1) == 'documents' ? 'active' : '' }}">
              <a href="{{action('DocumentTypeController@index')}}" id="tour_step2"><i class="fa fa-file-text-o"></i>
                @lang('document_type.types')</a>
            </li>
            @endif
            @if (auth()->user()->can('correlatives.view') || auth()->user()->can('correlatives.create'))
            <li class="{{ $request->segment(1) == 'correlatives' ? 'active' : '' }}">
              <a href="{{action('DocumentCorrelativeController@index')}}" id="correls"><i class="fa fa-list-ol"></i>
                @lang('document_type.correlatives')</a>
            </li>
            @endif
          </ul>
        </li>
        @endif
        <li class="treeview @if( in_array($request->segment(1), ['carrousel']) ) {{'active active-sub'}} @endif">
          <a href="#" id="ss"><i class="fa fa-tachometer"></i> <span>Dashboard</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" id="aas">
            @can('alert.view')
            <li class="{{ $request->segment(1) == 'carrousel' ? 'active' : '' }}">
              <a href="{{action('SliderController@index')}}"><i class="fa fa-image"></i>
                <span>@lang('carrousel.carrousel_config')</span></a>
            </li>
            @endcan
          </ul>
        </li>
        <!-- diagnostic settings -->
        @can('sales_settings.access')
        <li class="treeview @if (in_array($request->segment(1), ['diagnostic']) ) {{ 'active active-sub' }} @endif">
          <a href="#"><i class="fa fa-cogs"></i> <span>@lang('material_type.diagnostic_settings')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ $request->segment(1) == 'diagnostic' ? 'active' : '' }}">
              <a href="{{ action('Optics\DiagnosticController@index') }}"><i class="fa fa-heartbeat"></i>
                @lang('diagnostic.diagnostics')</a>
            </li>
          </ul>
        </li>
        @endcan
        <!-- /.diagnostic settings -->
        @can('invoice_settings.access')
        <li class="@if( in_array($request->segment(1), ['invoice-schemes', 'invoice-layouts']) ) {{'active'}} @endif">
          <a href="{{action('InvoiceSchemeController@index')}}"><i class="fa fa-file"></i>
            <span>@lang('invoice.invoice_settings')</span></a>
        </li>
        @endcan

        <li class="{{ $request->segment(1) == 'printers' ? 'active' : '' }}">
          <a href="{{action('PrinterController@index')}}"><i class="fa fa-share-alt"></i>
            <span>@lang('printer.receipt_printers')</span></a>
        </li>

        @if(auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create'))
        <li class="{{ $request->segment(1) == 'tax-rates' ? 'active' : '' }}">
          <a href="{{action('TaxRateController@index')}}"><i class="fa fa-bolt"></i>
            <span>@lang('tax_rate.tax_rates')</span></a>
        </li>
        @endif

        @if(in_array('tables', $enabled_modules))
        @can('business_settings.access')
        <li class="{{ $request->segment(1) == 'modules' && $request->segment(2) == 'tables' ? 'active' : '' }}">
          <a href="{{action('Restaurant\TableController@index')}}"><i class="fa fa-table"></i>
            @lang('restaurant.tables')</a>
        </li>
        @endcan
        @endif

        @if(in_array('modifiers', $enabled_modules))
        @if(auth()->user()->can('product.view') || auth()->user()->can('product.create') )
        <li class="{{ $request->segment(1) == 'modules' && $request->segment(2) == 'modifiers' ? 'active' : '' }}">
          <a href="{{action('Restaurant\ModifierSetsController@index')}}"><i class="fa fa-delicious"></i>
            @lang('restaurant.modifiers')</a>
        </li>
        @endif
        @endif

        @can('devolpment.access')
        <li class="treeview @if (in_array($request->segment(1), ['kardex']) ) {{ 'active active-sub' }} @endif">
          <a href="#">
            <i class="fa fa-code"></i> <span>@lang('lang_v1.development')</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            <li
              class="{{ $request->segment(1) == 'kardex' && $request->segment(2) == 'get-recalculate-cost' ? 'active' : '' }}">
              <a href="{{ action('KardexController@getRecalculateCost') }}">
                <i class="fa fa-wrench"></i> @lang('product.recalculate_cost')
              </a>
            </li>
          </ul>
        </li>
        @endcan

        @if(Module::has('Superadmin'))
        @include('superadmin::layouts.partials.subscription')
        @endif

      </ul>
    </li>
    @endif
    @can('account.access')
    @if(Module::has('Account') && in_array('account', $enabled_modules))
    @include('account::layouts.partials.sidebar')
    @endif
    @endcan

    @if(Module::has('Woocommerce'))
    @include('woocommerce::layouts.partials.sidebar')
    @endif
    </ul>

    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>