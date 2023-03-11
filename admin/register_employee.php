<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}
;

if (isset($_POST['add_product'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $job = $_POST['job'];
   $job = filter_var($job, FILTER_SANITIZE_STRING);

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   $select_employees = $conn->prepare("SELECT * FROM `employees` WHERE name = ?");
   $select_employees->execute([$name]);

   if ($select_employees->rowCount() > 0) {
      $message[] = 'employee already exist!';
   } else {

      $insert_products = $conn->prepare("INSERT INTO `employees`(name, image, job) VALUES(?,?,?)");
      $insert_products->execute([$name, $image_01, $job]);

      if ($insert_products) {
         if ($image_size_01 > 2000000) {
            $message[] = 'image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            $message[] = 'new employee added!';
         }

      }

   }

}
;

if (isset($_GET['delete'])) {

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `employees` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/' . $fetch_delete_image['image']);
   $delete_product = $conn->prepare("DELETE FROM `employees` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   header('location:register_employee.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>employees</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="add-products">

      <h1 class="heading">add employee</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <span>employee name (required)</span>
               <input type="text" class="box" required maxlength="100" placeholder="enter employee name" name="name">
            </div>
            <div class="inputBox">
               <span>image (required)</span>
               <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box"
                  required>
            </div>
            <div class="inputBox">
               <span>job (required)</span>
               <select name="job" class="box" required>
                  <?php
                  $select_products = $conn->prepare("SELECT * FROM `services`");
                  $select_products->execute();
                  while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                     echo "<option value='" . $fetch_products['name'] . "'>" . $fetch_products['name'] . "</option>";
                  }
                  ?>
               </select>
            </div>

            <input type="submit" value="add employee" class="btn" name="add_product">
      </form>

   </section>

   <section class="show-products">

      <h1 class="heading">Employees</h1>

      <div class="box-container">

         <?php
         $select_employees = $conn->prepare("SELECT * FROM `employees`");
         $select_employees->execute();
         if ($select_employees->rowCount() > 0) {
            while ($fetch_employees = $select_employees->fetch(PDO::FETCH_ASSOC)) {
               ?>
               <div class="box">
                  <img src="../uploaded_img/<?= $fetch_employees['image']; ?>" alt="">
                  <div class="name">
                     <span>Name:</span>
                     <?= $fetch_employees['name']; ?>
                  </div>
                  <div class="price"><span>
                        <span>Job:</span>
                        <?= $fetch_employees['job']; ?>
                     </span></div>
                  <div class="details"><span>
                        <span>Status:</span>
                        <?= $fetch_employees['status']; ?>
                     </span></div>
                  <div class="flex-btn">
                     <a href="update_employee.php?update=<?= $fetch_employees['id']; ?>" class="option-btn">update</a>
                     <a href="register_employee.php?delete=<?= $fetch_employees['id']; ?>" class="delete-btn"
                        onclick="return confirm('delete this employee?');">delete</a>
                  </div>
               </div>
               <?php
            }
         } else {
            echo '<p class="empty">no employees added yet!</p>';
         }
         ?>

      </div>

   </section>








   <script src="../js/admin_script.js"></script>

</body>

</html>