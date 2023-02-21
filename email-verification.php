<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}
;

if (isset($_POST['verify_email'])) {

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $verification_code = $_POST['verification_code'];
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND verification_code = ?");
    $select_user->execute([$email, $verification_code]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        $_SESSION['user_id'] = $row['id'];
        $update_user = $conn->prepare("UPDATE users SET email_verified_at = NOW() WHERE email = '" . $email . "' AND verification_code = '" . $verification_code . "'");
        $update_user->execute([]);
        header('location:index.php');
    } else {
        $message[] = 'incorrect verification code!';
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="form-container">

        <form action="" method="post">
            <h3>Enter Code</h3>
            <input type="email" name="email" required value="<?php echo $_GET['email']; ?>">
            <input type="text" name="verification_code" required placeholder="enter verification code" class="box">
            <input type="submit" value="Verify Email" class="btn" name="verify_email">
        </form>

    </section>













    <?php include 'components/footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>