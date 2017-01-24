/**
 * Created by myles on 24/1/2017.
 */
$("#changeDevice .submit").click(function () {
    var code = $("input#code").val();

    if (code) {
        $.post('ctrl.php', {
            action: 'validate_code',
            code: code
        }, function (response) {
            if (response['success']) {
                var uuid = response['uuid'];
                reload_uuid();
            } else {
                $("#uuid").text("绑定码错误，请重试");
            }
        });
        $("input#code").val('');
    }
});

$(document).ready(function () {
    reload_uuid();
    load_messages();
});


function reload_uuid() {
    $.post('ctrl.php', {
        action: "get_assoc_uuid"
    }, function (response) {
        if (response['success']) {
            var uuid = response['uuid'];
            $("#uuid").text(uuid);
            load_messages();
        } else {
            $("#uuid").text("绑定码错误，请重试");
        }
    });
}


function load_messages() {
    $.post('ctrl.php', {
        action: "get_message"
    }, function (response) {
        if (response['success']) {
            var messages = response['messages'];
            // load to table
            var tbody = $("#message-table tbody");
            //clear table
            tbody.empty();
            $.each(messages, function () {
                var uuid = this['uuid'];
                var sender = this['sender'];
                var body = this['message_body'];
                var slot = parseInt(this['slot']);
                var time = timestampToString(this['timestamp']);
                tbody.append("<tr><td>" + sender + "</td><td>" + body + "</td><td>" + slot + "</td><td colspan='2'>" + time + "</td></tr>")
            });

        } else {
        }

    });
}

function timestampToString(ts) {
    var d = new Date(ts * 1000);
    return d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2)
        + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);

}