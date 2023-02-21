jQuery(document).ready(($) => {
    $('#formUpdate').on('show.bs.modal', (event) => {
        var edit_button = $(event.relatedTarget);
        var post_id = edit_button.data('form-id');
        var post_name = edit_button.data('form-name');
        var post_entry = edit_button.data('form-entry');
        var post_restricted = edit_button.data('form-restricted');
        var post_msg_succes = edit_button.data('form-msg-success');
        var modal = $('#formUpdate');
        modal.find('#form-name').val(post_name);
        modal.find('#id').val(post_id);
        modal.find('#entry-name').val(post_entry);
        modal.find('#success-message').val(post_msg_succes);;
        if (post_restricted === true) {
            modal.find('#switch').prop("checked", true)
        } else {
            modal.find('#switch').prop("checked", false)
        }
    });
});




function add_new_form() {
    const save_btn = document.getElementById('rform-save-button');
    save_btn.innerHTML = 'Saving...';
    save_btn.disabled = true;
    jQuery(($) => {
        data = $('#rtform-add-form').serialize();
        $.ajax({
            type: 'post',
            url: romethemeform_ajax_url.ajax_url,
            data: data,
            success: (response) => {
                location.href = response.data.url;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("The following error occured: " + textStatus, errorThrown);
            },
        });
    });
}

function update_form() {
    const save_btn = document.getElementById('rform-update-button');
    save_btn.innerHTML = 'Saving...';
    save_btn.disabled = true;
    jQuery(($) => {
        data = $('#rtform-update-form').serialize();
        console.log(data)
        $.ajax({
            type: 'post',
            url: romethemeform_ajax_url.ajax_url,
            data: data,
            success: (data) => {
                location.href = romethemeform_url.form_url;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("The following error occured: " + textStatus, errorThrown);
            },
        });
    });
}

function export_entries(form_id, form_name) {
    jQuery(document).ready(($) => {
        console.log(form_name + ' , ' + form_id);
        window.location.href = `${romethemeform_ajax_url.ajax_url}?action=export_entries&form_id=${form_id}&form_name=${form_name}`;
    });
}