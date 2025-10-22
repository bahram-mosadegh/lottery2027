<style>
.dropbtn {
  font-size: 16px;
  border: none;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 180px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 8px;
  top: 48px;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  border-radius: 8px;
}

.dropdown-content a:hover {background-color: #0047ab; color: #fff;}

.dropdown:hover .dropdown-content {display: block;}

@media (max-width: 1199px) {
  .hide-on-mobile {
    display: none !important;
  }

  .show-on-mobile {
    display: flex !important;
  }
}

@media (min-width: 1200px) {
  .sidenav {
    display: none !important;
  }
}

@media (max-width: 767px) {
  .switch {
    flex-direction: column-reverse;
  }
  .description {
    margin-top: 30px;
  }
}

.navbar-expand-xs {
  top: 55px !important;
}
</style>
<!-- Navbar -->
<nav class="navbar noprint navbar-expand-lg position-absolute top-0 z-index-3 py-2 start-0 end-0 mx4" style="background-color: #fff;height: 70px;box-shadow: 0 1px 8px rgba(0,0,0,0.1);">
  <div class="container-fluid container hide-on-mobile" style="max-width: 97% !important;">
    <div>
      <a href="https://nilgam.com/">
        <img src="{{asset('assets/img/nilgam_safar.png')}}">
      </a>
    </div>
    <div class="me-2 ms-2">
      <a href="https://nilgam.com/immigration/lottery/">ثبت نام لاتاری</a>
    </div>
    <div>
      <img width="30px" class="cursor-pointer share" src="{{asset('assets/img/share-icon.svg')}}">
    </div>
  </div>
  <div class="container-fluid container show-on-mobile p-1" style="max-width: 99% !important; display: none;">
    <ul class="navbar-nav p-0">
      <li class="nav-item d-xl-none pe-3 d-flex align-items-center">
        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
          <div class="sidenav-toggler-inner">
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
          </div>
        </a>
      </li>
    </ul>
    <div>
      <a href="https://nilgam.com/">
        <img src="{{asset('assets/img/nilgam_safar.png')}}">
      </a>
    </div>
    <div>
      <img width="30px" class="cursor-pointer share" src="{{asset('assets/img/share-icon.svg')}}">
    </div>
  </div>
</nav>
<!-- End Navbar -->

<script>
  $('.share').on('click', async function () {
    const shareData = {
        title: document.title,
        text: 'ثبت نام لاتاری',
        url: '{{ route('step_zero') }}'
    };

    if (navigator.share) {
        try {
            await navigator.share(shareData);
            // console.log('Page shared successfully');
        } catch (err) {
            // console.error('Error sharing:', err);
        }
    } else {
        const fallbackLinks = `
            <div>
                <p>Share via:</p>
                <ul>
                    <li><a href="mailto:?subject=${encodeURIComponent(shareData.title)}&body=${encodeURIComponent(shareData.text)}%0A${encodeURIComponent(shareData.url)}">Email</a></li>
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareData.url)}" target="_blank">Facebook</a></li>
                    <li><a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(shareData.text)}&url=${encodeURIComponent(shareData.url)}" target="_blank">Twitter</a></li>
                    <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(shareData.url)}&title=${encodeURIComponent(shareData.title)}&summary=${encodeURIComponent(shareData.text)}" target="_blank">LinkedIn</a></li>
                </ul>
            </div>
        `;
        $('body').append(fallbackLinks);
    }
});

</script>
