<?php
// 开启会话
session_start();

// 数据库连接配置
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "your_azerothcore_db";

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("数据库连接失败: ". $conn->connect_error);
}

// 验证码开关，可按需修改
$captchaEnabled = true;

// 消息公告内容
$announcement = "欢迎来到 AzerothCore 注册页面！请遵守相关规定进行注册。";

// 初始化注册结果信息
$registrationResult = "";

// 检查是否通过 POST 方法提交了表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取用户输入的信息
    $inputUsername = $_POST["username"];
    $inputPassword = $_POST["password"];
    $inputEmail = $_POST["email"];

    $errorMessage = "";

    // 验证码验证
    if ($captchaEnabled) {
        $turnstileResponse = $_POST["cf-turnstile-response"];
        $secretKey = "YOUR_SECRET_KEY";
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = array(
            'secret' => $secretKey,
            'response' => $turnstileResponse,
            'remoteip' => $ip
        );
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
        if ($result === false) {
            $errorMessage = "验证码验证请求失败，请稍后重试。";
        } else {
            $response = json_decode($result, true);
            if (!$response['success']) {
                $errorMessage = "验证码验证失败，请重试。";
            }
        }
    }

    // 其他表单验证
    if (empty($errorMessage)) {
        if (empty($inputUsername) || empty($inputPassword) || empty($inputEmail)) {
            $errorMessage = "请填写所有必填字段。";
        } elseif (!filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "请输入有效的电子邮件地址。";
        }
    }

    // 插入数据库
    if (empty($errorMessage)) {
        // 对输入进行转义，防止 SQL 注入
        $username = $conn->real_escape_string($inputUsername);
        $password = $conn->real_escape_string($inputPassword);
        $email = $conn->real_escape_string($inputEmail);

        // 生成 AzerothCore 所需的密码哈希（格式为 username:password 的 SHA1 哈希）
        $hash = sha1(strtoupper($username. ':' . strtoupper($password)));

        // 检查用户名是否已存在
        $checkQuery = "SELECT id FROM account WHERE username = '$username'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            $errorMessage = "该用户名已被使用，请选择其他用户名。";
        } else {
            // 插入新账号信息
            $insertQuery = "INSERT INTO account (username, sha_pass_hash, email) VALUES ('$username', '$hash', '$email')";

            if ($conn->query($insertQuery) === TRUE) {
                $registrationResult = '<div class="success">账号注册成功！</div>';
            } else {
                $registrationResult = '<div class="error">注册过程中出现错误: '. $conn->error. '</div>';
            }
        }
    } else {
        $registrationResult = '<div class="error">'. $errorMessage. '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AzerothCore 账号注册</title>
    <!-- 引入 Turnstile 脚本 -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        body {
            background-image: url('background.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }

        .announcement {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="announcement">
            <?php echo $announcement; ?>
        </div>
        <?php echo $registrationResult; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">用户名:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">密码:</label>
            <input type="password" id="password" name="password" required>
            <label for="email">邮箱:</label>
            <input type="email" id="email" name="email" required>
            <?php if ($captchaEnabled): ?>
                <div class="cf-turnstile" data-sitekey="YOUR_SITE_KEY"></div>
            <?php endif; ?>
            <input type="submit" value="注册">
        </form>
    </div>
</body>

</html>
