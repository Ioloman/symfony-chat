import $ from 'jquery';

$(function () {
    $('#createChatroomButton').click(function (e) {
        e.preventDefault();
        const $form = $(this).parent().parent();
        if (isNaN(Number.parseInt($form.find('#selectUser').val()))) {
            $form.addClass('was-validated');
        } else {
            $form.removeClass('was-validated');

            $.ajax(
                {
                    url: $(this).data('controller-url'),
                    method: 'POST',
                    data: $form.serialize()
                }
            ).done(function (data) {
                if (data['status'] === 'success') {
                    window.location.href = data['url'];
                } else {
                    alert(data['message']);
                }
            }).fail(function () {
                alert('Internal Error!');
            })
            ;
            (new bootstrap.Modal(document.getElementById('newDialogModal'))).hide();
        }
    });
});