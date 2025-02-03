- mysql >8.0
- php >7.3

--- 

# English
### WowAccountRegistration
Azerothcore account registration supports custom backgrounds. Integrated Cloudflare Turnstile human-machine verification, supporting captcha switch.

### precautions
- Database connection: Use mysqli extension to connect to AzerothCore database.
- Control whether Turnstile captcha verification is enabled through the $captchaEnabled variable.
- Custom Background: Set the page background image through the background image property.
- Turnstile captcha: Determine whether to display the captcha component based on the $captchaEnabled variable.

### Important note
- Please replace YOUR_SITE_KEY and YOUR_SECRET_KEY with the actual keys you obtained from the Cloudflare Turnstile console.
- Ensure that the background.jpg image file exists in the correct directory.

--- 

# 中文
Azerothcore帐户注册支持自定义背景。集成Cloudflare Turnstile人机验证，支持验证码开关。


### 代码说明
> PHP 部分
- 数据库连接：通过 mysqli 连接到 AzerothCore 数据库，若连接失败则终止程序。
- 验证码开关：使用 $captchaEnabled 变量控制是否启用 Turnstile 验证码验证。
- 消息公告：定义 $announcement 变量存储消息公告内容。
- 表单处理：当用户提交表单时，会进行以下操作：
 - 验证码验证：根据 $captchaEnabled 决定是否验证验证码。
 - 表单字段验证：检查所有字段是否为空，邮箱格式是否正确，密码和确认密码是否一致。
 - 用户名查重：查询数据库，检查用户名是否已被使用。
 - 数据库插入：若所有验证通过，将用户信息插入数据库，并根据操作结果设置 $registrationResult。

### 重要说明
> HTML 和 CSS 部分
- 自定义背景：使用 background-image 属性设置网页背景图片。
- 居中布局：利用 display: flex、justify-content: center 和 align-items: center 实现表单容器在页面中水平和垂直居中。
- Turnstile 验证码：根据 $captchaEnabled 决定是否显示验证码组件。
- 注册结果显示：使用 $registrationResult 显示注册成功或失败的消息。
- 消息公告展示：在表单上方展示 $announcement 中的消息公告内容。
