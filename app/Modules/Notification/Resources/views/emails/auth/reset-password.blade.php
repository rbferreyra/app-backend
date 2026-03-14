@extends('notification::emails.layouts.base')

@section('content')
    <p>Hi {{ $name }},</p>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>This link expires in <strong>60 minutes</strong>.</p>
    <a href="{{ $url }}" class="btn">Reset Password</a>
    <hr class="divider">
    <p class="meta">If you did not request a password reset, no further action is required.</p>
    <p class="meta">If the button does not work, copy and paste this link: <br>{{ $url }}</p>
@endsection
