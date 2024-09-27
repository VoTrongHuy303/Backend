<!-- verify.blade.php -->
<form method="POST" action="{{ route('register.verifyCode') }}">
    @csrf
    <input type="hidden" name="email" value="{{ session('email') }}">
    <input type="hidden" name="name" value="{{ session('name') }}">
    <input type="hidden" name="password" value="{{ session('password') }}">
    <input type="text" name="code" placeholder="Nhập mã xác thực" required>
    <button type="submit">Xác thực</button>
</form>
