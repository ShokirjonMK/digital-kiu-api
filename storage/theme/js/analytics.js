var web_analytics = {};

web_analytics.init = function (key, value) {
    if (value != undefined) {
        link = value;
    } else {
        link = web_analytics.current_link;
    }

    web_analytics.ajax({
        action: 'set',
        key: key,
        value: link,
        referrer: web_analytics.referrer,
        status_code: web_analytics.status_code,
    });
}

web_analytics.ajax = function (data) {
    var text = "";
    var length = 10;
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var alps = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < length; i++) {
        text += alps.charAt(Math.floor(Math.random() * alps.length));
    }

    var config = {
        'xre': 'analytics',
        '_ajax_token': text,
        '_csrf_token': csrfToken,
    };

    if (data != undefined && data != '') {
        var datas = Object.assign(data, config);
    } else {
        var datas = data;
    }

    setTimeout(function () {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: window.location.href,
            data: datas
        });
    }, 500);
}