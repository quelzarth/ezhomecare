<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
;

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="products">

      <h1 class="heading">Latest Services</h1>

      <div class="box-container">

         <?php
         $sql = "SELECT * FROM employees INNER JOIN services ON employees.job = services.name WHERE employees.status = 'available' GROUP BY services.id;
         ";
         $result = $conn->query($sql);

         if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
               ?>
               <form action="checkout.php" method="post" class="box">
                  <input type="hidden" name="pid" value="<?= $row['id']; ?>">
                  <input type="hidden" name="name" value="<?= $row['name']; ?>">
                  <input type="hidden" name="price" value="<?= $row['price']; ?>">
                  <input type="hidden" name="image" value="<?= $row['image_01']; ?>">
                  <a href="quick_view.php?pid=<?= $row['id']; ?>" class="fas fa-eye"></a>
                  <img src="uploaded_img/<?= $row['image_01']; ?>" alt="">
                  <div class="name">
                     <?= $row['name']; ?>
                  </div>
                  <div class="flex">
                     <div class="price"><span>₱</span>
                        <?= $row['price']; ?><span></span>
                     </div>
                  </div>
                  <input type="submit" value="Book Service" class="btn" name="add_to_cart">
               </form>
               <?php
            }

         } else {
            echo '<p class="empty">no products found!</p>';
         }
         ?>

      </div>

   </section>













   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>