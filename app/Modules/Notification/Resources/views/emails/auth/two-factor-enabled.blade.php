@extends('notification::emails.layouts.base')

@section('content')
    <p>Hi {{ $name }},</p>
    <p>Two-factor authentication has been successfully enabled on your account.</p>
    <table class="meta-table">
        <tr>
            <td>Date</td>
            <td>{{ $enabled_at }}</td>
        </tr>
        <tr>
            <td>IP Address</td>
            <td>{{ $ip }}</td>
        </tr>
    </table>
    <p class="meta">If you did not enable 2FA, please contact support immediately.</p>
@endsection
