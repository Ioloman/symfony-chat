import $ from 'jquery';

$(function () {
    $('#createChatroomButton').click(function (e) {
        e.preventDefault();
        const $form = $(this).parent().parent();
        if (isNaN(Number.parseInt($form.find('#selectUser')))) {
            $form.addClass('was-validated');
        }
    });
});