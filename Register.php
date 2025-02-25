<?php
    session_start();
    // Display session message if it exists
    if (isset($_SESSION['status'])) {
        echo '<div class="popup-success">
                <span class="emoji">🎉</span>
                ' . $_SESSION['status'] . '
                <span>Registration Successful!</span>
              </div>';
        unset($_SESSION['status']); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap');        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family:'Noto Serif', serif;
        }
        body {
            
            background: url('303031.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        

        .container {
            text-align: center;
        }
        form .field i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #007bff; 
    }
    form {
    background: rgba(255, 255, 255, 0);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    
}



        form .field {
            position: relative;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #fff;
            font-size: 14px;
        }
        form input, form select, form button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: box-shadow 0.3s;
            padding-left: 35px;
        }
        form input:hover, form select:hover {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }
        form input {
            background: rgba(255, 255, 255, 0.5);
            border: none;
        }
        form select {
            background: rgba(255, 255, 255, 0.5);
            border: none;
        }
        form .field i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #555;
        }
        .error {
            color: red;
            font-size: 12px;
            text-align: left;
            grid-column: span 2;
            margin-top: -15px;
        }
        form button {
            grid-column: span 2;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .password-wrapper {
        position: relative;
        width: 100%;
        grid-column: span 2; 
    }
    
    .password-wrapper input {
        padding-right: 40px;
    }
    .password-wrapper .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 18px;
        color: #007bff; 
    }
        .popup-success {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
            z-index: 1000;
            animation: sparkle 1.5s forwards;
        }
        @keyframes sparkle {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        .popup-success span {
            display: block;
            font-size: 24px;
            color: #4caf50;
            margin-top: 10px;
        }
        .popup-success span.emoji {
            font-size: 36px;
        }
        .login-button {
            grid-column: span 2;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
    <div>
                    <h1 class="logo-badge text-whitesmoke" style="font-size:80px;color:#e6e6e6"><span class="fa fa-user-circle"></span></h1>
                </div>
        <form id="registrationForm" action="send.php" method="POST">
            <div class="field">
                
                <i class="fas fa-user" style="color: #ff6347;"></i>
                <input type="text" id="name" name="name" required placeholder="Name">
                <div class="error" id="nameError"></div>
            </div>
            <div class="field">
               
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <div class="error" id="genderError"></div>
            </div>
            <div class="field">
               
                <i class="fas fa-id-card" style="color: #ff6347;"></i>
                <input type="text" id="roll_no" name="roll_no" placeholder="e.g., 23CSE101" required placeholder="roll_no">
                <div class="error" id="rollNoError"></div>
            </div>
            <div class="field">
               
                <i class="fas fa-home" style="color: #ff6347;"></i>
                <input type="number" id="hostel_no" name="hostel_no" required placeholder="hostel_no">
                <div class="error" id="hostelNoError"></div>
            </div>
            <div class="field">
               
                <i class="fas fa-phone" style="color: #ff6347;"></i>
                <input type="text" id="phone" name="phone" required placeholder="phone">
                <div class="error" id="phoneError"></div>
            </div>
            <div class="field">
               
                <i class="fas fa-envelope" style="color: #ff6347;"></i>
                <input type="email" id="email" name="email" required placehoder="email">
                <div class="error" id="emailError"></div>
            </div>
            <div class="field password-wrapper">
               
                <i class="fas fa-lock" style="color: #ff6347;"></i>
                <input type="password" id="password" name="password" required placeholder="password">
                <i class="toggle-password" id="togglePassword">👁️</i>
                <div class="error" id="passwordError"></div>
            </div>
            <button type="submit" name="register_btn">Register</button>
            <button type="button" class="login-button" onclick="window.location.href='login.php';">Login</button>
        </form>
    </div>

    <script>
       // Show password toggle
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    const type = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = type;
    this.textContent = type === 'password' ? '👁️' : '🙈';
});

// Show popup on page load
window.addEventListener('load', function () {
    const popup = document.querySelector('.popup-success');
    if (popup) {
        popup.style.display = 'block';
        setTimeout(() => {
            popup.style.display = 'none';
        }, 5000);
    }
});

// Validation function
function validateField(input, regex, errorMsg, errorElement) {
    const value = input.value.trim();
    if (!regex.test(value)) {
        errorElement.textContent = errorMsg;
    } else {
        errorElement.textContent = '';
    }
}

// Adding blur event listeners for real-time validation
document.getElementById('name').addEventListener('blur', function () {
    validateField(this, /.+/, 'Name is required.', document.getElementById('nameError'));
});

document.getElementById('gender').addEventListener('blur', function () {
    if (this.value === '') {
        document.getElementById('genderError').textContent = 'Please select a gender.';
    } else {
        document.getElementById('genderError').textContent = '';
    }
});

document.getElementById('roll_no').addEventListener('blur', function () {
    validateField(this, /^\d{2}[A-Z]{3}\d{3}$/, 'Roll number must be in the format yyBranchInteger (e.g., 23CSE101).', document.getElementById('rollNoError'));
});

document.getElementById('hostel_no').addEventListener('blur', function () {
    if (this.value <= 0 || this.value === '') {
        document.getElementById('hostelNoError').textContent = 'Hostel number must be a positive number.';
    } else {
        document.getElementById('hostelNoError').textContent = '';
    }
});

document.getElementById('phone').addEventListener('blur', function () {
    validateField(this, /^[6-9]\d{9}$/, 'Please enter a valid phone number.', document.getElementById('phoneError'));
});

document.getElementById('email').addEventListener('blur', function () {
    validateField(this, /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'Please enter a valid email address.', document.getElementById('emailError'));
});

document.getElementById('password').addEventListener('blur', function () {
    validateField(this, /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+={}\[\]|\\:;\"'<>,.?/-]{8,}$/, 
        'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.', 
        document.getElementById('passwordError')
    );
});

// Validate on form submission
document.getElementById('registrationForm').addEventListener('submit', function (e) {
    let isValid = true;
    document.querySelectorAll('.error').forEach(error => error.textContent = '');

    const name = document.getElementById('name').value.trim();
    if (name === '') {
        document.getElementById('nameError').textContent = 'Name is required.';
        isValid = false;
    }

    const gender = document.getElementById('gender').value;
    if (gender === '') {
        document.getElementById('genderError').textContent = 'Please select a gender.';
        isValid = false;
    }

    const rollNo = document.getElementById('roll_no').value.trim();
    if (!/^\d{2}[A-Z]{3}\d{3}$/.test(rollNo)) {
        document.getElementById('rollNoError').textContent = 'Roll number must be in the format yyBranchInteger (e.g., 23CSE101).';
        isValid = false;
    }

    const hostelNo = document.getElementById('hostel_no').value;
    if (hostelNo <= 0 || hostelNo === '') {
        document.getElementById('hostelNoError').textContent = 'Hostel number must be a positive number.';
        isValid = false;
    }

    const phone = document.getElementById('phone').value.trim();
    if (!/^[6-9]\d{9}$/.test(phone)) {
        document.getElementById('phoneError').textContent = 'Please enter a valid phone number.';
        isValid = false;
    }

    const email = document.getElementById('email').value;
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('emailError').textContent = 'Please enter a valid email address.';
        isValid = false;
    }

    const password = document.getElementById('password').value;
    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+={}\[\]|\\:;\"'<>,.?/-]{8,}$/.test(password)) {
        document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.';
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});
</script>
</body>
</html>
