<h2>Login via WhatsApp</h2>

@if(session('error'))
<p style="color:red">{{ session('error') }}</p>
@endif

<form method="POST" action="/login-wa/request">
    @csrf
    <input type="text" name="phone_number" placeholder="Masukkan No WA">
    <button type="submit">Login</button>
</form>