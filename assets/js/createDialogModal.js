import $ from 'jquery';

function addField(e) {
    const $newUserInput = $(this).parent().parent().clone();
    $newUserInput.find('select').val('');
    $newUserInput.find('#addField').on('click', addField);
    $newUserInput.find('#removeField').on('click', removeField);
    $(this).parent().parent().after($newUserInput);
}

function removeField(e) {
    $(this).parent().parent().slideUp().remove();
}

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

    $('#addField').on('click', addField);

    $('#removeField').on('click', removeField);
});