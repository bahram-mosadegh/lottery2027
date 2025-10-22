  <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  @php
  // $res = Http::withoutVerifying()->timeout(1)->get('http://www.geoplugin.net/json.gp?ip='.\Request::ip());
  $enamad_src =  asset('assets/img/enamad.png');
  //if($res && $res->object() && $res->object()->geoplugin_countryName) {
  //  if($res->object()->geoplugin_countryName == 'Iran') {
  //    $enamad_src = 'https://Trustseal.eNamad.ir/logo.aspx?id=169610&Code=6YlCGggzJ2IS4urHgGsh';
  //  }
  //}
  @endphp
  <footer class="noprint footer d-flex align-items-end" style="background-color: rgb(149,178,221);background-size: cover;background-position: center bottom; min-height: 50vh;">
        <div class="container py-4">
          <div class="row">
            <div class="col-sm-12 col-md-6 text-center">
              <p style="margin-bottom: 1px;"><a referrerpolicy="origin" target="_blank" href="https://trustseal.enamad.ir/?id=141430&amp;Code=xXur8NCxSUzgn9Xrea1f"><img referrerpolicy="origin" src="{{$enamad_src}}" alt="" style="cursor:pointer" id="6YlCGggzJ2IS4urHgGsh"></a> <img class="alignnone size-thumbnail wp-image-7609" src="{{asset('assets/img/samandehi.png')}}" alt="" width="100" height="100" /></p>
            </div>
            <div class="col-sm-12 col-md-6 text-white" style="line-height: 40px;">
              <img class="alignnone size-full wp-image-7610" role="img" src="{{asset('assets/img/svgexport-60.svg')}}" alt="" width="19" height="22" />  تهران، خیابان‌ شریعتی، خیابان‌ دستگردی(ظفر)، بین شمس‌تبریزی و نفت شمالی، جنب بانک شهر، پلاک 148،طبقه اول
              <br>
              <a href="tel:+982175237" class="text-white"><img class="alignnone size-full wp-image-7611" role="img" src="{{asset('assets/img/svgexport-62.svg')}}" alt="" width="21" height="21" />  75237 – 021</a>
              <br>
              <a href="mailto:info@nilgam.com" class="text-white"><img class="alignnone size-full wp-image-7612" role="img" src="{{asset('assets/img/svgexport-64.svg')}}" alt="" width="22" height="19" />  info@nilgam.com</a>
            </div>
          </div>
          <div class="row m-4 mt-7">
            <div class="mx-auto text-center mt-1">
              <p class="mb-0 text-secondary text-white">
                {{ __('message.copyright') }} © <script>
                  document.write(new Date().getFullYear())
                </script> {{ __('message.made_by') }} 
                <a style="color: #252f40;" href="http://nilgam.com/" class="font-weight-bold ml-1 text-white" target="_blank">{{ __('message.nilgam_group') }}</a>.
              </p>
            </div>
          </div>
        </div>
  </footer>
  <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
