
function payWithPaystack(e) {
    e.preventDefault();

    let amount = document.getElementById("amount").value;
    let email = document.getElementById("email-address").value;

    if (!amount || amount <= 0) {
        alert("Please enter a valid amount.");
        return;
    }
    if (!email) {
        alert("Please enter your email.");
        return;
    }

    let handler = PaystackPop.setup({
        key: 'pk_live_2db13dc69ff42532024b0696aec42358a614422c',
        email: email,
        amount: amount * 100,
        currency: 'GHS', // Ghanaian Cedis
        ref: 'txn_' + Math.floor((Math.random() * 1000000000) + 1),
        onClose: function () {
            alert('Payment window closed.');
        },
        callback: function (response) {
            // Redirect to verify payment (you can also send this to an API)
            window.location.href = `verify.html?reference=${response.reference}`;
        },
        onError: function () {
            alert('An error occurred while processing your payment.');
        }
    });

    handler.openIframe();
}
