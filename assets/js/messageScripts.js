import $ from 'jquery';
import { Modal } from "bootstrap";

const availableMIMETypes = ['image/jpeg', 'image/png'];

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
    const $fileInput = $('#filesInput');
    if ($input.val() !== "" || $fileInput.val() !== "") {
        const formData = new FormData();
        formData.append("message", $input.val());
        if ($fileInput[0].files[0] && availableMIMETypes.includes($fileInput[0].files[0].type)) {
            formData.append("attachment", $fileInput[0].files[0], $fileInput[0].files[0].name);
        }

        $
            .ajax({
                url: window.location.href,
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
            })
            .done(function (data) {
                if (data['error']) {
                    alert(data['error']);
                } else {
                    setMessage(data);
                    scrollChatDown();
                }
            })
            .fail(function (event) {
                if (event.status === 403) {
                    alert('Access Restricted. You need to be a member of a chat to send messages!');
                }
            })
        ;
        $input.val("");
        $fileInput.val("");
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

    $('#closeAttachmentModal').click(function () {
        $('#filesInput').val("");
    });

    $('#attachFile').click(function () {
        const $input = $('#filesInput');
        if ($input[0].files[0]) {
            if (!availableMIMETypes.includes($('#filesInput')[0].files[0].type)) {
                $input.addClass('is-invalid')
            } else {
                $input.removeClass('is-invalid')
                $('#closeModalButton').click();
            }
        }
    });
});