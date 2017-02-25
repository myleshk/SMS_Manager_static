/**
 * Created by myles on 24/1/2017.
 */
// initialize wilddog
var appId = "telxt";
var config = {
    syncURL: "https://" + appId + ".wilddogio.com",
    authDomain: appId + ".wilddog.com"
};
var error_code_dict;
wilddog.initializeApp(config);

wilddog.sync().ref('error_code').once("value").then(function (snapshot) {
    error_code_dict = snapshot.val();
}).catch(function (err) {
    console.error(err);
});

var main_app = new Vue({
    el: '#app-main',
    data: {
        logged_in: -1, // -1/0/1 means loading/not logged in/logged in, respectively
        view: "login",
        email: "",
        pswd: "",
        error: false,
        error_text: "",
        info: false,
        info_text: ""
    },
    computed: {
        login_active: function () {
            return {
                active: this.view == "login"
            }
        },
        register_active: function () {
            return {
                active: this.view == "register"
            }
        }
    },
    methods: {
        register_view: function () {
            hidePrompt();
            if (this.view == "login") {
                this.view = "register";
            }
        },
        login_view: function () {
            hidePrompt();
            if (this.view == "register") {
                this.view = "login";
            }
        },
        login: function () {
            hidePrompt();
            login(this.email, this.pswd);
        },
        logout: function () {
            hidePrompt();
            logout();
        },
        register: function () {
            hidePrompt();
            register(this.email, this.pswd);
        },
        resetPassword: function () {
            hidePrompt();
            resetPassword(this.email);
        }
    }
});

// check current user
var currentUser;
wilddog.auth().onAuthStateChanged(function (user) {
    currentUser = wilddog.auth().currentUser;
    if (currentUser) {
        if (currentUser.emailVerified) {
            main_app.logged_in = 1;
        } else {
            main_app.logged_in = 0;
            sendEmailVerification(currentUser);
            showError(null, "您的账号未激活，我们刚刚发送了确认邮件，请验证邮箱后重试登录");
            logout();
        }
    } else {
        main_app.logged_in = 0;
    }
    console.info("currentUser", currentUser);
});

var message_ref = wilddog.sync().ref('message');
message_ref.on("value", function (snapshot) {
    console.log(snapshot.val());
});


function login(email, pswd) {
//        main_app.logged_in = -1;// loading
    wilddog.auth().signInWithEmailAndPassword(email, pswd)
        .then(function () {
            console.info("login success");
        }).catch(function (err) {
        console.log(err);
        main_app.logged_in = 0;
        showError(err.code, err.message);
    });
}

function logout() {
    wilddog.auth().signOut().then(function () {
        console.info("user sign out.");
    });
}

function register(email, pswd) {
//        main_app.logged_in = -1;
    wilddog.auth().createUserWithEmailAndPassword(email, pswd)
        .then(function (user) {
            console.info("user created.", user);
        }).catch(function (err) {
        console.error(err);
        main_app.logged_in = 0;
        showError(err.code, err.message);
    });
}

function resetPassword(email) {
    wilddog.auth().sendPasswordResetEmail(email).then(function () {
        // 发送成功
        showInfo("密码重置邮件已经发送，请查收邮箱");
    }).catch(function (err) {
        // 发生错误
        console.error(err);
        showError(err.code, err.message);
    });
}

function hidePrompt() {
    main_app.info = false;
    main_app.error = false;
}

function showInfo(info_text) {
    main_app.info = true;
    main_app.info_text = info_text;
}

function showError(error_code, error_text) {
    main_app.error = true;
    if (error_code_dict && error_code_dict[error_code]) {
        main_app.error_text = error_code_dict[error_code]['cn'];
    } else {
        main_app.error_text = error_text;
    }
}

function sendEmailVerification(user) {
    user.sendEmailVerification()
        .then(function (user) {
            console.info("link email.", user);
        })
        .catch(function (err) {
            console.info("link email failed.", err.code, err);
        });
}

/**********************************************************************
 * OLD
 ***********************************************************************/
/*$("#changeDevice .submit").click(function () {
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
 load_email();
 });*/

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
                tbody.prepend("<tr><td>" + sender + "</td><td>" + body + "</td><td>" + slot + "</td><td>" + time + "</td></tr>")
            });

            updateLastTime(true);
        } else {
            updateLastTime();
        }
    })
        .fail(function () {
            updateLastTime();
        });
}

function timestampToString(ts) {
    var d = new Date(ts * 1000);
    return d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2)
        + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":"
        + ("0" + d.getSeconds()).slice(-2);
}

function updateLastTime(success) {
    var extra_status = "";
    if (!success) extra_status = " （失败）";
    $("#last-update").text(timestampToString(new Date().valueOf() / 1000) + extra_status);
}