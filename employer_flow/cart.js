function addToCart(candidateId, price) {
    $.ajax({
        url: 'add_to_cart.php',
        type: 'POST',
        data: {
            candidate_id: candidateId,
            price: price
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                updateCartCount();
                alert('Candidate added to cart!');
            } else {
                alert('Error adding to cart: ' + data.message);
            }
        }
    });
}

function updateCartCount() {
    $.ajax({
        url: 'get_cart_count.php',
        type: 'GET',
        success: function(response) {
            const data = JSON.parse(response);
            $('#cart-count').text(data.count);
        }
    });
}