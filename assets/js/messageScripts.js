import $ from 'jquery';

const availableMIMETypes = ['image/jpeg', 'image/png'];

function setMessage(messageHtml) {
    const $lastMessageBox = $('.media-chat').last();
    const $newMessageBox = $(messageHtml);
    $newMessageBox.find('img').on('load', scrollChatDown);
    $newMessageBox.find('i.delete-icon').on('click', deleteMessage);

    if ($lastMessageBox.length === 0) {
        $('#chatContent').prepend($newMessageBox);
    } else {
        if ($lastMessageBox.hasClass('media-chat-reverse')) {
            $lastMessageBox.find('.media-body').append($newMessageBox.find('.media-body>div'));
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

function deleteMessage() {
    const $messageBox = $(this).parent().parent();
    $.ajax({
        url: window.location.href + '/message/' + $messageBox.data('id'),
        method: 'DELETE',
    }).done(function(response) {
        if (response['status'] === 'success') {
            if ($messageBox.siblings().length > 0) {
                $messageBox.remove();
            } else {
                $messageBox.closest('.media-chat').remove();
            }
        } else {
            alert(response['message']);
        }
    }).fail(function() {
        alert('Server Error!')
    })
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

        const player = new YT.Player(playerId, {
            height: '200',
            width: '380',
            videoId: matches[1],
        })
        const $iframe = $(player.h);
        const $wrapper = $(
            `<div class="clearfix" data-id="${$elem.data('id')}">
                <div class="float-start">
                </div>
                <div class="float-end"><i class="bi bi-trash-fill delete-icon"></i></div>
            </div>`
        );
        $wrapper.find('.float-start').append($iframe.clone());
        $iframe.parent().replaceWith($wrapper);
        $wrapper.find('i.delete-icon').on('click', deleteMessage);
    }
    if ($elem.find('.float-start').html().trim().replaceAll('<br>', '') === "") {
        $elem.remove();
    }
}

const convertYoutubeLinks = () => {
    $('div.media-body>div').map((i, elem) => {
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
    $('i.delete-icon').unbind('click').on('click', deleteMessage);

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

    $('#joinChat').on('click', function () {
        fetch(window.location.href + '/join', {
            method: 'POST',
        }).then(response => {
            if (!response.ok) {
                alert('Some Error Occurred! Code: ' + response.status);
                throw new Error('Some Error Occurred! Code: ' + response.status);
            } else {
                return response.json();
            }
        }).then(data => {
            if (data['status'] === 'success') {
                window.location.reload();
            } else {
                alert(data['message']);
            }
        });
    });

    $('#removeChat').on('click', () => {
        fetch(window.location.href, { method: 'DELETE' })
            .then(response => {
                if (!response.ok) {
                    alert('Some Error Occurred! Code: ' + response.status);
                    throw new Error('Some Error Occurred! Code: ' + response.status);
                } else {
                    window.location.href = '/room';
                }
            })
        ;
    });

    $('#chatroomNameButton').on('click', () => {
        fetch(
            window.location.href,
            {
                method: 'PUT',
                body: new URLSearchParams({ chatroomName: $('#chatroomNameInput').val() }),
            }
        ).then(response => {
            if (!response.ok) {
                throw new Error('Bad response');
            }
            return response.json()
        }).then(response => {
            $('#chatroomNameInput').val(response['chatroomName']).css( { background: '#00ff7f' } );
            setTimeout(() => { $('#chatroomNameInput').css( { background: 'white' } ) }, 1000);
            $('.card-title > strong').html(response['chatroomName'])

        }).catch(error => {
            $('#chatroomNameInput').css( { background: '#f13a13' } );
            setTimeout(() => { $('#chatroomNameInput').css( { background: 'white' } ) }, 1000);
        })
    });

    $('.deleteUser').on('click', function () {
        fetch(
            window.location.href + '/user/' + $(this).parent().parent().data('user-id'),
            { method: 'DELETE' }
        ).then(response => {
            if (!response.ok) {
                throw new Error('Bad response');
            }
            $(this).parent().parent().slideUp(500);
            setTimeout($(this).parent().parent().remove, 500);
        });
    });
});