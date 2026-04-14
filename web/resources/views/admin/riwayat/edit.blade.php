<link rel="stylesheet" href="{{ asset('css/dashboard/admin-style.css') }}">

<h2>Edit Riwayat</h2>

<div class="form-container">
<form method="POST" action="/admin/riwayat/update/{{ $data->id }}">
    @csrf

    <label>Judul</label>
    <input type="text" name="judul" value="{{ $data->judul }}" required>

    <label>Status</label>
    <select name="status">
        <option value="hoax" {{ $data->status == 'hoax' ? 'selected' : '' }}>Hoax</option>
        <option value="fakta" {{ $data->status == 'fakta' ? 'selected' : '' }}>Fakta</option>
    </select>

    <button type="submit">Simpan</button>
</form>
</div>