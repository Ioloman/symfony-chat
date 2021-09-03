import $ from 'jquery';
import { Modal } from "bootstrap";

const availableMIMETypes = ['image/jpeg', 'image/png'];

function setMessage(messageHtml) {
    const $lastMessageBox = $('.media-chat').last();
    const $newMessageBox = $(messageHtml);
    $newMessageBox.find('img').on('load', scrollChatDown);

    if ($lastMessageBox.length === 0) {
        $('#chatContent').prepend($newMessageBox);
    } else {
        if ($lastMessageBox.hasClass('media-chat-reverse')) {
            $lastMessageBox.find('.media-body').append($newMessageBox.find('.media-body>p'));
        } else {
            $lastMessageBox.after($newMessageBox);
        }
    }
    convertYoutubeLinks();
}

const scrollChatDown = () => $('#chatContent').scrollTop($('#chatContent')[0].scrollHeight - $('#chatContent')[0].clientHeight);


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

/**
 *
 * @param {jQuery} $elem
 * @param {RegExp} regExp
 */
const processMessageWithLink = ($elem, regExp) => {
    if (window.localStorage.getItem('playerId') === null) {
        window.localStorage.setItem('playerId', 'player-1');
    }
    while ($elem.html().search(regExp) > -1) {
        /** @type Array */
        const matches = $elem.html().match(regExp);

        const playerId = window.localStorage.getItem('playerId');
        window.localStorage.setItem('playerId', playerId.split('-')[0] + '-' + (parseInt(playerId.split('-')[1]) + 1));

        $elem.html($elem.html().replace(matches[0], ''))
        $elem.after($(`<div><div id="${playerId}"></div></div>`));

        new YT.Player(playerId, {
            height: '200',
            width: '380',
            videoId: matches[1],
        })
    }
    if ($elem.html().trim().replaceAll('<br>', '') === "") {
        $elem.remove();
    }
}

const convertYoutubeLinks = () => {
    $('div.media-body>p').map((i, elem) => {
        const regExp = /http(?:s?):\/\/(?:www\.)?youtu(?:be\.com\/watch\?v=|\.be\/)([\w\-\_]*)(&(amp;)?‌​[\w\?‌​=]*)?/;
        const $elem = $(elem);
        if ($elem.html().search(regExp) > -1) {
            processMessageWithLink($elem, regExp);
        }
    });
}

export function onYouTubeIframeAPIReady() {
    convertYoutubeLinks();
}

$(function () {
    scrollChatDown();
    $('#chatContent').find('img').on('load', scrollChatDown);

    // const target = document.getElementById('chatContent')
    // // create an observer instance
    // const observer = new MutationObserver(function (mutations) {
    //     scrollChatDown();
    // });
    // // configuration of the observer:
    // const config = {childList: true, subtree: true};
    // observer.observe(target, config);

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