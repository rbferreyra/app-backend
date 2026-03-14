@extends('notification::emails.layouts.alert')

@section('content')
    <p>Hi {{ $name }},</p>
    <p>The email address associated with your account has been changed.</p>
    <div class="alert-box">
        <p>If you did not make this change, your account may be compromised.</p>
    </div>
    <table class="meta-table">
        <tr>
            <td>Old email</td>
            <td>{{ $old_email }}</td>
        </tr>
        <tr>
            <td>New email</td>
            <td>{{ $new_email }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>{{ $changed_at }}</td>
        </tr>
    </table>
    <a href="{{ config('app.frontend_url') }}/support" class="btn">Contact Support</a>
@endsection
