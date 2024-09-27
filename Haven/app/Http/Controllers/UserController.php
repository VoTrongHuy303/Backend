<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\PasswordReset;

class UserController extends Controller
{
    private Builder $model;

    public function __construct()
    {
        $this->model = (new User())->query();
    }

    public function index(Request $request)
    {
        $users = $this->model->with('role')->get();
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = $this->model->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }


    public function store(StoreUserRequest $request)
    {
        $data = $request->all();

        // Kiểm tra xem có tải ảnh lên không
        if ($request->hasFile('image')) {
            try {
                // Upload ảnh lên Cloudinary
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
                $data['image'] = $uploadedFileUrl;
            } catch (Exception $e) {
                return redirect()->back()->withErrors(['image' => 'Failed to upload image to Cloudinary: ' . $e->getMessage()]);
            }
        }

        // Tạo user với dữ liệu
        $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'image' => $data['image'] ?? null,
            'phone' => $data['phone'],
            'address' => $data['address'],
            'status' => $data['status'],
        ]);

        return redirect()->route('users.index')->with('success', 'Tạo người dùng thành công!');
    }


    // public function update(UpdateUserRequest $request, User $user)
    // {
    //     $data = $request->all();

    //     // Kiểm tra xem có tải ảnh lên không
    //     if ($request->hasFile('image')) {
    //         // Lưu ảnh vào thư mục 'public/images' và lấy tên file
    //         $imagePath = $request->file('image')->store('images', 'public');
    //         $data['image'] = $imagePath; // Lưu đường dẫn ảnh vào database
    //     }

    //     // Cập nhật user với dữ liệu, bao gồm cả đường dẫn ảnh nếu có
    //     $user->update([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => $data['password'] ? Hash::make($data['password']) : $user->password,
    //         'role_id' => $data['role_id'],
    //         'image' => $data['image'] ?? $user->image,  // Lưu đường dẫn ảnh nếu có
    //         'phone' => $data['phone'],
    //         'address' => $data['address'],
    //         'status' => $data['status'],
    //     ]);

    //     return redirect()->route('users.index')->with('success', 'Cập nhật người dùng thành công!');
    // }



    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->all();
    
        // Kiểm tra xem có tải ảnh lên không
        if ($request->hasFile('image')) {
            try {
                // Upload ảnh lên Cloudinary và lấy URL ảnh đã upload
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
                $data['image'] = $uploadedFileUrl; // Lưu URL từ Cloudinary vào biến $data
            } catch (Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        } else {
            // Nếu không có ảnh mới được tải lên, giữ nguyên ảnh cũ
            $data['image'] = $user->image;
        }
    
        // Cập nhật user với dữ liệu, bao gồm cả đường dẫn ảnh nếu có
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ? Hash::make($data['password']) : $user->password,
            'role_id' => $data['role_id'],
            'image' => $data['image'],  // Sử dụng ảnh mới hoặc giữ lại ảnh cũ
            'phone' => $data['phone'],
            'address' => $data['address'],
            'status' => $data['status'],  // Đảm bảo cập nhật cả status
        ]);
    
        return redirect()->route('users.index')->with('success', 'Cập nhật người dùng thành công!');
    }
    

    


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Xóa người dùng thành công.');
    }

    // Chức năng đăng nhập
    public function indexlogin(Request $request)
    {
        return view("login");
    }

    public function login(LoginRequest $request)
    {
        // Lấy dữ liệu email và mật khẩu từ form
        $credentials = $request->only('email', 'password');

        // Kiểm tra xác thực
        if (Auth::attempt($credentials)) {
            // Xác thực thành công
            $request->session()->regenerate();

            // Chuyển hướng đến trang sau khi đăng nhập thành công
            return redirect()->route('users.index')->with('success', 'Đăng nhập thành công.');
        }

        // Xác thực thất bại
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput();
    }
   

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Đăng xuất thành công.');
    }
    public function googlelogin()
    {
        return Socialite::driver('google')->redirect();
    }
    public function googlecallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Tìm người dùng bằng google_id
            $findUser = User::where('google_id', $googleUser->id)->first();
            
            if ($findUser) {
                // Đăng nhập nếu tìm thấy người dùng
                Auth::login($findUser);
                return redirect()->route('users.index');
            } else {
                // Tạo người dùng mới với role_id = 2 (user)
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id'=> $googleUser->id,
                    'password' => Hash::make('123456dummy'), // Mật khẩu ngẫu nhiên cho user từ Google
                    'role_id' => 2, // Đặt role_id là 2 (user)
                ]);

                // Đăng nhập người dùng mới
                Auth::login($newUser);
                return redirect()->route('users.index');
            }

        } catch (Exception $e) {
            return redirect('/login')->withErrors('Unable to login using Google. Please try again.');
        }
    }

    public function facebooklogin()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function facebookcallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $findUser = User::where('facebook_id', $facebookUser->id)->first();

            if ($findUser) {
                Auth::login($findUser);
                return redirect()->route('users.index');
            } else {
                $newUser = User::create([
                    'name' => $facebookUser->name,
                    'email' => $facebookUser->email,
                    'facebook_id'=> $facebookUser->id,
                    'password' => Hash::make('123456dummy'),
                    'role_id' => 2, // Đặt role_id là 2 cho user mới từ Facebook
                ]);

                Auth::login($newUser);
                return redirect()->route('users.index');
            }

        } catch (Exception $e) {
            return redirect('/login')->withErrors('Unable to login using Facebook. Please try again.');
        }
    }

    //Reset Password

    public function showForgotForm()
    {
        return view('forgot-password');
    }

    // Send reset code via email
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Generate a random 5-digit code
        $code = rand(10000, 99999);

        // Store the reset code in the password_resets table
        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            ['token' => $code, 'created_at' => Carbon::now()]
        );

        // Send the code via email
        Mail::send('reset-code', ['code' => $code], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Your Password Reset Code');
        });

        return redirect()->route('password.reset')->with([
            'email' => $request->email,
            'success' => 'A 5-digit reset code has been sent to your email.'
        ]);
    }

    // Show reset password form
    public function showResetForm()
    {
        return view('reset-password');
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric|digits:5',
            'password' => 'required|confirmed|min:6',
        ]);

        // Validate the code
        $reset = PasswordReset::where('email', $request->email)
                              ->where('token', $request->code)
                              ->where('created_at', '>=', Carbon::now()->subMinutes(30)) // Check if code is within 30 minutes
                              ->first();

        if (!$reset) {
            return back()->withErrors(['code' => 'The code is invalid or expired.']);
        }

        // Update the user's password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the reset entry
        PasswordReset::where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password has been reset successfully.');
    }

    //Register
    public function showRegisterForm()
    {
        return view('register');
    } public function showVerifyForm()
    {
        return view('verify');
    }
    public function sendRegisterCode(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'name' => 'required',
        'password' => 'required|min:6',
    ]);

    // Tạo mã xác thực ngẫu nhiên
    $code = rand(10000, 99999);

    // Lưu thông tin vào bảng password_resets
    PasswordReset::updateOrCreate(
        ['email' => $request->email],
        ['token' => $code, 'created_at' => Carbon::now()]
    );

    // Gửi mã xác thực qua email
    Mail::send('register-code', ['code' => $code], function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Mã xác thực đăng ký tài khoản');
    });

    return redirect()->route('register.verify')->with([
        'email' => $request->email,
        'name' => $request->name,
        'password' => $request->password,
    ]);
}
public function verifyRegisterCode(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:password_resets,email',
        'code' => 'required|numeric|digits:5',
    ]);

    // Kiểm tra mã xác thực
    $reset = PasswordReset::where('email', $request->email)
                          ->where('token', $request->code)
                          ->where('created_at', '>=', Carbon::now()->subMinutes(30))
                          ->first();

    if (!$reset) {
        return back()->withErrors(['code' => 'Mã xác thực không đúng hoặc đã hết hạn.']);
    }

    // Tạo tài khoản mới
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Xóa mã xác thực
    PasswordReset::where('email', $request->email)->delete();

    return redirect()->route('login')->with('success', 'Tài khoản đã được tạo thành công.');
}


}
