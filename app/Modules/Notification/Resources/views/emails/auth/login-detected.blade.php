@extends('notification::emails.layouts.alert')

@section('content')
    <p>Hi {{ $name }},</p>
    <p>A new login was detected on your account.</p>
    <table class="meta-table">
        <tr>
            <td>Date</td>
            <td>{{ $logged_at }}</td>
        </tr>
        <tr>
            <td>Device</td>
            <td>{{ $device }}</td>
        </tr>
        <tr>
            <td>IP Address</td>
            <td>{{ $ip }}</td>
        </tr>
    </table>
    <div class="alert-box">
        <p>If this was not you, secure your account immediately.</p>
    </div>
    <a
        href="{{ config('app.frontend_url') }}/settings/devices"
        class="btn"
    >Review Devices</a>
@endsection
