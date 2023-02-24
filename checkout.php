<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:user_login.php');
}
;

if (isset($_POST['order'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ' - ' . $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $service_date = $_POST['service_date'];
   $service_date = filter_var($service_date, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $proof_of_payment = $_FILES['proof_of_payment']['name'];
   $proof_of_payment = filter_var($proof_of_payment, FILTER_SANITIZE_STRING);
   $proof_of_payment_size = $_FILES['proof_of_payment']['size'];
   $proof_of_payment_tmp_name = $_FILES['proof_of_payment']['tmp_name'];
   $proof_of_payment_folder = 'uploaded_img/'.$proof_of_payment;

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if ($check_cart->rowCount() > 0) {

      $insert_order = $conn->prepare("INSERT INTO `bookings`(user_id, name, number, email, method, address, service_date, total_products, total_price, proof_of_payment) VALUES(?,?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $service_date, $total_products, $total_price, $proof_of_payment]);

      if($insert_order){
         if($proof_of_payment_size > 2000000){
            $message[] = 'image size is too large!';
         }else{
            move_uploaded_file($proof_of_payment_tmp_name, $proof_of_payment_folder);
         }

      }

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'order placed successfully!';
   } else {
      $message[] = 'your cart is empty';
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="checkout-orders">

      <form action="" method="POST" enctype="multipart/form-data">

         <h3>Your Services</h3>

         <div class="display-orders">
            <?php
            $grand_total = 0;
            $cart_items[] = '';
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $cart_items[] = $fetch_cart['name'] . ' (' . $fetch_cart['price'] . ') - ';
                  $total_products = implode($cart_items);
                  $grand_total += ($fetch_cart['price']);
                  ?>
                  <p>
                     <?= $fetch_cart['name']; ?> <span>(
                        <?='₱' . $fetch_cart['price']; ?>)
                     </span>
                  </p>
                  <?php
               }
            } else {
               echo '<p class="empty">your services is empty!</p>';
            }
            ?>
            <input type="hidden" name="total_products" value="<?= $total_products; ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
            <div class="grand-total">grand total : <span>₱
                  <?= $grand_total; ?>
               </span></div>
         </div>

         <h3>Place your Bookings</h3>

         <div class="flex">
            <div class="inputBox">
               <span>Your Name :</span>
               <input type="text" name="name" placeholder="enter your name" class="box" maxlength="20" required>
            </div>
            <div class="inputBox">
               <span>Your Number :</span>
               <input type="number" name="number" placeholder="enter your number" class="box" min="0" max="9999999999"
                  onkeypress="if(this.value.length == 11) return false;" required>
            </div>
            <div class="inputBox">
               <span>Your Email :</span>
               <input type="email" name="email" placeholder="enter your email" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>Payment Method :</span>
               <select name="method" class="box" required>
                  <option value="gcash">GCash</option>
                  <option value="bank transfer">Bank Transfer</option>
               </select>
            </div>
            <div class="inputBox">
               <span>Address Line 01 :</span>
               <input type="text" name="flat" placeholder="e.g. Blk 3, Lot 8, Bading Subd." class="box" maxlength="50"
                  required>
            </div>
            <div class="inputBox">
               <span>Address Line 02 :</span>
               <input type="text" name="street" placeholder="e.g. Baliko Street" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>City :</span>
               <select name="city" class="box" required>
                  <option value="Tungko, Bulacan">Tungko, Bulacan</option>
                  <option value="Quezon">Quezon City</option>
                  <option value="Makati">Makati</option>
                  <option value="Manila">Manila</option>
               </select>
            </div>
            <div class="inputBox">
               <span>Zip Code :</span>
               <input type="number" min="0" name="pin_code" placeholder="e.g. 3032" min="0" max="999999"
                  onkeypress="if(this.value.length == 4) return false;" class="box" required>
            </div>
            <div class="inputBox">
               <span>Date of Service :</span>
               <input type="date" name="service_date" class="box" min="<?php echo date("Y-m-d"); ?>" required>
            </div>
            <div class="inputBox">
            </div>
            <div class="inputBox">
               <span><b>*READ BEFORE PROCEEDING*</b></span>
            </div>
            <div class="inputBox">
            </div>
            <div class="inputBox">
               <span>*A downpayment of 50% of total price should be paid upon booking with <b>GCash</b> or <b>Bank
                     Transfer</b></span>
            </div>
            <div class="inputBox">
               <span>*The remaining 50% of total price will be paid upfront upon staff arrival</span>
            </div>
            <div class="inputBox">
               <span><b>GCash QR Code :</b> </span>
               <img src="images/gcash.jpg" alt="" width="400" height="auto">
            </div>
            <div class="inputBox">
               <span><b>Bank Transfer Details :</b> </span>
               </br> </br>
               <span><b>Account Name:</b> Gerlie Mariel Fernandez </br>
                  <b>Account:</b> 001271647735 </br>
                  <b>Bank:</b></br>BDO UNIBANK, INC. </br>
                  7899 </br>
                  BDO CORPORATE CENTER </br>
                  CITY OF MAKATI Philippines </br>
                  <b>SWIFT/BIC:</b> BNORPHMM</span>
            </div>
            <div class="inputBox">
               <span>Proof of Payment (50% Downpayment)</span>
               <input type="file" name="proof_of_payment" accept="image/jpg, image/jpeg, image/png, image/webp"
                  class="box" required>
            </div>
         </div>
         </br>
         <input type="submit" name="order" class="btn <?=($grand_total > 1) ? '' : 'disabled'; ?>"
            value="place Booking">

      </form>

   </section>



   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>