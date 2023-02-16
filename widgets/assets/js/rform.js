jQuery(document).ready(($) => {
    $('.rform-button-submit').click(function (event) {
        event.preventDefault();
        var form = $(this).closest("form");
        var form_id = form.attr('data-form');
        if (form.hasClass('rform-dsb')) {
            var m = form.find('.require-login');
            m.css('display', 'block');
        } else {
            if (form.get(0).checkValidity()) {
                if (form.find("[aria-invalid= true]").length == 0) {
                    console.log('Form Valid');

                    var loading = form.find('.loading');
                    loading.css('display' , 'flex');
                    $(this).prop('disabled', true);
                    var data = form.serialize();
                    var inputs = data.split("&");
                    var serializedInputs = {};
                    for (var i = 0; i < inputs.length; i++) {
                        var keyValue = inputs[i].split("=");
                        serializedInputs[keyValue[0]] = decodeURIComponent(keyValue[1]);
                    }

                    var jsonString = JSON.stringify(serializedInputs);
                    var data_sending = { action: "rformsendform", id: form_id, data: jsonString };
                    sending_form(data_sending, $(this), loading);
                } else {
                    form.find(":invalid").each(function () {
                        $(this).attr("aria-invalid", "true");
                    });
                }
            } else {
                form.find(":invalid").each(function () {
                    $(this).attr("aria-invalid", "true");
                });
            }
        }
    });
    $('.close-msg').click(function (event) {
        event.preventDefault();
        var msg = $(this).closest('.msg');
        msg.css('display', 'none');
    });
});


function sending_form(data, btn , loading) {
    jQuery(document).ready(($) => {
        $.ajax({
            type: 'post',
            url: romethemeform_ajax_url.ajax_url,
            data: data,
            success: (e) => {
                btn.prop('disabled', false);
                btn.closest('form').find('.success-submit').css('display', 'block');
                loading.css('display' , 'none');
                btn.closest('form')[0].reset();
            }
        });

    });
}