<?php

include 'config.php';
 

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}
include 'header.php';

if(isset($_POST['order_btn'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);

   $address = mysqli_real_escape_string($conn, 'flat no. '. $_POST['flat']);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
      while($cart_item = mysqli_fetch_assoc($cart_query)){
         $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      }
   }

   $total_products = implode(', ',$cart_products);

   $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

   if($cart_total == 0){
      $message[] = 'your cart is empty';
   }else{
      if(mysqli_num_rows($order_query) > 0){
         $message[] = 'order already placed!'; 
      }else{
         mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
         $message[] = 'order placed successfully!';
         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      }
   }
   
}







?>
<!----========================Display Order Start=================----->
<section class="display-order">

   <?php  
   
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_cart) > 0){
         while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
   ?>
   <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo 'GH₵'.$fetch_cart['price'].'/-'.' x '. $fetch_cart['quantity']; ?>)</span> </p>
   <?php
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>

<?php
   $vat = (15/100 * $grand_total);
   $grand_total1 = $grand_total + $vat;
?>
   
   <div class="grand-total"> total : <span>GH₵<?php echo $grand_total ; ?></span> </div> 
   <div class="grand-total"> VAT : <span>GH₵<?php echo $vat = (1/10 * $grand_total); ?></span> </div>
   <div class="grand-total"> grand total : <span>GH₵<?php echo $grand_total1 ; ?></span> </div>     
</section>
<!----========================Display Order End=================----->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/payment.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Payment</title>
</head>
<body>
<section class="checkout">
    <form id="paymentForm" >
    <div class="flex">
    
        <div class="inputBox">
          <span>Email Address</span>
          <input type="email" id="email-address" name="email" required value="<?php echo $_SESSION['user_email']; ?>" />
        </div>


        <div class="inputBox">
            <span>Amount :</span>
            <input type="tel" name="number" id="amount" value="<?php echo isset($grand_total1) ? $grand_total1 : ''; ?>" readonly  >
         </div>

        <div class="inputBox">
            <span>your phone number :</span>
            <input type="number" name="number" required placeholder="enter your phone number" id="numberInput">
         </div>

       
         <div class="inputBox">
            <span>delivery address :</span>
            <input type="text" min="0" name="flat" required placeholder="Takoradi Technical University.">
         </div>
        
        

        
      </div>
<div class="form-submit">
          <button type="submit" onclick="payWithPaystack()"> Pay Now </button>
          <script>
            

          </script>
         
        </div>
      </form>
</section>
      


<script>

        // Limit the number of characters for the number input field
        document.getElementById('numberInput').addEventListener('input', function () {
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10); // Truncate to 10 characters
            }
        });
        //js files 
        /*
const paymentForm = document.getElementById('paymentForm');
paymentForm.addEventListener("submit", payWithPaystack, false);
function payWithPaystack() {
  e.preventDefault();

  let handler = PaystackPop.setup({
    key: 'pk_live_2db13dc69ff42532024b0696aec42358a614422c', // public key
    email: document.getElementById("email-address").value,
    amount: document.getElementById("amount").value * 100,
    currency: 'GHS',
    ref: ''+Math.floor((Math.random()* 1000000000)+1),
    onclose: function(){
      alert('Window closed.'); 
    },
   
    callback: function(response){
      let message = 'Payment complete! Reference: '+response.reference;
      alert(message);
      clearCart();
      window.location.href='shop.php';
    }
  });
  handler.openIframe();
  
  
}
function clearCart() {
  //Assuming you're using session storage to store cart items
  sessionStorage.removeItem('cart');
}
*/

    </script>
      
      <!-- custom js file link  -->
      <script src="js/script.js"></script>
         <script src="https://js.paystack.co/v1/inline.js"></script> 
            <script src="js/payment.js"></script>
    
      
      <?php include 'footer.php'; ?>

   </body>
</html>
