<?php
// ============================================================
//  Mail Ayarları — SMTP (Gmail / diğer sağlayıcılar)
// ============================================================
//
//  Gmail kullanıyorsanız:
//  1. Google Hesabınız → Güvenlik → 2 Adımlı Doğrulama (AÇ)
//  2. "Uygulama Şifresi" oluşturun ve aşağıya girin
//
// ============================================================

define('MAIL_HOST',       'smtp.gmail.com');   // SMTP sunucu
define('MAIL_USERNAME',   'berberbookrandevu@gmail.com'); // Gönderici e-posta
define('MAIL_PASSWORD',   'vumzdvdkswjsfmnd'); // Uygulama şifresi
define('MAIL_PORT',       587);                // 587 = TLS, 465 = SSL
define('MAIL_ENCRYPTION', 'tls');              // 'tls' veya 'ssl'
define('MAIL_FROM',       'berberbookrandevu@gmail.com');
define('MAIL_FROM_NAME',  'BerberBook');

// CRON Log dosyası yolu
define('CRON_LOG_PATH', __DIR__ . '/../cron_log/reminder.log');
