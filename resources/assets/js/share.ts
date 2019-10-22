import notify from "./notify.ts";

$('#download').click((event) => {
    event.preventDefault();
    let url = $(event.target).attr('href');
    axios.post(currentUrl + '/download');
    let downloadSate = setInterval(() => {
        axios.get(currentUrl + '/progress').then((response) => {
            window['$']('.progress-bar').css('width', response.data.progress + '%');
            if (true === response.data.downloadable) {
                clearInterval(downloadSate);
                downloadFile(url);
            }
        });
    }, 1000);
});

let downloadFile = (url) => {
    if (!$('#download-frame').length) {
        $('body').append('<iframe id="download-frame" style="display: none;"></iframe>');
    }
    $('#download-frame').prop('src', url);
};

$('.directory-container .list-group-item').click((event) => {
    let $row = $(event.currentTarget);

    if ('file' == $row.data('type')) {
        let downloadUrl = currentUrl + '/directory/' + $row.data('id') + '/download';

        axios.post(downloadUrl).then((response) => {

            if (!response.data.success) {
                notify.danger($row.find('.col-name').text() + '<hr>' + response.data.error);
                return;
            }

            $row.find('.progress').removeClass('d-none');
            $row.find('.progress-bar').removeClass('d-none').css('width', '1%');
            let downloadSate = setInterval(() => {
                axios.get(currentUrl + '/directory/' + $row.data('id') + '/state').then((response) => {
                    $row.find('.progress-bar').css('width', response.data.progress + '%');
                    if (true === response.data.downloadable) {
                        clearInterval(downloadSate);
                        $row.find('i.icon').removeClass('far').addClass('fas');
                        $row.data('state', 2);
                        downloadFile(downloadUrl);
                    }
                });
            }, 1000);
        });
    } else {
        window.location = $row.data('path');
    }
});