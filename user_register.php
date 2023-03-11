<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
;

if (isset($_POST['submit'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);
   $mail = new PHPMailer(true);

   try {
      $mail->SMTPDebug = 0;
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'ezhomecareph@gmail.com';
      $mail->Password = 'hlwfuaqwejjcyhhd';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;
      $mail->setFrom('ezhomecareph@gmail.com', 'ezhomecare.great-site.net');
      $mail->addAddress($email, $name);
      $mail->isHTML(true);
      $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
      $mail->Subject = 'Email Verification';
      $mail->Body = '<p>Your verification code is: <b style="font-size: 30 px;">' .
         $verification_code . '</b></p>';
      $mail->send();


      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
      $select_user->execute([$email]);
      $row = $select_user->fetch(PDO::FETCH_ASSOC);

      if ($select_user->rowCount() > 0) {
         $message[] = 'email already exists!';
      } else {
         if ($pass != $cpass) {
            $message[] = 'confirm password not matched!';
         } else {
            $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password, verification_code, email_verified_at) VALUES(?,?,?,?,?)");
            $insert_user->execute([$name, $email, $cpass, $verification_code, NULL]);
            $message[] = 'Registered successfully. A one-time verification code was sent to your email. You may proceed to Login now.';
         }
      }
   } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: {$mail->ErrorInfo}';
   }


}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="form-container">

      <form action="" method="post">
         <h3>register now</h3>
         <h2>(all fields are required)</h2>
         <input type="text" name="name" required placeholder="enter username" maxlength="20" class="box">
         <input type="email" name="email" required placeholder="enter email" maxlength="50"
            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="pass" required placeholder="enter password"
            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
            title="Must contain at least one  number and one uppercase and lowercase letter, and at least 8 or more characters"
            maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="cpass" required placeholder="confirm your password (required)" maxlength="20"
            class="box" oninput="this.value = this.value.replace(/\s/g, '')">
         <p> By clicking Register Now, you agree with our
            <a href="tos.php" target="_blank">Terms of Service</a>
         <p>
            <input type="submit" value="register now" class="btn" name="submit">
         <p>already have an account?</p>
         <a href="user_login.php" class="option-btn">login now</a>
      </form>

   </section>













   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>