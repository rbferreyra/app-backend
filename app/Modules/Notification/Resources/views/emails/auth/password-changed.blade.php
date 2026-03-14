@extends('notification::emails.layouts.base')

@section('content')
    <p>Hi {{ $name }},</p>
    <p>Your password has been changed successfully.</p>
    <table class="meta-table">
        <tr>
            <td>Date</td>
            <td>{{ $changed_at }}</td>
        </tr>
        <tr>
            <td>IP Address</td>
            <td>{{ $ip }}</td>
        </tr>
    </table>
    <p class="meta">If you did not make this change, please contact support immediately.</p>
@endsection
