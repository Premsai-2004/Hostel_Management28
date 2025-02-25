<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HostelEase</title>
    
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    overflow: hidden;
}

.welcome-screen {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(135deg, #ff9a9e, #fad0c4);
    transition: opacity 1s ease;
    opacity: 1;
}

.welcome-screen h1 {
    font-size: 3rem;
    color: #ffffff;
    animation: slideIn 1s forwards;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

#enter-button {
    padding: 10px 20px;
    border: none;
    background-color: #ff6a88;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    animation: buttonHover 1s infinite alternate;
}

@keyframes buttonHover {
    from {
        transform: scale(1);
    }
    to {
        transform: scale(1.1);
    }
}

.navbar {
    background-color: #333;
    display: flex;
    justify-content: space-between;
    padding: 15px 20px;
    position: sticky;
    top: 0;
    transition: all 0.5s;
}

.logo {
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
}

.nav-links {
    list-style: none;
    display: flex;
}

.nav-button {
    background: #ff6a88;
    color: white;
    border: none;
    padding: 10px 15px;
    margin-left: 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
}

.nav-button:hover {
    background-color: #ff4c72;
    transform: scale(1.1);
}

.content {
    display: none;
    padding: 20px;
    text-align: center;
    color: #333;
}

body.active .welcome-screen {
    opacity: 0;
    transition: opacity 1s ease;
}

body.active .content {
    display: block;
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
        </style>
</head>
<body>
    <div class="welcome-screen" id="welcome-screen">
        <h1>Welcome to HostelEase</h1>
        <button id="enter-button">Enter</button>
    </div>

    <nav class="navbar">
        <div class="logo">HostelEase</div>
        <ul class="nav-links">
            <li><button class="nav-button" id="login-button">Login</button></li>
            <li><button class="nav-button" id="register-button">Register</button></li>
        </ul>
    </nav>

    <div class="content">
        <h2>Explore Your New Home!</h2>
        <p>Find the perfect hostel for your needs.</p>
    </div>

    <script >
        document.getElementById('enter-button').addEventListener('click', function() {
    document.body.classList.add('active');
});
    </script>
</body>
</html>