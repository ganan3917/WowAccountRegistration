### WowAccountRegistration
Azerothcore account registration supports custom backgrounds. Integrated Cloudflare Turnstile human-machine verification, supporting captcha switch.

- mysql >8.0
- php >7.3

### describe
- 数据库连接：使用 mysqli 扩展连接到 AzerothCore 数据库。
- 通过 $captchaEnabled 变量控制是否启用 Turnstile 验证码验证。
- 自定义背景：通过 background-image 属性设置页面背景图片。
- Turnstile 验证码：根据 $captchaEnabled 变量决定是否显示验证码组件。

### keynote
- 请将 YOUR_SITE_KEY 和 YOUR_SECRET_KEY 替换为你在 Cloudflare Turnstile 控制台获取的实际密钥。
- 确保 background.jpg 图片文件存在于正确的目录中。
