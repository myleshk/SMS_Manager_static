/**
 * Created by myles on 24/1/2017.
 */
function login(form) {
    var $form = $(form);
    var data = getFormData($form);
    $form.find("button").prop("disabled", true);
    if (data['email'] && data['password']) {
        $.post("ctrl.php", {
            action: 'login',
            email: data['email'],
            password: data['password']
        }, function (response) {
            console.log(response);
            if (response['error']) {
                warn(response['error']);
            } else if (response['success']) {
                return window.location = "./";
            } else {
                warn("电邮或密码错误");
            }
            $form.find("button").prop("disabled", false);
        });
    } else {
        warn("请输入电邮和密码");
    }
    return false;
}

function register(form) {
    var $form = $(form);
    var data = getFormData($form);
    $form.find("button").prop("disabled", true);
    if (data['email'] && data['password']) {
        $.post("ctrl.php", {
            action: 'register',
            email: data['email'],
            password: data['password'],
            name: data['name'] ? data['name'] : ''
        }, function (response) {
            console.log(response);
            if (response['error']) {
                warn(response['error']);
            } else if (response['success']) {
                $("#activate-info").removeClass("hidden");
                warn(false);
                return;
            } else {
                warn("您已经注册，请登录");
            }
            $form.find("button").prop("disabled", false);
        });
    } else {
        warn("请输入电邮和密码");
    }
    return false;
}

function warn(str) {
    if (str) {
        $('#warning').removeClass("hidden").find(".panel-heading").text(str);
    } else {
        $('#warning').addClass("hidden");
    }
}

function findPassword(elem) {
    warn("请联系管理员");
    return false;
}

function getFormData($form) {
    return $form.serializeArray().reduce(function (obj, item) {
        if (obj[item.name]) {
            if ($.isArray(obj[item.name])) {
                obj[item.name].push(item.value);
            } else {
                var previousValue = obj[item.name];
                obj[item.name] = [previousValue, item.value];
            }
        } else {
            obj[item.name] = item.value;
        }

        return obj;
    }, {});
}

$(document).ready(function () {
    $("input").on('change input', function () {
        $('#warning').addClass("hidden");
        $("#login-form button").prop("disabled", false);
        $("#activate-info").addClass("hidden");
    });
});