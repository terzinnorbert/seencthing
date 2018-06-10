const ignoreDownload = ['share', 'share-container'];

$('.directory-container .share').click((event) => {
    let $row = $(event.currentTarget).closest('.list-group-item');

    axios.get(currentUrl + '/directory/' + $row.data('id') + '/share').then((response) => {
        let $modal = window['$']('#share-modal');

        $row.find('.share').removeClass('far').addClass('fas');
        $modal.find('#share-modal-info').addClass('d-none');
        $modal.find('#share-modal-url').val(response.data.url);
        window['$']('#share-modal').modal('show');
    });
});

$('#share-modal-copy').click((event) => {
    window['$']('#share-modal-info').removeClass('d-none');
    window['$']('#share-modal-url').select();
    document.execCommand("copy");
});

$('.directory-container .list-group-item').click((event) => {
    let $row = $(event.currentTarget);

    let $target = $(event.target);
    for (let ignoredClass of ignoreDownload) {
        if ($target.hasClass(ignoredClass)) {
            return;
        }
    }

    if ('file' == $row.data('type')) {
        let downloadUrl = currentUrl + '/directory/' + $row.data('id') + '/download';

        if (2 == $row.data('state')) {
            downloadFile(downloadUrl);
            return;
        }

        axios.post(downloadUrl).then((response) => {
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

let downloadFile = (url) => {
    if (!$('#download-frame').length) {
        $('body').append('<iframe id="download-frame" style="display: none;"></iframe>');
    }
    $('#download-frame').prop('src', url);
}