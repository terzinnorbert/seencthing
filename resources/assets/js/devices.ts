$(function () {
    $('a.toolbar-add').click((e) => {
        e.preventDefault();
        $('.toolbar-add').toggle();
        $('#device-id').focus();
    });

    $('#add-device').click(() => {
        $('.add-device-error').addClass('d-none');
        axios.post('/devices', {
            deviceId: $('#device-id').val()
        }).then((response) => {
            if (true === response.data.success) {
                location.reload();
            } else {
                $('.add-device-error').html(response.data.error).removeClass('d-none');
            }
        });
    });
});

