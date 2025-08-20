# Anonymous Message Bot for Telegram

این پروژه یک ربات تلگرامی برای ارسال پیام‌های ناشناس است.  
کاربران می‌توانند پیام‌های ناشناس ارسال کنند و مدیر ربات آن‌ها را دریافت کند.

---

## 🚀 ویژگی‌ها
- ارسال پیام ناشناس به ادمین
- ذخیره اطلاعات کاربر (اختیاری)
- قابلیت توسعه آسان (PHP)

---

## 🛠 نصب و راه‌اندازی

1. کلون کردن پروژه:
```bash
git clone https://github.com/bitamooz/TelegramAnonymousMessageBot.git
cd TelegramAnonymousMessageBot
```
**باز کردن فایل config.php و وارد کردن:**

```php
<?php
define('BOT_TOKEN', 'YOUR_BOT_TOKEN');
define('ADMIN_ID', 'YOUR_TELEGRAM_ID');
?>
```
آپلود فایل‌ها روی هاست یا سرور.

**ست کردن Webhook:**

```bash
https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=YOUR_DOMAIN/index.php
```

## 📌 نیازمندی‌ها
- PHP 7.4+
- فعال بودن cURL

