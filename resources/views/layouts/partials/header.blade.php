@inject('request', 'Illuminate\Http\Request')
<div class="modal fade business_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
</div>
<!-- Main Header -->
  <header class="main-header no-print">
    <a href="{{action('HomeController@index')}}" class="logo">
       <span class="logo-lg">{{ Session::get('business.name') }}</span> 
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation" style="height: 45px;">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" style="height: 50px;">
        <span class="sr-only">Toggle navigation</span>
      </a>

      @if(Module::has('Superadmin'))
        @include('superadmin::layouts.partials.active_subscription')
      @endif

      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">      
        
        @if($request->segment(1) == 'pos')
          <button type="button" id="register_details" title="{{ __('cash_register.register_details') }}" data-toggle="tooltip" data-placement="bottom" class="btn btn-success btn-flat pull-left m-8 hidden-xs btn-sm mt-10 btn-modal" data-container=".register_details_modal" 
          data-href="{{ action('CashRegisterController@getRegisterDetails')}}">
            <strong><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></strong>
          </button>
          <button type="button" id="close_register" title="{{ __('cash_register.close_register') }}" data-toggle="tooltip" data-placement="bottom" class="btn btn-danger btn-flat pull-left m-8 hidden-xs btn-sm mt-10 btn-modal" data-container=".close_register_modal" 
          data-href="{{ action('CashRegisterController@getCloseRegister')}}">
            <strong><i class="fa fa-window-close fa-lg"></i></strong>
          </button>
        @endif

        <a href="http://satisassistance.test" title="{{ __('rrhh.mark_assistance') }}" class="btn-flat pull-left m-8 hidden-xs btn-sm mt-10">
          <strong><i class="fa fa-list"></i>&nbsp; @lang("rrhh.mark_assistance")</strong>
        </a>

        <a href="{{ action('BusinessController@getChangeBusiness')}}" title="{{ __('home.conected_business') }}" class="btn-flat pull-left m-8 hidden-xs btn-sm mt-10 btn_business_modal">
            <strong><i class="fa fa-briefcase"></i>&nbsp; @lang("business.change_business")</strong>
        </a>

        @can('sell.create')
          <a href="{{action('SellPosController@create')}}" title="POS" data-toggle="tooltip" data-placement="bottom" class="btn btn-success btn-flat pull-left m-8 hidden-xs btn-sm mt-10">
            <strong><i class="fa fa-th-large"></i> &nbsp; POS</strong>
          </a>
        @endcan

        <!-- Help Button -->
        @if(auth()->user()->hasRole('Admin#' . auth()->user()->business_id))
          <button type="button" id="start_tour" title="@lang('lang_v1.application_tour')" data-toggle="tooltip" data-placement="bottom" class="btn btn-success btn-flat pull-left m-8 hidden-xs btn-sm mt-10">
            <strong><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i></strong>
          </button>
        @endif        

        <ul class="nav navbar-nav">
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <!-- <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image"> -->
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span>{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span>
              <span></span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                @if(!empty(Session::get('business.logo')))
                  <img src="{{ url( '/uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo"></span>
                @else
                  <img src="{{ url( '/img/default/satis_white.png' ) }}" alt="SATIS ERP"></span>
                @endif
                <p>
                  {{ Auth::User()->first_name }} {{ Auth::User()->last_name }}
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="{{action('UserController@getProfile')}}" class="btn btn-default btn-flat">@lang('lang_v1.profile')</a>
                </div>
                <div class="pull-right">
                  <a href="{{action('Auth\LoginController@logout')}}" class="btn btn-default btn-flat">@lang('lang_v1.sign_out')</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
        </ul>
      </div>
    </nav>
  </header>