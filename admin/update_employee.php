<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['update'])) {

    $pid = $_POST['pid'];
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $job = $_POST['job'];
    $job = filter_var($job, FILTER_SANITIZE_STRING);

    $update_product = $conn->prepare("UPDATE `employees` SET name = ?, job = ? WHERE id = ?");
    $update_product->execute([$name, $job, $pid]);

    $message[] = 'employee updated successfully!';

    $old_image_01 = $_POST['old_image_01'];
    $image_01 = $_FILES['image']['name'];
    $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
    $image_size_01 = $_FILES['image']['size'];
    $image_tmp_name_01 = $_FILES['image']['tmp_name'];
    $image_folder_01 = '../uploaded_img/' . $image_01;

    if (!empty($image_01)) {
        if ($image_size_01 > 2000000) {
            $message[] = 'image size is too large!';
        } else {
            $update_image_01 = $conn->prepare("UPDATE `employees` SET image = ? WHERE id = ?");
            $update_image_01->execute([$image_01, $pid]);
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            unlink('../uploaded_img/' . $old_image_01);
            $message[] = 'image updated successfully!';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update employee</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="update-product">

        <h1 class="heading">update employee</h1>

        <?php
        $update_id = $_GET['update'];
        $select_products = $conn->prepare("SELECT * FROM `employees` WHERE id = ?");
        $select_products->execute([$update_id]);
        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                    <input type="hidden" name="old_image_01" value="<?= $fetch_products['image']; ?>">
                    <div class="image-container">
                        <div class="main-image">
                            <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                        </div>
                    </div>
                    <span>update name</span>
                    <input type="text" name="name" required class="box" maxlength="100" placeholder="enter product name"
                        value="<?= $fetch_products['name']; ?>">
                    <span>update image 01</span>
                    <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
                    <span>update job</span>
                    <select name="job" class="box" required>
                        <?php
                        $select_products = $conn->prepare("SELECT * FROM `services`");
                        $select_products->execute();
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $fetch_products['name'] . "'>" . $fetch_products['name'] . "</option>";
                        }
                        ?>
                    </select>
                    <div class="flex-btn">
                        <input type="submit" name="update" class="btn" value="update">
                        <a href="register_employee.php" class="option-btn">go back</a>
                    </div>
                </form>

                <?php
            }
        } else {
            echo '<p class="empty">no product found!</p>';
        }
        ?>

    </section>












    <script src="../js/admin_script.js"></script>

</body>

</html>