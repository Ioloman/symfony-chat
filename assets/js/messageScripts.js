import $ from 'jquery';

function setMessage(messageHtml) {
    const $lastMessageBox = $('.media-chat').last();
    const $newMessageBox = $(messageHtml);

    if ($lastMessageBox.hasClass('media-chat-reverse')) {
        $lastMessageBox.find('.media-body').append($newMessageBox.find('.media-body>p'));
    } else {
        $lastMessageBox.after($newMessageBox);
    }
}

function sendMessageEventHandler(e) {
    e.preventDefault();
    const $input = $('#messageInput');
    if ($input.val() !== "") {
        $
            .ajax({
            url: window.location.href,
            method: 'POST',
            data: {message: $input.val()}
        })
            .done(function (data) {
                setMessage(data);
            })
        ;
        $input.val("");
    }
}

$(function () {
    $('#chatContent').scrollTop($('#chatBottom').offset().top);

    $('#messageInput').keypress(function (e) {
        if (e.which === 13) {
            sendMessageEventHandler(e);
        }
    });

    $('#sendMessageButton').click(function (e) {
        sendMessageEventHandler(e);
    });
});