  <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <footer class="noprint footer py-5">
    <div class="container">
      <div class="row">
      @if (!auth()->user() || \Request::is('static-sign-up')) 
        <div class="row">
          <div class="col-8 mx-auto text-center mt-1">
            <p class="mb-0 text-secondary">
              {{ __('message.copyright') }} © <script>
                document.write(new Date().getFullYear())
              </script> {{ __('message.made_by') }} 
              <a style="color: #252f40;" href="http://nilgam.com/" class="font-weight-bold ml-1" target="_blank">{{ __('message.nilgam_group') }}</a>.
            </p>
          </div>
        </div>
      @endif
    </div>
  </footer>
  <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
