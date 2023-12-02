Admin hCaptcha CN
====================

这个插件适用于 [YOURLS](http://yourls.org) `>= 1.7`（理论上更早版本也可以使用，但未经过测试）。在`1.9.2`上经测试可用。

概述
-----------
针对私有的（包括使用前台为用户提供服务的）YOURLS 短链接服务管理面板的 SPAM 防护。任何未认证的用户都需要通过 hCaptcha 才能登录到管理面板。

安装
------------
1. 下载发行版压缩包，解压后将整个 `admin-hcaptcha-cn` 文件夹上传至 `/user/plugins`。
2. 转到插件管理页面 ( *例如* `http://sho.rt/admin/plugins.php` ) 并激活插件。
3. 在 [hCaptcha 仪表板](https://dashboard.hcaptcha.com/signup)注册账号，注册后在跳转的[账号设置](https://dashboard.hcaptcha.com/settings)页生成并复制私钥（secret）。转到[网站页](https://dashboard.hcaptcha.com/sites)，创建一个新的网站，完成设置后复制站点密钥（sitekey）。
4. 转到 Manage Plugins > Admin hCaptcha 设置，将复制的私钥和站点密钥粘贴到对应框中，点击“保存设置”。
5. 至此设置完成，享受本插件带给你的安全体验吧！

许可证
-------
MIT 许可证