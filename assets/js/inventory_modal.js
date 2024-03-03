 // JavaScript code
 $(document).ready(function() {
    // Function to open the modal and populate it with data
    function openEditForm(id, price, stocks) {
        // Populate the modal fields with data
        $("#editPrice").val(price);
        $("#editStocks").val(stocks);
        $("#productId").val(id);

        // Show the modal
        $("#myModal").css("display", "block");
    }

    // Add a click event listener to the Edit buttons
        $(".edit_btn").click(function() {
            // Get the data attributes
            var id = $(this).data("id");
            var price = $(this).data("price");
            var stocks = $(this).data("stocks");
            
            // Call the function to open the modal with data
            openEditForm(id, price, stocks);
    });

    // Close the modal when the close button is clicked
    $(".close").click(function() {
        $("#myModal").css("display", "none");
    });
});