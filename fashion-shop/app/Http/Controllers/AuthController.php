<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    //Login
    public function loginHandler(Request $request){
        $filedType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if($filedType == 'email'){
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required|min:5'
            ],[
                'login_id.required' => 'Vui lòng nhập email hoặc username',
                'login_id.email' => 'Địa chỉ email không hợp lệ',
                'login_id.exists' => 'Tài khoản không tồn tại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);
        }else{
            $request->validate([
                'login_id' => 'required|exists:users,username',
                'password' => 'required|min:5'
            ],[
                'login_id.required' => 'Vui lòng nhập email hoặc username',
                'login_id.exists' => 'Tài khoản không tồn tại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);
        }

        $creds = array(
            $filedType => $request->login_id,
            'password' => $request->password
        );

        if(Auth::attempt($creds)){
            return redirect()->route('dashboard');
        } else {
            return back()->withErrors(['password' => 'Mật khẩu không chính xác'])->withInput();
        }
    }

    //Register
    public function registerHandler(Request $request){
        // Validate data
        $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
        ],[
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 5 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu',
        ]);

        // Tạo người dùng mới
        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        // Redirect về trang đăng nhập 
        return redirect()->route('login')->with('toast', 'Đăng ký tài khoản thành công!');
    }

    //Logout
    public function logoutHandler(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    //login with Google
    public function redirectToGoogle(){
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Could not redirect to Google']);
        }
    }

    public function handleGoogleCallback(){
        try{
            // Smart SSL verification based on environment
            $verifySSL = app()->isLocal() ? false : (env('CURL_CA_BUNDLE') ?: true);
            
            $client = new Client([
                'verify' => $verifySSL,
            ]);
            
            $googleUser = Socialite::driver('google')
                ->setHttpClient($client)
                ->stateless()
                ->user();
        }catch(\Exception $e){
            return redirect()->route('login')
                ->withErrors(['google' => 'Google authentication failed: ' . $e->getMessage()]);
        }

        $user = User::where('email', $googleUser->email)->first();

        if($user){
            Auth::login($user);
            return redirect()->route('dashboard');        
        }else{
            $user = User::create([
                'username' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(rand()),
            ]);
            Auth::login($user);
            return redirect()->route('dashboard')->with('toast', 'Đăng nhập thành công với Google!');
        }
    }
}
