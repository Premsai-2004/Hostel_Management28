<!DOCTYPE html>
<html lang="en">
<head>
    <title>Webpage Design</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="main">
        <div class="navbar">
            <div class="icon">
                <h2 class="logo">HostelEase</h2>
            </div>

            <div class="menu">
                <ul>
                    <li><a href="#">HOME</a></li>
                    <li><a href="#">ABOUT</a></li>
                    <li><a href="#">SERVICE</a></li>
                    <li><a href="#">DESIGN</a></li>
                    <li><a href="#">CONTACT</a></li>
                </ul>
            </div>
        </div> 

        <div class="content">
            <h1>Experience<br><span>a Seamless</span> <br>Stay</h1>
            <p class="par">
                Ensure your hostel remains compliant with all regulations and standards.
                HostelEase simplifies <br>compliance management by providing a centralized platform for tracking inspections. 
                <br>You can streamline your focus on providing a safe and welcoming environment for your guests.
            </p>

            <button class="cn"><a href="#">JOIN US</a></button>

            
            <div class="form">
                <h2>Login Here</h2>

                
                <?php if (isset($error_message)): ?>
                    <div class="error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form action="loginsend.php" method="POST">
                    <input type="email" id="email" name="email" placeholder="Enter Email Here" required>
                    <input type="password" id="password" name="password" placeholder="Enter Password Here" required>
                    <button type="submit" class="btnn">Login</button>
                </form>

                <p class="link">Don't have an account?<br>
                <a href="register.php">Sign up here</a></p>

                
            </div>
        </div>
    </div>

   
</body>
</html>
