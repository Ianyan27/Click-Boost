<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Clickup Management System Registration</title>
    <link rel="stylesheet" href="{{ asset('css/login_page.css') }}">
</head>
<body>
    <div class="login-container" id="LoginCtn">
        <div class="left-side" id="LoginSide">
            <div class="left-side-inner-container">
                @if ($errors->any())
                    <div class="error-container">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="label-container">
                    <h2>User Login</h2>
                </div>
                <div class="inputs-container">
                    <form action=" {{ route('user.login') }}" method="POST">
                        @csrf
                        <div class="email-container">
                            <input type="text" placeholder="Email" name="email" required>
                        </div>
                        <div class="password-container">
                            <input type="password" placeholder="Password" name="password" required>
                        </div>
                        <div class="login-acc-btn-container">
                            <button type="submit">Login</button>
                        </div>
                        <div class="register-btn-container">
                            <p>Don't have an account?</p>
                            <button id="RegisterBtn">Register</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="left-side" id="RegisterSide">
            <div class="left-side-inner-container">
                <div class="label-container">
                    <h2>User Registration</h2>
                </div>
                <div>
                    <form action="{{ route('user.register') }}" method="POST">
                        @csrf
                        <div class="username-container">
                            <input type="text" placeholder="Username" name="username" required>
                        </div>
                        <div class="email-container">
                            <input type="email" placeholder="Email" name="email" required>
                        </div>
                        <div class="password-container">
                            <input type="password" placeholder="Password" name="password" required>
                        </div>
                        <div style="display:none;" class="password-container">
                            <input type="text" placeholder="Role" name="role" value="Admin" required>
                        </div>
                        <div class="register-acc-btn-container">
                            <button type="submit">Register</button>
                        </div>
                        <div class="login-btn-container">
                            <p>Already have an account?</p>
                            <button id="LoginBtn">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="right-side">
            <h2>Welcome to Click Boost</h2>
            <div>
                <img src=" {{ asset('images/Modern Minimalist Design Featuring Upward-Pointing Gradient Arrow.png') }} " alt="Logo">
            </div>
        </div>
    </div>
    <script>
        let registerBtn = document.getElementById('RegisterBtn');
        let loginBtn = document.getElementById('LoginBtn')
        let loginCtn = document.getElementById('LoginSide');
        let registerCtn = document.getElementById('RegisterSide');

        registerBtn.addEventListener('click', function(){
            loginCtn.style.display = 'none';
            registerCtn.style.display = 'block';
        })

        loginBtn.addEventListener('click', function(){
            registerCtn.style.display = 'none';
            loginCtn.style.display = 'block';
        })
    </script>
</body>
</html>
