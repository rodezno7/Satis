<!-- Main Footer -->
<footer class="main-footer no-print">
    <!-- Default to the left -->
      {{ config('app.name') }} - v{{config('app.app_version')}} Copyright &copy; {{ date('Y') }} All rights reserved.
    <div class="btn-group pull-right">
      <span class="logged-in"> ● </span> <small>@lang('lang_v1.your_ip') {{ Session::get('user.user_ip') }}</small>
    </div>
</footer>