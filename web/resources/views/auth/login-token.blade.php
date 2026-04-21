<h2>Masukkan Token</h2>

@if(session('success'))
<p style="color:green">{{ session('success') }}</p>
@endif

@if(session('error'))
<p style="color:red">{{ session('error') }}</p>
@endif

<form method="POST" action="/login-wa/verify">
    @csrf
    <input type="text" name="token" placeholder="Masukkan Token">
    <button type="submit">Verifikasi</button>
</form>