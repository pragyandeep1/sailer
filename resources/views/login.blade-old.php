<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Busfam Admin - Login</title>
    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="{{ asset('/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/sb-admin.css') }}">
</head>

<body class="intro-bg">
    <div class="auth-container">
        <div class="card">
            <div class="auth-content">
                <div class="auth-header">
                    <img src="{{ asset('/img/login_logo.webp') }}" alt="" class="img-fluid">
                </div>
                <h5>Hello! let's get started</h5>
                {{-- <p>Login to continue.</p> --}}
                {{ __('Login to continue.') }}

                <form method="POST" action="{{ route('login') }}" id="introForm" class="formular">
                    @csrf
                    <div class="mb-3">
                        <!-- <label for="username" class="form-label">Username</label> -->
                        <label for="email"
                            class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                        {{-- <input type="text" class="validate[required] text-input form-control" placeholder="Username"
                            name="user_name" id="user_name"> --}}
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <!-- <label for="password" class="form-label">Password</label> -->
                        <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                        {{-- <input type="password"
                            class="form-control validate[required] text-input login-field  login-field-password"
                            placeholder="Password" id="password-1" name="password"> --}}
                        <input id="password-1" type="password"
                            class="form-control text-input login-field  login-field-password @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        {{-- <input class="form-check-input" id="remember" type="checkbox">
                        <label for="remember">Remember me</label> --}}
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                        {{-- <a href="#" class="forgot-btn float-end">Forgot password?</a> --}}
                    </div>
                    <div class="mb-3 text-center">
                        {{-- <a href="index.php" type="submit"
                            class="btn btn-block btn-primary">Login</a> </div> --}}
                        <button type="submit" class="btn btn-block btn-primary">
                            {{ __('Login') }}
                        </button>
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                        <div class="mb-3 text-center">
                            Need an Account? <a href="register.php">Sign Up</a>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/js/sb-admin-2.js') }}"></script>
    <script src="{{ asset('/js/hideShowPassword.min.js') }}"></script>
    <script>
        $('#password-1').hidePassword(true);
        $('#password-2').showPassword('focus', {
            toggle: {
                className: 'my-toggle'
            }
        });
        $('#show-password').change(function() {
            $('#password-3').hideShowPassword($(this).prop('checked'));
        });
        jQuery(document).ready(function() {
            jQuery("#introForm").validationEngine('attach', {
                promptPosition: "bottomLeft",
                autoPositionUpdate: true
            });
        });
    </script>
</body>

</html>
