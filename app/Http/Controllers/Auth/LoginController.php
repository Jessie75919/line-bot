<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\JWTService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Session;
use function redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/product/product';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login()
    {
        $input = Input::all();

        $messages = [
            'email' => 'Email的格式錯誤囉！',
            'exists' => '輸入的帳號不存在喔！',
        ];

        $rules = ['email'=>'required|email|exists:users,email',
                  'password'=>'required'
        ];

        $v = Validator::make($input, $rules, $messages);

        if ($v->passes()) {
            $attempt = Auth::attempt([
                'email' => $input['email'],
                'password' => $input['password']
            ]);

            if ($attempt) {
                return Redirect::intended($this->redirectTo);
            }

            return Redirect::to('login')
                           ->withErrors(['fail'=>'帳號或密碼錯誤囉！請重新輸入。']);
        }

        //fails
        return Redirect::to('login')
                       ->withErrors($v)
                       ->withInput(Input::except('password'));
    }





    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        Session::flush();

        return redirect('/login');

    }




}
