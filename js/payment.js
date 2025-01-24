function payWithPaystack(e) {
    e.preventDefault();

    let amount = document.getElementById("amount").value;
    if (!amount || amount <= 0) {
        alert("Please enter a valid amount.");
        return;
    }

    let handler = PaystackPop.setup({
        key: 'pk_live_2db13dc69ff42532024b0696aec42358a614422c', // Replace with your public test key
        email: document.getElementById("email-address").value,
        amount: amount * 100, // Amount in kobo
        currency: 'GHS', // Ghanaian Cedis
        ref: '' + Math.floor((Math.random() * 1000000000) + 1), // Generate a random reference number
        onClose: function () {
            alert('Payment window closed.');
        },
        callback: function (response) {
            if (response.status === 'success') {
                let message = 'Payment complete! Reference: ' + response.reference;
                alert(message);
                clearCart();
                window.location.href = 'shop.php'; // Redirect after successful payment
            } else {
                alert('Payment failed: ' + response.message);
            }
        },
        onError: function () {
            alert('Payment error occurred.');
        }
    });

    handler.openIframe();
}
