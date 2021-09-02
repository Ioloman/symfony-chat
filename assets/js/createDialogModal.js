import $ from 'jquery';
import { autocomplete } from "@algolia/autocomplete-js";
import '@algolia/autocomplete-theme-classic';

function initComplete(id) {
    autocomplete({
        container: '#' + id,
        placeholder: 'Enter User\'s email',
        getSources({ query }) {
            return [
                {
                    sourceId: 'users',
                    async getItems({ query }) {
                        return (await fetch(
                            $('#' + id).data('query-url') + '?q=' + query.toLowerCase(),
                            {
                                method: 'GET'
                            }
                        )).json();
                    },
                    getItemInputValue({ item }) {
                        return item.email;
                    },
                    templates: {
                        item({ item }) {
                            return item.email;
                        },
                    },
                },
            ];
        },
    });
    $('#' + id).find('input').attr('name', 'email[]')
    window.localStorage.setItem('searches', (parseInt(window.localStorage.getItem('searches')) + 1).toString())
}

function addField() {
    const $newUserInput = $(this).parent().parent().clone();
    const id = $newUserInput.find('div.col-10>div').attr('id').split('-')[0] + '-' + window.localStorage.getItem('searches');
    $newUserInput.find('div.col-10>div').attr('id', id).html("");
    $newUserInput.find('input').val('');
    $newUserInput.find('#addField').on('click', addField);
    $newUserInput.find('#removeField').on('click', removeField);
    $(this).parent().parent().after($newUserInput);
    initComplete(id);
}

function removeField() {
    $(this).parent().parent().slideUp().remove();
}

$(function () {
    $('#createChatroomButton').click(function (e) {
        e.preventDefault();
        const $form = $(this).parent().parent();

        $.ajax(
            {
                url: $(this).data('controller-url'),
                method: 'POST',
                data: {
                    name: $form.find('[name="name"]').val(),
                    private: $form.find('[name="private"]:checked').val(),
                    email: $form.find('input[name="email[]"]').map((id, elem) => elem.value).toArray(),
                }
            }
        ).done(function (data) {
            if (data['status'] === 'success') {
                window.location.href = data['url'];
            } else {
                alert(data['message']);
                $form.addClass('was-validated');
            }
        }).fail(function () {
            alert('Internal Error!');
        })
        ;
        (new bootstrap.Modal(document.getElementById('newDialogModal'))).hide();
    });

    $('#addField').on('click', addField);

    $('#removeField').on('click', removeField);

    window.localStorage.setItem('searches', '0');
    initComplete('autocomplete');
});