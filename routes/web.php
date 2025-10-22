<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth'], function () {
	Route::get('profile', [UserController::class, 'profile'])->name('profile');

	Route::get('users', [UserController::class, 'index'])->name('users');
	Route::get('change-user-status/{id}/{status}', [UserController::class, 'change_user_status']);
	Route::get('delete-user/{id}', [UserController::class, 'delete']);
	Route::post('change_user_permission/{id}', [UserController::class, 'change_user_permission']);

	Route::get('applicants', [RegisterController::class, 'index']);
	Route::get('check_data', [RegisterController::class, 'check_data']);
	Route::post('check_data', [RegisterController::class, 'check_data_post']);

	// Route::get('coupons', [CouponController::class, 'index']);
	// Route::get('add_coupon', [CouponController::class, 'add_get']);
	// Route::post('add_coupon', [CouponController::class, 'add_post']);
	// Route::get('edit_coupon/{id}', [CouponController::class, 'edit_get']);
	// Route::post('edit_coupon/{id}', [CouponController::class, 'edit_post']);
	// Route::get('delete_coupon/{id}', [CouponController::class, 'delete']);

    Route::get('logout', [UserController::class, 'logout']);
	Route::post('edit_profile', [UserController::class, 'edit_profile']);
});

Route::group(['middleware' => 'guest'], function () {
	Route::get('/signup', function ()
	{
		return view('session.signup');
	});
	Route::post('/signup', [UserController::class, 'signup']);
    Route::get('/login', function ()
	{
		return view('session.login');
	})->name('login');
	Route::post('/login', [UserController::class, 'login']);
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/applicants');
    } else {
        return redirect(route('step_zero'));
    }
});

Route::post('/otp/request', [RegisterController::class, 'otp_request'])->name('otp.request');
Route::post('/otp/verify',  [RegisterController::class, 'otp_verify'])->name('otp.verify');
Route::post('/upload_file', [RegisterController::class, 'upload_file']);
Route::post('/delete_file', [RegisterController::class, 'delete_file']);
Route::get('/step_zero', [RegisterController::class, 'step_zero'])->name('step_zero');
Route::get('/step_one/{applicant_id?}', [RegisterController::class, 'step_one'])->name('step_one');
Route::post('/step_one', [RegisterController::class, 'step_one_post']);
Route::get('/step_two/{applicant_id?}', [RegisterController::class, 'step_two']);
Route::post('/step_two', [RegisterController::class, 'step_two_post']);
Route::get('/step_three/{applicant_id?}', [RegisterController::class, 'step_three']);
Route::get('/step_four/{applicant_id?}', [RegisterController::class, 'step_four']);
Route::post('/step_four', [RegisterController::class, 'step_four_post']);
Route::get('/step_six/{applicant_id?}', [RegisterController::class, 'step_six']);
Route::get('/step_image/{applicant_id?}', [RegisterController::class, 'step_image']);
Route::get('/payment/{applicant_id?}', [RegisterController::class, 'payment']);
Route::post('/nilgam_pay_callback', [RegisterController::class, 'nilgam_pay_callback']);
Route::get('create_in_crm_bulk/{limit?}', [RegisterController::class, 'create_in_crm_bulk']);
Route::get('translate_data/{limit?}', [RegisterController::class, 'translate_data']);
Route::get('register_data', [RegisterController::class, 'register_data']);
Route::get('solve_captcha',  function () {
	return view('captcha');
});
Route::get('registration_tracking_number', [RegisterController::class, 'registration_tracking_number']);
Route::get('check_lottery_status', [RegisterController::class, 'check_lottery_status']);
Route::get('update_lottery_status', [RegisterController::class, 'update_lottery_status']);
