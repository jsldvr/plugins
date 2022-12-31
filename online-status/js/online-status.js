(function($) {
    $(document).ready(function() {
        var $statuses = $('.online-status');

        if (!$statuses.length) {
            return;
        }

        function updateStatus(authorId, online, offline) {
            $.post(online_status.ajax_url, {
                action: 'check_online_status',
                author_id: authorId
            }, function(response) {
                if (response.success) {
                    $('.online-status[data-author-id="' + authorId + '"] img').attr('src', online);
                } else {
                    $('.online-status[data-author-id="' + authorId + '"] img').attr('src', offline);
                }
            });
        }

        function checkStatus() {
            $statuses.each(function() {
                var $status = $(this);
                var authorId = $status.data('author-id');
                var online = $status.data('online');
                var offline = $status.data('offline');

                updateStatus(authorId, online, offline);
            });
        }

        checkStatus();
        setInterval(checkStatus, online_status.interval);
    });
})(jQuery);
