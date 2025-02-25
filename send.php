<?php
session_start();
include('connection.php');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;






//Load Composer's autoloader
require 'vendor/autoload.php'; 


function sendemail_verify($name,$email)
{
    $mail = new PHPMailer(true);
    $mail->SMTPAuth   = true;  
    
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->Username   = 'premsaipradhan10@gmail.com';                     //SMTP username
    $mail->Password   = 'ypgt zofl cwwj uipf';                               //SMTP password

    $mail->SMTPSecure = "tls";            
    $mail->Port       = 587;              

    //Recipients
    $mail->setFrom('premsaipradhan10@gmail.com', 'Zombie');
    $mail->addAddress($email);     

    $mail->isHTML(true);                                 
    $mail->Subject = 'Pyaar Tumse Hi Karenge';
    $mail->Body    = "तुम नहीं होते हो तो बहुत खलता है, प्यार कितना है तुमसे पता चलता है<br>
    Thank U For Registering To Hostel Ease<br>
    Now U can Experience The potential Of This Webiste <br>";
     
    $mail->send();
    echo 'Message has been sent';

    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


if (isset($_POST['register_btn']))
{
    $name = $_POST['name'];
    $gender = $_POST['gender']; 
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $roll_no = $_POST['roll_no'];
    $hostel_no=$_POST['hostel_no'];
    $role = 'user';
    
    
    $check_email = "SELECT email_id FROM users where email_id='$email'";
    $check_email_run= mysqli_query($conn,$check_email);

    if(mysqli_num_rows($check_email_run) > 0){
        $_SESSION['status'] = "Email already exists";
        header("Location:Register.php");


     }
 
 
    else{
        $query = "INSERT INTO users (roll_no,name,gender,hostel_no,email_id,password,role) VALUES ('$roll_no','$name','$gender','$hostel_no', '$email', '$password','$role')";
        $query_run = mysqli_query($conn, $query);
        if($query_run)
         {
            sendemail_verify("$name","$email",);
            $_SESSION['status']="registraion successfull verify your email";
            header("Location:Register.php");

        } else {
            $_SESSION['status'] = "Registration failed";
            header("Location:Register.php");
        }
    }
}

    

    

?>
