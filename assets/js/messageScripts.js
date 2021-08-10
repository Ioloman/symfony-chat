import $ from 'jquery';

function setMessage(messageHtml) {
    const $lastMessageBox = $('.media-chat').last();
    const $newMessageBox = $(messageHtml);

    if ($lastMessageBox.length === 0) {
        $('#chatContent').prepend($newMessageBox);
    } else {
        if ($lastMessageBox.hasClass('media-chat-reverse')) {
            $lastMessageBox.find('.media-body').append($newMessageBox.find('.media-body>p'));
        } else {
            $lastMessageBox.after($newMessageBox);
        }
    }
}

const scrollChatDown = () => $('#chatContent').scrollTop($('#chatBottom').offset().top);


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
                scrollChatDown();
            })
        ;
        $input.val("");
    }
}

$(function () {
    scrollChatDown();

    $('#messageInput').keypress(function (e) {
        if (e.which === 13) {
            sendMessageEventHandler(e);
        }
    });

    $('#sendMessageButton').click(function (e) {
        sendMessageEventHandler(e);
    });
});