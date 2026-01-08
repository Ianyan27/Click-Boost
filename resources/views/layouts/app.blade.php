<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClickUp Dashboard</title>
    <link rel="stylesheet" href=" {{ asset('css/app.css') }}">
    <link rel="stylesheet" href=" {{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href=" {{ asset('css/clickup_contents.css') }} ">
</head>
<body>
    <div class="header">
        <div class="logo-name">
            <span class="app-name">Click Boost</span>
        </div>
        <div class="user-account">
            <i class="fa-solid fa-user"></i>
            <span class="user-name"> {{ session(('name')) }} </span>
        </div>
    </div>
    <div class="main-content">
        <div class="sidebar">
            <ul>
                <li>
                    <a href=" {{ route('dashboard') }} ">
                        <i class="fa-solid fa-house"></i><span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('clickup.spaces') }}">
                        <i class="fa-solid fa-shuttle-space"></i><span>Spaces</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('clickup.folders') }}">
                        <i class="fa-solid fa-folder"></i><span>Folder</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('clickup.lists') }}">
                        <i class="fa-solid fa-list"></i><span>Lists</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('clickup.tasks') }}">
                        <i class="fa-solid fa-list"></i><span>Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('clickup.members') }}">
                        <i class="fa-solid fa-user"></i><span>Members</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="">
                        <i class="fa-solid fa-gear"></i><span>Settings</span>
                    </a>
                </li>
                <li>
                    <form action=" {{ route('user.logout') }} " method="POST">
                        @csrf
                        <button class="logout-btn" type="submit">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i><span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        <div class="content">
            @yield('content')
        </div>
    </div>
    <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
</body>
</html>