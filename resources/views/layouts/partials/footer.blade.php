<!-- Main Footer -->
  <footer class="main-footer no-print">
    <!-- To the right -->
    <!-- <div class="pull-right hidden-xs">
      Anything you want
    </div> -->
    <!-- Default to the left -->
      {{ config('app.name', 'EnvexERP') }} - V{{config('author.app_version')}} Copyright &copy; {{ date('Y') }} All rights reserved.
    <div class="btn-group pull-right">
      <span class="logged-in"> ● </span> <small>@lang('lang_v1.your_ip') {{ Session::get('user.user_ip') }}</small>
    </div>
</footer>