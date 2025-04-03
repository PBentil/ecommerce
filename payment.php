<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:login.php');
}
include 'header.php';

$grand_total = 0;
$vat = 0;
$grand_total1 = 0;

$cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
if (mysqli_num_rows($cart_query) > 0) {
    while ($cart_item = mysqli_fetch_assoc($cart_query)) {
        $sub_total = $cart_item['price'] * $cart_item['quantity'];
        $grand_total += $sub_total;
    }
}

$vat = 0.15 * $grand_total; // 15% VAT
$grand_total1 = $grand_total + $vat;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/payment.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <title>Payment</title>
</head>
<body>
<style>
    .form-submit button {
        background-color: #28a745; /* Green color */
        color: white; /* Text color */
        padding: 15px 30px; /* Padding */
        font-size: 16px; /* Font size */
        border: none; /* No border */
        border-radius: 5px; /* Rounded corners */
        cursor: pointer; /* Pointer on hover */
        transition: background-color 0.3s ease; /* Smooth background transition */
    }

    .form-submit button:hover {
        background-color: #218838; /* Darker green on hover */
    }

    .form-submit button:focus {
        outline: none; /* Remove outline */
    }
</style>
<section class="checkout">
    <form id="paymentForm">
        <div class="flex">
            <div class="inputBox">
                <span>Email Address</span>
                <input type="email" id="email-address" name="email" required
                       value="<?php echo $_SESSION['user_email']; ?>" readonly />
            </div>

            <div class="inputBox">
                <span>Amount :</span>
                <input type="text" name="amount" id="amount" value="<?php echo $grand_total1; ?>" readonly>
            </div>

            <div class="inputBox">
                <span>Your Phone Number :</span>
                <input type="tel" name="number" required placeholder="Enter your phone number" id="numberInput">
            </div>

            <div class="inputBox">
                <span>Delivery Address :</span>
                <input type="text" name="address" required placeholder="Enter your delivery address">
            </div>
        </div>

        <div class="form-submit">
            <!-- Prevent default form submission and call payWithPaystack() -->
            <button type="button" onclick="payWithPaystack()">Pay Now</button>
        </div>
    </form>
</section>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    function payWithPaystack() {
        console.log("function has been triggered");

        let email = document.getElementById("email-address").value;
        let amount = document.getElementById("amount").value;
        let phone = document.getElementById("numberInput").value;

        if (!phone) {
            alert("Please enter your phone number.");
            return;
        }

        // Setup the Paystack payment handler
        let handler = PaystackPop.setup({
            key: 'pk_live_2db13dc69ff42532024b0696aec42358a614422c', // Replace with your Paystack Public Key
            email: email,
            amount: amount * 100, // Convert to kobo
            currency: 'GHS',
            ref: 'txn_' + Math.floor((Math.random() * 1000000000) + 1), // Unique transaction ref
            onClose: function () {
                alert('Payment window closed.');
            },
            callback: function (response) {
                // Redirect after successful payment
                window.location.href = `verify.html?reference=${response.reference}`;
            },
            onError: function () {
                alert('An error occurred while processing your payment.');
            }
        });

        // Open the Paystack inline iframe
        handler.openIframe();
    }
</script>

<?php include 'footer.php'; ?>

</body>
</html>
