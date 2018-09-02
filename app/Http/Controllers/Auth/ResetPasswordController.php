<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendResetPasswordMail;
use App\Repository\Pos\UserRepository;

use App\Services\Pos\JWTService;
use Firebase\JWT\ExpiredException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Validator;
use function bcrypt;
use function compact;
use function redirect;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     * @var string
     */
    protected $redirectTo = '/home';


    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function sendResetPasswordEmail(Request $request,JWTService $JWTService)
    {
        $previous = 'password/reset';

        $v = Validator::make($input = Input::all(),
            ['email' => 'required|email|exists:users,email'],
            [
                'email'    => 'Email的格式錯誤囉！',
                'exists'   => '輸入的帳號不存在喔！',
                'required' => 'Email不可為空白喔！',
            ]
        );

        if ($v->fails()) {
            return redirect($previous)->withErrors($v);
        }

        $user = UserRepository::findUserByEmail($input['email']);
        $link = $JWTService->setEncodeData(['userId' => $user->id])
                           ->setValidTime(15)
                           ->encode()
                           ->generateLink('update_pwd');

        $mailable =
            (new SendResetPasswordMail())
                ->setLink($link)
                ->setUser($user);

        Mail::to($user)
            ->send($mailable);

        $request->session()->flash('success', 'ChuC已經發送更改密碼系統信到您的註冊信箱囉，趕快去收信吧。');

        return redirect($previous);
    }


    public function UpdatePassword(Request $request, JWTService $JWTService)
    {
        $input = Input::all();
        $rules = [
            'code' => 'required|string'
        ];

        $v = Validator::make($input, $rules);
        if ($v->fails()) {
            return $v->errors();
        }

        $decoded = $JWTService->decode($request->code);
        if ($decoded instanceof ExpiredException) {
            return $decoded->getMessage();
        }

        $userId = $decoded->userId;

        return view('auth.passwords.resetPassword', compact('userId'));
    }


    public function ResetPassword(Request $request)
    {
        $previous = 'password/reset';

        $v = Validator::make($request->all(),
            [
                'password' => 'required|string|max:225',
                'userId'   => 'required|numeric'
            ],
            [
                'required' => '密碼不可為空白喔！',
            ]
        );

        if ($v->fails()) {
            return redirect($previous)
                ->withErrors($v);
        }

        $user = UserRepository::findUser($request->userId);
        if (!$user) {
            return 'Not Found This User';
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $request->session()->flash('success_updatePW', '您的密碼已經更新成功囉!');

        return redirect('login');
    }
}
