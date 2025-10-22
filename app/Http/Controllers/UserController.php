<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

    public function index()
    {
        $this->authorize('users');
        $users = User::orderBy('id')->get();
        return view('users', ['users' => $users, 'permissions' => []]);
    }

    public function signup()
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'agreement' => ['accepted']
        ]);
        $attributes['password'] = bcrypt($attributes['password']);

        session()->flash('success', __('message.account_created_succeefully'));
        $user = User::create($attributes);
        return redirect('/');
    }

    public function login()
    {
        $attributes = request()->validate([
            'email'=>'required|email',
            'password'=>'required' 
        ]);

        $user = User::where('email', $attributes['email'])->first();
        if ($user && !$user['active']) {
            return back()->withErrors(['email' => __('message.your_account_is_not_active')]);
        }

        if(Auth::attempt($attributes))
        {
            session()->regenerate();
            Session::put('sidebar_show', 1);
            return redirect('/')->with(['success' => __('message.you_have_logged_in_successfuly')]);
        }
        else{
            return back()->withErrors(['email' => __('message.email_or_password_invalid')]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with(['success' => __('message.you_have_been_logged_out')]);
    }

    public function change_user_status($id, $status = 1)
    {
        $this->authorize('users');
        $account = User::find($id);
        if ($account) {
            $type = 'success';
            $message = __('message.user').' '.__('message.updated_successfully');
            if ($account->role == 'not_selected') {
                $type = 'error';
                $message = __('message.choose_user_permission');
            } elseif($status == 1) {
                $account->active = 1;
                $account->save();
            } elseif($status == 0) {
                $account->active = 0;
                $account->save();
            }else{
                $type = 'error';
                $message = __('message.status').' '.__('message.not_found');
            }
            
        } else {
            $type = 'error';
            $message = __('message.user').' '.__('message.not_found');
        }
        
        return redirect('/users')->with($type, $message);
    }

    public function change_user_permission($id)
    {
        $this->authorize('users');
        $attributes = request()->validate([
            'role' => ['required', 'in:not_selected,admin,user,agent'],
        ]);

        if (User::where('id', $id)->update($attributes)) {
            return redirect('/users')->with('success',__('message.permission').' '.__('message.updated_successfully'));
        } else {
            return redirect('/users')->with('error',__('message.edit_permission').' '.__('message.failed'));
        }
    }

    public function delete($id)
    {
        $this->authorize('users');
        $user = Auth::user();
        if ($user->id == $id) {
            return redirect('/users')->with('error', __('message.failed'));
        } else {
            if (User::where('id',$id)->delete()) {
                return redirect('/users')->with('success', __('message.user').' '.__('message.deleted_successfully'));
            } else {
                return redirect('/users')->with('error', __('message.user').' '.__('message.not_found'));
            }
        }
    }

    public function profile()
    {
        return view('profile');
    }

    public function edit_profile(Request $request)
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'mobile'     => ['nullable', 'regex:/[0][9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/'],
        ]);
        
        User::where('id',Auth::user()->id)
        ->update($attributes);
        
        return redirect('/profile')->with('success',__('message.profile').' '.__('message.updated_successfully'));
    }
}
