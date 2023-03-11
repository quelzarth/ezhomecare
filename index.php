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
   <link rel="icon" type="image/x-icon" href="images/ezhomecare logo.png">
   <title>home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <div class="home-bg">

      <section class="home">

         <div class="swiper home-slider">

            <div class="swiper-wrapper">

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/nanyyonthego2.png" alt="">
                  </div>
                  <div class="content">
                     <span>The best babysitter package!</span>
                     <h3>Nanny on the go!</h3>
                     <a href="shop.php" class="btn">browse now</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/theloyal.png" alt="">
                  </div>
                  <div class="content">
                     <span>Best house cleaning service!</span>
                     <h3>'The Loyal'</h3>
                     <a href="shop.php" class="btn">browse now</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/wonderwoman.png" alt="">
                  </div>
                  <div class="content">
                     <span>Cleaning and laundry? we got you.</span>
                     <h3>The 'Wonderwoman'</h3>
                     <a href="shop.php" class="btn">browse now</a>
                  </div>
               </div>

            </div>

            <div class="swiper-pagination"></div>

         </div>

      </section>

   </div>

   <section class="home-products">

      <h1 class="heading">Services</h1>

      <div class="swiper products-slider">

         <div class="swiper-wrapper">

            <?php
            $sql = "SELECT * FROM employees INNER JOIN services ON employees.job = services.name WHERE employees.status = 'available' GROUP BY services.id;
            ";
            $result = $conn->query($sql);

            if ($result->rowCount() > 0) {
               while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                  <form action="checkout.php" method="post" class="swiper-slide slide">
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
                           <?= $row['price']; ?>
                        </div>
                     </div>
                     <input type="submit" value="Book Service" class="btn" name="add_to_cart">
                  </form>
                  <?php
               }
            } else {
               echo '<p class="empty">no services available yet!</p>';
            }
            ?>

         </div>

         <div class="swiper-pagination"></div>

      </div>

   </section>



   <?php include 'components/footer.php'; ?>

   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

   <script src="js/script.js"></script>

   <script>

      var swiper = new Swiper(".home-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
      });

      var swiper = new Swiper(".category-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            0: {
               slidesPerView: 2,
            },
            650: {
               slidesPerView: 3,
            },
            768: {
               slidesPerView: 4,
            },
            1024: {
               slidesPerView: 5,
            },
         },
      });

      var swiper = new Swiper(".products-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            550: {
               slidesPerView: 2,
            },
            768: {
               slidesPerView: 2,
            },
            1024: {
               slidesPerView: 3,
            },
         },
      });

   </script>

</body>

</html>