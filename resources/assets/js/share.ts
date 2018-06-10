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
}