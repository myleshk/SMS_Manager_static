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

// load default error_code list
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
        info_text: "",
        uuid: "",
        uuids: [],
        last_update: "",
        messages: [],
        previous_uuid: "",
        pairing_editing: false,
        pairing_verifying: false,
        pairing_code: ""
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
        },
        sorted_messages: function () {
            return this.messages.sort(function (a, b) {
                return b.timestamp - a.timestamp;
            });
        },
        has_uuids: function () {
            return Object.keys(this.uuids).length > 0;
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
        },
        timestampToString: function (ts) {
            return timestampToString(ts);
        },
        editPairing: function () {
            this.pairing_editing = !this.pairing_editing;
            hidePrompt();
        },
        verifyCode: function () {
            verifyCode();
        }
    },
    watch: {
        uuid: function (val) {
            displayMessages(val);
        }
    }
});

// check current user
var currentUser;
wilddog.auth().onAuthStateChanged(function (user) {
    currentUser = wilddog.auth().currentUser;
    if (currentUser) {
        if (currentUser.emailVerified) {
            // login success
            main_app.logged_in = 1;
            main_app.email = currentUser.email;

            getUuids();
            displayMessages();
        } else {
            // need email verification
            main_app.logged_in = 0;
            sendEmailVerification(currentUser);
            showError(null, "您的账号未激活，我们刚刚发送了确认邮件，请验证邮箱后重试登录");
            logout();
        }
    } else {
        main_app.logged_in = 0;
    }
    console.info("currentUser", currentUser);
    // we always clean sensitive data
    main_app.pswd = "";
});

/***
 * functions
 */

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

    // clean previous user data
    main_app.email = "";
    main_app.uuid = "";
    main_app.uuids = [];
    main_app.messages = [];
    main_app.previous_uuid = "";
    main_app.pairing_code = "";
    // reload page to be safe
    location.reload();
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

function getUuids() {
    // get uuids
    wilddog.sync().ref('user/' + currentUser.uid).on("value", function (snapshot) {
        if (snapshot.val()) {
            main_app.uuids = snapshot.val().uuid;
            var uuids = main_app.uuids;
            if (Object.keys(uuids).length > 0) {
                main_app.uuid = uuids[Object.keys(uuids)[0]];
            }
        }
    });
    main_app.uuid = "";
}

function displayMessages(uuid) {
    if (!uuid) return false;
    if (main_app.previous_uuid) {
        // unbind old listener
        wilddog.sync().ref('message').child(main_app.previous_uuid).off();
    }
    wilddog.sync().ref('message').child(uuid).on("value", function (snapshot) {
        main_app.messages = snapshot.val();
        console.log(main_app.messages);
        main_app.last_update = timestampToString(new Date().valueOf() / 1000);
    });
    main_app.previous_uuid = uuid;
}

function timestampToString(ts) {
    var d = new Date(ts * 1000);
    return d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2)
        + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":"
        + ("0" + d.getSeconds()).slice(-2);
}

function verifyCode() {
    hidePrompt();
    var code = main_app.pairing_code;
    if (!code) return showError(null, "请输入验证码");
    main_app.pairing_verifying = true;
    console.log(code);

    wilddog.sync().ref('pairing_code').child(code).once("value").then(function (snapshot) {
        var values = snapshot.val();
        main_app.pairing_verifying = false;
        // check expire
        if (values && values.expire && new Date().valueOf() / 1000 < values.expire) {
            // check good
            if (values.uuid) {
                // add uuid to user record
                wilddog.sync().ref('user').child(currentUser.uid).child("uuid").push(values.uuid);
            }

        } else {
            showError(null, "绑定码错误或已过期，请在手机上重新获取");
        }
        // clear code
        main_app.pairing_code = "";
    }).catch(function (err) {
        showError(err.code, err.message);
        console.error(err);
        main_app.pairing_verifying = false;
    });
}

//TODO: pairing_code
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

// TODO: finish add/change phone logic -- remove phone