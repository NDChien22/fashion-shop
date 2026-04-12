<?php

namespace App\Http\Controllers;

use App\Mail\ForgotResetPasswordEmail;
use App\Models\CustomerMembershipLevel;
use App\Models\MembershipLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            $request->session()->regenerate();

            return $this->redirectAfterLogin(Auth::user());
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

        // Tạo người dùng và cấp mã khách hàng trong cùng transaction
        DB::transaction(function () use ($request) {
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $this->createDefaultMembershipForUser($user);
        });

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
            return $this->redirectAfterLogin($user);
        }else{
            $user = DB::transaction(function () use ($googleUser) {
                $newUser = User::create([
                    'username' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(rand()),
                ]);

                $this->createDefaultMembershipForUser($newUser);

                return $newUser;
            });

            Auth::login($user);
            return $this->redirectAfterLogin($user)->with('toast', 'Đăng nhập thành công với Google!');
        }
    }

    protected function createDefaultMembershipForUser(User $user): void
    {
        $defaultMembershipLevel = MembershipLevel::firstOrCreate(
            ['name' => 'Thành viên mới'],
            [
                'min_points' => 0,
                'point_conversion_rate' => 1,
                'discount_rate' => 0,
            ]
        );

        CustomerMembershipLevel::create([
            'user_id' => $user->id,
            'customer_code' => $this->generateUniqueCustomerCode(),
            'membership_level_id' => $defaultMembershipLevel->id,
            'points' => 0,
        ]);
    }

    protected function generateUniqueCustomerCode(): string
    {
        do {
            $customerCode = 'KH' . now()->format('ymd') . strtoupper(Str::random(4));
        } while (CustomerMembershipLevel::where('customer_code', $customerCode)->exists());

        return $customerCode;
    }

    protected function redirectAfterLogin(User $user)
    {
        $adminRoles = ['admin', 'productmanager', 'servicescustomer'];

        if (in_array((string) $user->role, $adminRoles, true) || $user->employee()->exists()) {
            return redirect()->route('admin.admin_dashboard');
        }

        return redirect()->route('dashboard');
    }

    // Quên mật khẩu
    public function sendPasswordResetEmail(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ],[
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'email.exists' => 'Email không tồn tại',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return redirect()->route('forgot_password')->with('error', 'Email không tồn tại');
        }

        //Gửi email reset password và tạo token
        //Xóa token cũ
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        //Tạo token mới
        $token = Str::random(64);
        $resetUrl = route('password_reset', ['token' => $token, 'email' => $user->email]);

        //Lưu token vào DB
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        try {
            Mail::to($user->email)->send(new ForgotResetPasswordEmail($user, $resetUrl, 15));

            return redirect()->route('login')->with('success', 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra email.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('forgot_password')->with('error', 'Không thể gửi email đặt lại mật khẩu lúc này. Vui lòng thử lại sau.');
        }
    }

    public function resetPasswordForm(Request $request, $token){
        $email = $request->email;

        //Kiểm tra token hợp lệ và chưa hết hạn
        $passwordReset = DB::table('password_reset_tokens')
                        ->where('email', $email)
                        ->where('token', $token)
                        ->first();

        if(!$passwordReset || now()->greaterThan(
            Carbon::parse($passwordReset->created_at)->addMinutes(15)
        )){
            return redirect()->route('forgot_password')->with('error', 'Liên kết đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại.');
        }

        $data = [
            'token' => $token,
            'email' => $email,
        ];

        return view('pages.auth.reset-password', $data);
    }

    // Xử lý đặt lại mật khẩu
    public function resetPasswordHandler(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
        ],[
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 5 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu',
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$passwordReset || now()->greaterThan(
            Carbon::parse($passwordReset->created_at)->addMinutes(15)
        )) {
            return redirect()->route('forgot_password')->with('error', 'Liên kết đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại.');
        }

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->delete();

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập lại.');
    }
}
