<header class="app-header navbar">
    <link href="{{ asset('simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon">
        </span>
    </button>
    <a class="navbar-brand" href="#">
        <img class="navbar-brand-full" src=" {{ asset('img/brand/1459493.svg') }}" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-full" src=" {{ asset('img/brand/861120.svg') }}" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-full" src=" {{ asset('img/brand/1530867.svg') }}" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-minimized" src=" {{ asset('img/brand/861120.svg') }}" width="30" height="30" alt="CoreUI Logo">
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon">
        </span>
    </button>
    <ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item px-3">
            <a class="nav-link" href="#">
                Sistema Contable Agroservicio Jiboa 
            </a>
        </li>
       
    </ul>
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <img class="img-avatar" src="{{ asset('img/avatars/149068.svg') }}" alt="admin@bootstrapmaster.com">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>
                       {{ Auth::user()->name }}
                   </strong>
               </div>              
            <div class="dropdown-divider">
            </div>            
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-lock">                    
                </i>
                {{ __('Logout') }}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
          </a>
      </div>
  </li>
</ul>
</header>