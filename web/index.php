<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Telxt</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php
$login_form = /** @lang HTML */
    <<<EOL
    <div class="form" id="login-form">

    <ul class="tab-group">
        <li class="tab"><a href="#signup">注册</a></li>
        <li class="tab active"><a href="#login">登录</a></li>
    </ul>

    <div class="panel hidden panel-danger" id="warning">
        <div class="panel-heading">Error</div>
    </div>

    <div class="panel hidden panel-success" id="activate-info">
        <div class="panel-heading">Your account is created! Please check your email to activate.</div>
    </div>

    <div class="tab-content">
        <div id="login">
            <h1>欢迎回来！</h1>
            <form onsubmit="return login(this)">
                <div class="field-wrap">
                    <label>
                        电邮<span class="req">*</span>
                    </label>
                    <input name="email" type="text" required title="格式错误，请确认" autocomplete="off"/>
                </div>

                <div class="field-wrap">
                    <label>
                        密码<span class="req">*</span>
                    </label>
                    <input name="password" type="password" required title="格式错误，请确认" autocomplete="off"/>
                </div>

                <a class="pull-right forgot btn btn-link" onclick="findPassword(this)">找回密码</a>
                <button class="button button-block"/>
                登录</button>
            </form>
        </div>
        <div id="signup">
            <h1>第一次使用？</h1>
            <form onsubmit="return register(this)">
                <div class="field-wrap">
                    <label>
                        电邮<span class="req">*</span>
                    </label>
                    <input name="email" type="text" required autocomplete="off"/>
                </div>

                <div class="field-wrap">
                    <label>
                        密码<span class="req">*</span>
                    </label>
                    <input name="password" type="password" required autocomplete="off"/>
                </div>

                <button type="submit" class="button button-block"/>
                马上注册</button>
            </form>
        </div>
    </div><!-- tab-content -->

</div> <!-- /form -->
EOL;

session_start();
// check login status
if (isset($_COOKIE['PHPSESSID']) && isset($_SESSION) && isset($_SESSION['user_id'])) {
//    $sid = $_COOKIE['PHPSESSID'];

    $_SESSION['user_id'];
} else {
    var_dump($_SESSION);
    // no cookie, not logged in
    echo $login_form;
}
?>
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="js/ui.js"></script>
<script src="js/login.js"></script>
</body>
</html>
