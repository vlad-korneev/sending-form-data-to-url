(function ($) {
    $('.form-sfdtu').on('submit', function (e) {
        e.preventDefault();
        let this_form = $(this),
            message_block = $('.sfdtu-message');
        $('input[type=submit]', this_form).prop( 'disabled', true );
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {
                if (data.status === true) {
                    this_form.remove();
                    message_block.html(data.message);
                } else {
                    if (data.fields.length !== 0) {
                        this_form.find('input').each(function() {
                            $(this).css({'border': ''});
                        });
                        $.each(data.fields, function(index, value) {
                            this_form.find('[name="'+value+'"]').css({'border': '1px solid red'});
                        });
                    }
                    message_block.html('failed to send');
                    $('input[type=submit]', this_form).prop( 'disabled', false );
                    console.log(data.error);
                }
            }
        });
    });
}(jQuery));