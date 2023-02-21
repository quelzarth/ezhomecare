<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
;

if (isset($_POST['submit'])) {

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // $conn = mysqli_connect("localhost", "root", "", "ezhomecare");
   // $sql = "SELECT * FROM users where email = '" . $email . "'";
   // $result = mysqli_query($conn, $sql);

   // if (mysqli_num_rows($result) == 0) {
   //    die("Email not found.");
   // }

   // $user = mysqli_fetch_object($result);
   // if (!password_verify($pass, $user->password)) {
   //    die("Password is not correct.");
   // }

   // if ($user->email_verified_at == null) {
   //    die("Please verify your email. <a href='email-verification.php?email=" . $email . "'>Click Here</a>");
   // }

   // header('location:index.php');

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if ($select_user->rowCount() > 0 && $row['email_verified_at'] == null) {
      $message[] = "Please enter code sent to your email. <a href='email-verification.php?email=" . $email . "'>Click Here</a>";
   }

   else if ($select_user->rowCount() > 0 && $row['email_verified_at'] != null) {
      header('location:index.php');
   } else {
      $message[] = 'account does not exist';
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="form-container">

      <form action="" method="post">
         <h3>login now</h3>
         <input type="email" name="email" required placeholder="enter your email" maxlength="50" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="pass" required placeholder="enter your password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="login now" class="btn" name="submit">
         <p>don't have an account?</p>
         <a href="user_register.php" class="option-btn">register now</a>
      </form>

   </section>













   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>