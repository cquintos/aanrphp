@extends('layouts.app')

@section('content')
    <div class="bg-light p-5 rounded">
        <h1><b>Email Verification</b></h1>
        <br>    
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                A new verification link has been sent to your email address.
            </div>
        @endif
        
        <h2>Before proceeding, please check your email for a verification link. If you did not receive the email,</h2> 
        <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="d-inline btn btn-link p-0">
                <h2>click here to request another</h2>
            </button>.
        </form>
        
    </div>
@endsection