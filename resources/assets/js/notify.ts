class Notify {
    notify(content, type) {
        let notification = $('#notify-bar .template').clone().removeClass('template').addClass('show alert-' + type);
        notification.find('.notify-bar-content').html(content);
        notification.appendTo('#notify-bar-list');

        setInterval(() => notification.hide('slow', () => notification.remove()), 5000);
    }

    success(content) {
        this.notify(content, 'success');
    }

    danger(content) {
        this.notify(content, 'danger');
    }
}

export default new Notify();