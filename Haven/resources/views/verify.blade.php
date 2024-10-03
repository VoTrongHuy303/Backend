<!-- verify.blade.php -->
<form method="POST" action="{{ route('register.verifyCode') }}">
    @csrf
    <label for="email">Email:</label>
<input type="email" name="email" id="email" value="{{ session('email') }}" readonly>

<label for="name">Tên:</label>
<input type="text" name="name" id="name" value="{{ session('name') }}" readonly>

<label for="password">Mật khẩu:</label>
<input type="password" name="password" id="password" value="{{ session('password') }}" readonly>
<label for="password">Mã xác thực:</label>
    <input type="text" name="code" placeholder="Nhập mã xác thực" required>
    <button type="submit">Xác thực</button>
</form>
