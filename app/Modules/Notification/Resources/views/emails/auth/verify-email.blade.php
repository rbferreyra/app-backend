@extends('notification::emails.layouts.base')

@section('content')
    <p>Hi {{ $name }},</p>
    <p>Thank you for registering. Please verify your email address by clicking the button below.</p>
    <p>This link expires in <strong>60 minutes</strong>.</p>
    <a href="{{ $url }}" class="btn">Verify Email Address</a>
    <hr class="divider">
    <p class="meta">If you did not create an account, no further action is required.</p>
    <p class="meta">If the button does not work, copy and paste this link: <br>{{ $url }}</p>
@endsection
