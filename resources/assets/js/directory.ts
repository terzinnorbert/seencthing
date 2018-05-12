$('.list-group-item').click((event) => {
    let $row = $(event.currentTarget);

    if ('file' == $row.data('type')) {
        //TODO implement file handling
    } else {
        window.location = $row.data('path');
    }
});