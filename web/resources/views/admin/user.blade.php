@extends('layouts.admin')

@section('title', 'Data Pengguna')

@section('content')

<div class="user-container">

    <!-- SEARCH -->
    <div class="search-box">
        <input type="text" placeholder="Search">
        <button><i class="fa fa-search"></i></button>
    </div>

    <!-- TABLE -->
    <table class="user-table">
        <thead>
            <tr>
                <th><i class="fa fa-user"></i></th>
                <th>Nama Pengguna</th>
                <th>Email</th>
                <th>Whatsapp</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Budi Pratomo</td>
                <td>budiprtmo34@gmail.com</td>
                <td>085876542319</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Siti Hartini</td>
                <td>sitih4rtini@gmail.com</td>
                <td>085476549315</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Andi Santoso</td>
                <td>sansand@gmail.com</td>
                <td>085476549318</td>
            </tr>
        </tbody>
    </table>

</div>

@endsection