$(document).ready(function () {
    $('.product-form').on('submit', function (e) {
        let isValid = true;
        let isEdit = $(this).data('edit') === true;

        // Clear errors
        $('.is-invalid').removeClass('is-invalid');
        $('.error-text').remove();

        const name = $('#name').val().trim();
        const price = $('#price').val().trim();
        const description = $('#description').val().trim();
        const image = $('#image').val();

        // Validate Name
        if (name === '') {
            $('#name').addClass('is-invalid')
                .after('<div class="error-text text-danger">Product name is required.</div>');
            isValid = false;
        }

        // Validate Price
        if (price === '' || isNaN(price) || parseFloat(price) <= 0) {
            $('#price').addClass('is-invalid')
                .after('<div class="error-text text-danger">Enter a valid price greater than 0.</div>');
            isValid = false;
        }

        // Validate Description
        if (description.length < 10) {
            $('#description').addClass('is-invalid')
                .after('<div class="error-text text-danger">Description must be at least 10 characters.</div>');
            isValid = false;
        }

        // Validate Image (required only on create)
       if (image !== '') {
            const ext = image.split('.').pop().toLowerCase();
            if (!['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                $('#image').addClass('is-invalid')
                    .after('<div class="error-text text-danger">Only JPG, PNG, and GIF files are allowed.</div>');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault(); // Stop form from submitting
        }
    });
});
