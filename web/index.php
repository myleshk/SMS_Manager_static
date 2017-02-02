<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Telxt</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="./sms_manager.apk" target="_blank" class="navbar-brand pull-right">
                <img src="img/ic_android_black_48dp_1x.png" title="下载APP" alt="下载APP" id="download-app">
            </a>
        </div>
    </div>
</nav>
<?php

$login_form = /** @lang HTML */
    <<<EOL
<link rel="stylesheet" href="css/login.css">

<div class="form" id="login-form">

    <ul class="tab-group">
        <li class="tab"><a href="#signup">注册</a></li>
        <li class="tab active"><a href="#login">登录</a></li>
    </ul>

    <div class="panel hidden panel-danger" id="warning">
        <div class="panel-heading">Error</div>
    </div>

    <div class="panel hidden panel-success" id="activate-info">
        <div class="panel-heading">注册成功！请登录</div>
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

$dashboard = /** @lang HTML */
    <<<DOD
<div class="container">
    <table class="table table-bordered table-condensed table-hover">
        <tbody>
        <tr>
            <th>当前账号</th>
            <td><span id="email"><div class='loader'></div></span></td>
            <td class="button-container"><button class="btn-block btn btn-danger" onclick="log_out()">退出登录</button></td>
        </tr>
        <tr>
            <th style="width: 1px;white-space: nowrap;">绑定手机(UUID)</th>
            <td><span id="uuid"><div class='loader'></div></span>
            </td>
            <td class="button-container">
                <button class="btn btn-default btn-block" data-toggle="modal" data-target="#changeDevice">更改</button>
            </td>
        </tr>
        <tr>
            <th style="width: 1px;white-space: nowrap;">上次刷新</th>
            <td><span id="last-update"><div class='loader'></div></span>
            </td>
            <td class="button-container">
                <button class="btn btn-block btn-default" onclick="return load_messages()">刷新</button>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="table table-bordered" id="message-table">
        <thead>
        <tr>
            <th>来自</th>
            <th>消息内容</th>
            <th>SIM卡编号</th>
            <th>时间</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div id="changeDevice" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">更改绑定的手机设备</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="code">请输入四位绑定码</label>
                    <input type="text" class="form-control" maxlength="4" id="code">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="submit btn btn-danger" data-dismiss="modal">提交</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">放弃</button>
            </div>
        </div>
    </div>
</div>
DOD;


session_start();
// check login status
if (isset($_COOKIE['PHPSESSID']) && isset($_SESSION) && isset($_SESSION['user_id'])) {
//    $sid = $_COOKIE['PHPSESSID'];
    if ($_SESSION['user_id']) {
        $user_id = $_SESSION['user_id'];
        echo $dashboard;
    }
} else {
    // no cookie, not logged in
    echo $login_form;
}
?>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="js/ui.js"></script>
<script src="js/login.js"></script>
<script src="js/main.js"></script>
</body>
</html>
