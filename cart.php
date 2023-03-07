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

if (isset($_POST['delete'])) {
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
}

if (isset($_GET['delete_all'])) {
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="products shopping-cart">

      <h3 class="heading">Cart</h3>

      <div class="box-container">
         <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
               ?>
               <form action="checkout.php" method="post" class="box">
                  <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                  <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
                  <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
                  <div class="name">
                     <?= $fetch_cart['name']; ?>
                  </div>
                  <div class="flex">
                     <div class="price">₱
                        <?= $fetch_cart['price']; ?>
                     </div>
                  </div>
                  <input type="submit" class="btn" value="Proceed to checkout">
                  <input type="submit" value="delete service" onclick="return confirm('delete this from bookings?');"
                     class="delete-btn" name="delete">
               </form>
               <?php
               $grand_total += $fetch_cart['price'];
            }
         } else {
            echo '<p class="empty">your cart is empty</p>';
         }
         ?>
      </div>

      <div class="cart-total">
         <p>grand total : <span>₱
               <?= $grand_total; ?>
            </span></p>
         <a href="shop.php" class="option-btn">continue Browsing</a>
         <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>"
            onclick="return confirm('delete all from bookings?');">Delete All Services</a>
         <!-- <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">proceed to checkout</a> -->
      </div>

   </section>













   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>