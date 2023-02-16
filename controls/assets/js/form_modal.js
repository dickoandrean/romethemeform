function openmodal(uid, admin_url) {
    jQuery(document).ready(function ($) {
        var modal = $("#myModal" + uid);
        var selected = $('#' + uid + ' option:selected').val();
        var url = admin_url + 'post.php?post=' + selected + '&action=elementor';
        $('#ifr-' + uid).attr('src', url);
        modal.show();
    });
}

function closemodal(uid) {
    jQuery(document).ready(function ($) {
        var modal = $("#myModal" + uid);
        var iframe = window.parent.document.getElementById("ifr-" + uid);
        var elementorEditor = iframe.contentWindow.elementor;

        var panel = elementor.getPanelView();
        // Get the current selected widget
        var currentSelectedWidget = panel.getCurrentPageView().getOption('editedElementView');

        elementorEditor.saver.saveEditor({
            onSuccess: function () {
                modal.hide();
                currentSelectedWidget.renderOnChange();
                iframe.src = '';
            },
            onError: function () {
                alert("Error saving Form");
            }
        });
    });
}