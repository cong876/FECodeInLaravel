<form method="POST" action="/auth/update">
    {!! csrf_field() !!}

    <div class="col-md-6">
        Mobile
        <input type="phone" name="mobile" value="{{ old('mobile') }}">
    </div>

    <div>
        Email
        <input type="email" name="email" value="{{ old('email') }}">
    </div>

    <div>
        wx_number
        <input type="text" name="wx_number" value="{{ old('wx_number') }}">
    </div>
    <div>
        SecurePassword
        <input type="password" name="secure_password">
    </div>

    <div class="col-md-6">
        Confirm Secure Password
        <input type="password" name="secure_password_confirmation">
    </div>

    <div>
        <button type="submit">Register</button>
    </div>
</form>