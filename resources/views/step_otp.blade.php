@extends('layouts.user_type.guest')

@section('content')

<div class="container-fluid p-4 pt-1 mt-7" style="max-width: 1320px;">
    <div class="row">
        @if(session('success'))
            <div class="mt-0 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                <span class="alert-text text-white">
                {{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
            @php Session::forget('success'); @endphp
        @endif
        @if(session('error'))
            <div class="mt-0 alert alert-primary alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">
                {{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
            @php Session::forget('error'); @endphp
        @endif
        @if($errors->any())
            <div class="mt-0 alert alert-primary alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">
                {{$errors->first()}}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif
    </div>
    @include('layouts.breadcrump')
    @if(date('Y-m-d') >= env('END_REG_DATE'))
    <div class="alert alert-warning text-white" role="alert">
        {{__('message.registration_ended')}}
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <h4 class="mb-0 text-center">{{ __('message.register_for_lottery') }}</h4>
                </div>
                <div class="card-body pt-4 p-3">
                    {{-- resources/views/auth/otp-login.blade.php --}}
                  {{-- STEP 1: Phone --}}
                    <form id="phoneForm" action="{{ route('otp.request') }}" method="POST" class="transition-fast">
                        @csrf
                        <div class="mb-3">
                            <label for="phone" class="form-label">{{__('message.mobile')}}</label>
                            <div class="input-group">
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    class="form-control form-control-lg text-lg text-center"
                                    placeholder="09..."
                                    onblur="if(this.value && !final_phone_check(this.value)){$(this).val('')}"
                                    onkeypress="return numeric_check(event);"
                                    pattern="[0][9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]"
                                    required
                                >
                            </div>
                            @error('phone')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="sendCodeBtn">{{__('message.send_code')}}</button>
                    </form>

                    {{-- STEP 2: OTP --}}
                    <form id="otpForm" action="{{ route('otp.verify') }}" method="POST" class="transition-fast" style="display: none;">
                        @csrf
                        <input type="hidden" name="phone" id="otpPhone">
                        <input type="hidden" name="code" id="otpCode">

                        <div class="mb-2 d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-label mb-0">{{__('message.enter_code')}}</label>
                                <div class="text-secondary small" id="sentTo"></div>
                            </div>
                            <button type="button" class="btn btn-link p-0 small" id="editPhoneBtn">{{__('message.edit_mobile_number')}}</button>
                        </div>

                        <div class="otp-grid mb-3" dir="ltr" role="group" aria-label="One-time password inputs">
                            <input class="otp-input form-control text-center" maxlength="1" inputmode="numeric" pattern="\d*" aria-label="Digit 1" />
                            <input class="otp-input form-control text-center" maxlength="1" inputmode="numeric" pattern="\d*" aria-label="Digit 2" />
                            <input class="otp-input form-control text-center" maxlength="1" inputmode="numeric" pattern="\d*" aria-label="Digit 3" />
                            <input class="otp-input form-control text-center" maxlength="1" inputmode="numeric" pattern="\d*" aria-label="Digit 4" />
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100" id="verifyBtn" disabled>{{__('message.verify_code')}}</button>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small text-secondary" id="timerHelp" aria-live="polite"></span>
                            <button type="button" class="btn btn-outline-secondary btn-sm m-0" id="resendBtn">{{__('message.resend_code')}}</button>
                        </div>                        

                        <div id="otpError" class="text-danger small mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
  .otp-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .75rem;
  }
  .otp-input {
    font-size: 1.75rem;
    padding: .5rem 0;
    height: 3.25rem;
    border-radius: 1rem;
  }
  .transition-fast {
    transition: opacity .25s ease, transform .25s ease, visibility .25s ease;
  }
  .slide-out { opacity: 0; transform: translateY(-8px); visibility: hidden; pointer-events: none; }
  .slide-in  { opacity: 1; transform: translateY(0); visibility: visible; }
</style>

<script>
  // Requires jQuery
  $(function () {
    var $phoneForm = $('#phoneForm');
    var $otpForm   = $('#otpForm');
    var $sendBtn   = $('#sendCodeBtn');
    var $phone     = $('#phone');
    var $otpPhone  = $('#otpPhone');
    var $otpCode   = $('#otpCode');
    var $sentTo    = $('#sentTo');
    var $editPhone = $('#editPhoneBtn');
    var $resendBtn = $('#resendBtn');
    var $verifyBtn = $('#verifyBtn');
    var $timerHelp = $('#timerHelp');
    var $otpInputs = $('.otp-input');
    var $otpError  = $('#otpError');

    var CSRF = '{{ csrf_token() }}';
    var REQUEST_URL = "{{ route('otp.request') }}";
    var VERIFY_URL = "{{ route('otp.verify') }}";
    var RESEND_COOLDOWN = 120;
    var cooldown = 0, cooldownTimer = null;

    function digitsOnly(str) { return (str || '').replace(/\D/g, ''); }

    function updateVerifyEnabled() {
      var code = '';
      $otpInputs.each(function () { code += $(this).val(); });
      var complete = code.length === $otpInputs.length;
      $verifyBtn.prop('disabled', !complete);
      if (complete) $otpCode.val(code);
    }

    function startCooldown() {
      cooldown = RESEND_COOLDOWN;
      $resendBtn.prop('disabled', true);
      $timerHelp.text(cooldown + '{{__('message.seconds_until_resend_code')}}');
      if (cooldownTimer) clearInterval(cooldownTimer);
      cooldownTimer = setInterval(function () {
        cooldown -= 1;
        if (cooldown <= 0) {
          clearInterval(cooldownTimer);
          $resendBtn.prop('disabled', false);
          $timerHelp.text('{{__('message.click_to_resend_code')}}');
        } else {
          $timerHelp.text(cooldown + '{{__('message.seconds_until_resend_code')}}');
        }
      }, 1000);
    }

    function showOtpStep(phone) {
      $otpPhone.val(phone);
      $sentTo.text('{{__('message.code_send_to_mobile_x')}}'.replace(':mobile', phone));
      // Smooth swap
      $phoneForm.slideUp();
      $otpForm.slideDown();
      setTimeout(function(){ $otpInputs.eq(0).trigger('focus'); }, 180);
      startCooldown();
    }

    function showPhoneStep() {
      $otpForm.slideUp();
      $phoneForm.slideDown();
      $phone.trigger('focus');
    }

    // STEP 1: Send code
    $phoneForm.on('submit', function (e) {
      e.preventDefault();
      var phoneVal = $.trim($phone.val());
      if (!phoneVal) { $phone.trigger('focus'); return; }

      $sendBtn.prop('disabled', true);

      $.ajax({
        url: REQUEST_URL,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ phone: phoneVal }),
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
      })
      .done(function () {
        showOtpStep(phoneVal);
        $sendBtn.prop('disabled', false);
      })
      .fail(function (xhr) {
        var msg = 'Could not send code. Please try again.';
        try { msg = (xhr.responseJSON && xhr.responseJSON.message) || msg; } catch(e){}
        alert(msg);
        $sendBtn.prop('disabled', false);
      });
    });

    // Edit phone
    $editPhone.on('click', function () { showPhoneStep(); });

    // Resend
    $resendBtn.on('click', function () {
      if ($resendBtn.prop('disabled')) return;
      var phoneVal = $otpPhone.val();

      $resendBtn.prop('disabled', true);

      $.ajax({
        url: REQUEST_URL,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ phone: phoneVal, resend: true }),
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
      })
      .done(function () {
        startCooldown();
        $otpInputs.val('');
        $otpInputs.eq(0).trigger('focus');
        updateVerifyEnabled();
      })
      .fail(function (xhr) {
        var msg = 'Could not resend code.';
        try { msg = (xhr.responseJSON && xhr.responseJSON.message) || msg; } catch(e){}
        alert(msg);
        $resendBtn.prop('disabled', false);
      });
    });

    // OTP inputs UX
    $otpInputs.on('input', function () {
      var $this = $(this);
      var idx = $otpInputs.index(this);

      $this.val(digitsOnly($this.val()).slice(0,1));

      if ($this.val() && idx < $otpInputs.length - 1) {
        $otpInputs.eq(idx + 1).trigger('focus').select();
      }
      updateVerifyEnabled();
    });

    $otpInputs.on('keydown', function (e) {
      var idx = $otpInputs.index(this);
      if (e.key === 'Backspace' && !$(this).val() && idx > 0) {
        e.preventDefault();
        $otpInputs.eq(idx - 1).val('').trigger('focus');
        updateVerifyEnabled();
      }
      if (e.key === 'ArrowLeft' && idx > 0)  $otpInputs.eq(idx - 1).trigger('focus');
      if (e.key === 'ArrowRight' && idx < $otpInputs.length - 1) $otpInputs.eq(idx + 1).trigger('focus');
    });

    $otpInputs.on('paste', function (e) {
      var pasted = digitsOnly((e.originalEvent.clipboardData || window.clipboardData).getData('text')).slice(0, $otpInputs.length);
      if (!pasted) return;
      e.preventDefault();
      for (var i = 0; i < $otpInputs.length; i++) {
        $otpInputs.eq(i).val(pasted[i] || '');
      }
      var focusIdx = Math.min(pasted.length, $otpInputs.length) - 1;
      if (focusIdx >= 0) $otpInputs.eq(focusIdx).trigger('focus');
      updateVerifyEnabled();
    });

    function showError(msg) {
      $otpError.text(msg || 'Invalid or expired code.').removeClass('d-none');
      setTimeout(function () { $otpError.addClass('d-none').text(''); }, 5000);
    }

    // STEP 2: Verify submit — ensure hidden code is filled
    $otpForm.off('submit').on('submit', function (e) {
      e.preventDefault();

      // Compose code from the 4 inputs
      var code = '';
      $otpInputs.each(function () { code += $(this).val(); });

      if (code.length !== $otpInputs.length) {
        showError('Please enter the full 4-digit code.');
        return;
      }

      $otpCode.val(code);
      $verifyBtn.prop('disabled', true);

      $.ajax({
        url: VERIFY_URL,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          phone: $otpPhone.val(),
          code: code
        }),
        headers: {
          'X-CSRF-TOKEN': CSRF,
          'Accept': 'application/json'
        }
      })
      .done(function (resp) {
        if (resp && resp.success) {
          var dest = resp.redirect || '/';
          window.location.assign(dest);
        } else {
          showError((resp && resp.message) || 'Invalid or expired code.');
          $verifyBtn.prop('disabled', false);
        }
      })
      .fail(function (xhr) {
        var msg = 'Verification failed. Please try again.';
        try { msg = (xhr.responseJSON && xhr.responseJSON.message) || msg; } catch (e) {}
        showError(msg);
        $verifyBtn.prop('disabled', false);
      });
    });
  });

  if ('OTPCredential' in window) {
      window.addEventListener('DOMContentLoaded', async () => {
          try {
              const content = await navigator.credentials.get({
                  otp: { transport: ['sms'] },
                  signal: new AbortController().signal,
              });
              const code = (content.code + '').replace(/\D/g, '');
              const boxes = document.querySelectorAll('.otp-input');
              for (let i = 0; i < boxes.length; i++) {
                  boxes[i].value = code[i] || '';
              }
              $('.otp-input').trigger('input');
              if (code.length >= 4) {
                  $('#verifyBtn').trigger('click');
              }
          } catch (error) {
              console.error('OTP retrieval failed: ', error);
          }
      });
  }
</script>
@endsection