<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['update_payment'])) {
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
   $employee = $_POST['employee'];
   $employee = filter_var($employee, FILTER_SANITIZE_STRING);
   if ($payment_status == 'approved') {
      $update_payment = $conn->prepare("UPDATE `bookings` SET payment_status = ?, employee = ? WHERE id = ?");
      $update_payment->execute([$payment_status, $employee, $order_id]);
      $update_employee = $conn->prepare("UPDATE `employees` SET status = 'assigned' WHERE name = ?");
      $update_employee->execute([$employee]);
      $message[] = 'status updated!';
   } else if ($payment_status == 'completed') {
      $update_payment = $conn->prepare("UPDATE `bookings` SET payment_status = ?, employee = ? WHERE id = ?");
      $update_payment->execute([$payment_status, $employee, $order_id]);
      $update_employee = $conn->prepare("UPDATE `employees` SET status = 'available' WHERE name = ?");
      $update_employee->execute([$employee]);
      $message[] = 'status updated!';
   }

}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `bookings` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/' . $fetch_delete_image['proof_of_payment']);
   $delete_order = $conn->prepare("DELETE FROM `bookings` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>placed bookings</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="orders">

      <h1 class="heading">placed bookings</h1>

      <div class="box-container">

         <?php
         $select_orders = $conn->prepare("SELECT * FROM `bookings`");
         $select_orders->execute();
         if ($select_orders->rowCount() > 0) {
            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
               ?>
               <div class="box">
                  <p> placed on : <span>
                        <?= $fetch_orders['placed_on']; ?>
                     </span> </p>
                  <p> name : <span>
                        <?= $fetch_orders['name']; ?>
                     </span> </p>
                  <p> number : <span>
                        <?= $fetch_orders['number']; ?>
                     </span> </p>
                  <p> address : <span>
                        <?= $fetch_orders['address']; ?>
                     </span> </p>
                  <p> date of service : <span>
                        <?= $fetch_orders['service_date']; ?>
                     </span> </p>
                  <p> services : <span>
                        <?= $fetch_orders['total_products']; ?>
                     </span> </p>
                  <p> total price : <span>???
                        <?= $fetch_orders['total_price']; ?>
                     </span> </p>
                  <p> payment method : <span>
                        <?= $fetch_orders['method']; ?>
                     </span> </p>
                  <p>proof of payment : <img src="../uploaded_img/<?= $fetch_orders['proof_of_payment']; ?>" width="250"
                        height="auto"></p>
                  <form action="" method="post">
                     <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                     <select name="payment_status" class="select">
                        <option selected>
                           <?= $fetch_orders['payment_status']; ?>
                        </option>
                        <option value="pending">pending</option>
                        <option value="approved">approved</option>
                        <option value="completed">completed</option>
                     </select>
                     <p> assigned employee : <span>
                           <select name="employee" class="select" required>
                              <option selected>
                                 <?= $fetch_orders['employee']; ?>
                              </option>
                              <?php
                              $sql = "SELECT employees.name, bookings.total_products FROM employees INNER JOIN bookings ON employees.job = bookings.total_products WHERE employees.status = 'available' GROUP BY employees.id";
                              $stmt = $conn->prepare($sql);
                              $stmt->execute();
                              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                              if (count($result) > 0) {
                                 foreach ($result as $row) {
                                    echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                                 }
                              }
                              ?>
                           </select>
                        </span> </p>
                     <div class="flex-btn">
                        <input type="submit" value="update" class="option-btn" name="update_payment">
                        <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn"
                           onclick="return confirm('delete this order?');">delete</a>
                     </div>
                  </form>
               </div>
               <?php
            }
         } else {
            echo '<p class="empty">no bookings placed yet!</p>';
         }
         ?>

      </div>

   </section>

   </section>












   <script src="../js/admin_script.js"></script>

</body>

</html>