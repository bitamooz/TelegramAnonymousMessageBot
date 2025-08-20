<?php
/*
🚀 این سورس کد رو به‌صورت کاملاً رایگان از گنجینه برنامه‌نویسی بیت‌آموز دریافت کردی!  
🎯 جدیدترین سورس‌ها، آموزش‌ها و ابزارهای کاربردی رو همین الان از سایت ما دانلود کن:  
🌐 https://BitAmooz.com  

💡 دوست داری همیشه یک قدم جلوتر باشی؟  
هر روز کلی سورس رایگان، تکنیک‌های برنامه‌نویسی و نکات حرفه‌ای توی بیت‌آموز منتشر میشه!  
⏳ وقتشه که سطح کدنویسی خودتو ارتقا بدی!  
🔗 همین الان وارد سایت شو و سورس‌های بیشتری بگیر: https://BitAmooz.com  
*/

ob_start();

const STORAGE_PATH = 'data_storage/'; // مسیر پوشه ذخیره سازی اطلاعات
const ADMIN_ID = 00000000000; // آیدی عددی ادمین
const BOT_USERNAME = 'Hdsjdbot'; // آیدی ربات بدون @
const BOT_NAME = 'پیام ناشناس به من بیت آموز'; // اسم ربات
const API_TOKEN = '821X7wuYMVMImE'; // توکن ربات

function BitCTel($method, $params = []) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.telegram.org/bot" . API_TOKEN . "/" . $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params
    ]);
    $response = curl_exec($ch);
    if (curl_error($ch)) {
        error_log(curl_error($ch));
        return false;
    }
    curl_close($ch);
    return json_decode($response, true);
}

function storeData($path, $content) {
    if (!file_exists(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }
    return file_put_contents($path, $content) !== false;
}

function fetchData($path) {
    return file_exists($path) ? file_get_contents($path) : '';
}

function dispatchText($chatId, $text, $keyboard = null) {
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'MarkDown'
    ];
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    BitCTel('sendMessage', $data);
}

function dispatchAction($chatId, $action) {
    BitCTel('sendChatAction', ['chat_id' => $chatId, 'action' => $action]);
}

function relayMessage($toChat, $fromChat, $msgId) {
    BitCTel('forwardMessage', [
        'chat_id' => $toChat,
        'from_chat_id' => $fromChat,
        'message_id' => $msgId
    ]);
}

$input = json_decode(file_get_contents('php://input'), true);
$callbackQuery = $input['callback_query'] ?? null;
$message = $input['message'] ?? null;
if (!$message && !$callbackQuery) exit;

$chatId = $message ? $message['chat']['id'] : $callbackQuery['from']['id'];
$userId = $message ? $message['from']['id'] : $callbackQuery['from']['id'];
$messageId = $message ? $message['message_id'] : $callbackQuery['message']['message_id'];
$text = $message ? ($message['text'] ?? '') : '';
$firstName = $message ? ($message['from']['first_name'] ?? 'کاربر') : ($callbackQuery['from']['first_name'] ?? 'کاربر');
$uniqueCode = $chatId * 6;

if (!file_exists(STORAGE_PATH . $chatId)) {
    mkdir(STORAGE_PATH . $chatId, 0777, true);
}

$currentStep = fetchData(STORAGE_PATH . $chatId . '/step.txt');
$userStatus = fetchData(STORAGE_PATH . $chatId . '/status.txt');

$mainMenu = [
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'لینک اختصاصی 📬']],
        [['text' => 'تنظیمات ⚙️'], ['text' => 'راهنما ❓']]
    ]
];

$backMenu = [
    'resize_keyboard' => true,
    'keyboard' => [[['text' => 'بازگشت 🏠']]]
];

$settingsMenu = [
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'خاموش کردن 📴'], ['text' => 'روشن کردن 🔄']],
        [['text' => 'بازگشت 🏠']]
    ]
];

function isSendAllowed($chatId) {
    $lastTime = fetchData(STORAGE_PATH . $chatId . '/last_message.txt');
    if ($lastTime) {
        $elapsed = time() - (int)$lastTime;
        return $elapsed >= 300;
    }
    return true;
}

if ($text === '/start') {
    dispatchAction($chatId, 'typing');
    storeData(STORAGE_PATH . $chatId . "/$uniqueCode.txt", $userId);
    dispatchText($chatId, "سلام {$firstName} جان 😊\nبه **{$BOT_NAME}** خوش اومدی! 🎉\nلینک اختصاصی خودتو بساز و پیامای ناشناس از دوستات بگیر 💬\nیه گزینه انتخاب کن 👇", $mainMenu);
} elseif ($text === 'بازگشت 🏠') {
    dispatchAction($chatId, 'typing');
    storeData(STORAGE_PATH . $chatId . '/step.txt', 'none');
    storeData(STORAGE_PATH . $chatId . '/reply_to.txt', 'null');
    dispatchText($chatId, 'برگشتیم به منوی اصلی! 😄', $mainMenu);
} elseif ($text === 'لینک اختصاصی 📬') {
    dispatchText($chatId, "اینو با دوستات یا توی گروه‌ها به اشتراک کن 📤\nاونا می‌تونن ناشناس باهات گپ بزنن 🕵️❤️\nپیام‌ها از ربات می‌رسن ✉️");
    dispatchAction($chatId, 'typing');
    dispatchText($chatId, "سلام! من {$firstName} هستم 😊\nروی لینک زیر بزن 👇\nهر چی تو دلته، انتقاد یا اعتراف، بنویس و بفرست. ناشناسه 🕶️✨\n📝 پیامت مستقیم می‌رسه به من\nتو هم رباتو تست کن و پیامای باحال بگیر! 😅\n\n🔗 لینک من:\nhttps://telegram.me/" . BOT_USERNAME . "?start={$uniqueCode}", $backMenu);
} elseif (strpos($text, '/start ') === 0) {
    $code = str_replace('/start ', '', $text);
    $targetId = $code / 6;
    
    if (file_exists(STORAGE_PATH . $targetId . "/$code.txt")) {
        if ($targetId == $chatId) {
            dispatchText($chatId, 'تنهایی سخته، ولی نمی‌تونی با خودت چت کنی! 😄');
            exit;
        }
        
        if (fetchData(STORAGE_PATH . $targetId . '/status.txt') === 'blocked') {
            dispatchText($chatId, 'این کاربر به دلیل تخلف مسدود شده 🚫');
            exit;
        }
        
        storeData(STORAGE_PATH . $chatId . '/text.txt', $targetId);
        storeData(STORAGE_PATH . $chatId . '/step.txt', 'message');
        dispatchText($chatId, "سلام! 🌟\n\nداری برای کاربر **{$code}** پیام ناشناس می‌فرستی.\nهر چی تو دلته بنویس 💬\nبعدش می‌تونی با /start لینک خودتو بگیری 😌📩\n🛑 پیامتو کامل و یکجا بفرست.\n🚫 توهین یا محتوای نامناسب ممنوعه!\nپیامت رو بنویس! 💌\n👇", $backMenu);
    } else {
        dispatchText($chatId, "😔 نمی‌تونیم با این کاربر ارتباط بگیریم!\n\nچرا؟ 🤔\n🔍 شاید لینک اشتباهه.\n🔒 یا سرویسش خاموشه.\nلطفاً لینک رو چک کن یا بعداً تست کن! 🚀", $mainMenu);
    }
} elseif ($currentStep === 'message') {
    if (!isSendAllowed($chatId)) {
        dispatchText($chatId, 'لطفاً ۵ دقیقه صبر کن تا دوباره بتونی پیام بفرستی ⏳');
        exit;
    }
    
    $targetId = fetchData(STORAGE_PATH . $chatId . '/text.txt');
    $msgMap = json_encode(['sender' => $chatId, 'receiver' => $targetId, 'message_id' => $messageId, 'original_message_id' => $messageId]);
    storeData(STORAGE_PATH . $targetId . "/message_map_{$messageId}.txt", $msgMap);
    dispatchText($targetId, $text, [
        'inline_keyboard' => [[['text' => 'پاسخ بده ✍️', 'callback_data' => "reply_{$messageId}"]]]
    ]);
    storeData(STORAGE_PATH . $chatId . '/text.txt', 'null');
    storeData(STORAGE_PATH . $chatId . '/step.txt', 'none');
    storeData(STORAGE_PATH . $chatId . '/last_message.txt', time());
    dispatchText($chatId, 'پیامت با موفقیت ارسال شد! 🎉', $mainMenu);
} elseif ($currentStep === 'reply' && $text) {
    $replyToMsgId = fetchData(STORAGE_PATH . $chatId . '/reply_to.txt');
    $msgMapFile = STORAGE_PATH . $chatId . "/message_map_{$replyToMsgId}.txt";
    if (!file_exists($msgMapFile)) {
        dispatchText($chatId, 'خطا: پیام اصلی یافت نشد! لطفاً دوباره امتحان کن.', $mainMenu);
        storeData(STORAGE_PATH . $chatId . '/step.txt', 'none');
        storeData(STORAGE_PATH . $chatId . '/reply_to.txt', 'null');
        exit;
    }
    
    $msgMap = json_decode(fetchData($msgMapFile), true);
    if ($msgMap && isset($msgMap['sender']) && $msgMap['receiver'] == $chatId) {
        dispatchText($msgMap['sender'], "پاسخ ناشناس به پیامت: {$text}", $backMenu);
        storeData(STORAGE_PATH . $chatId . '/step.txt', 'none');
        storeData(STORAGE_PATH . $chatId . '/reply_to.txt', 'null');
        dispatchText($chatId, 'پاسخت با موفقیت ارسال شد! 🎉', $mainMenu);
    } else {
        dispatchText($chatId, 'خطا: نمی‌تونیم گیرنده پیام رو پیدا کنیم. دوباره تلاش کن.', $mainMenu);
        storeData(STORAGE_PATH . $chatId . '/step.txt', 'none');
        storeData(STORAGE_PATH . $chatId . '/reply_to.txt', 'null');
    }
} elseif ($text === 'راهنما ❓') {
    dispatchAction($chatId, 'typing');
    dispatchText($chatId, "🎭 **{$BOT_NAME}** رباتی برای پیام‌های ناشناسه! 😄💬\nنظراتت رو بدون لو رفتن اسمت بگو.\n\n⚠️ قوانین:\n✅ با ادب باش\n❌ بحث سیاسی ممنوع\n❌ توهین به ادیان ممنوع\n❌ محتوای نامناسب ممنوع\n\n🔒 ربات هیچ مسئولیتی در قبال پیام‌ها نداره.\n🚫 تخلف مساویه با مسدود شدن.\n\n💻 ساخته شده توسط **ابوالفضل عنایتی**، بیت‌آموز.\nممنون که با ما هستی 🌈\nتیم {$BOT_NAME} 🤝", $backMenu);
} elseif ($text === 'تنظیمات ⚙️') {
    dispatchAction($chatId, 'typing');
    dispatchText($chatId, 'یه گزینه انتخاب کن 👇', $settingsMenu);
} elseif ($text === 'خاموش کردن 📴') {
    dispatchAction($chatId, 'typing');
    unlink(STORAGE_PATH . $chatId . "/$uniqueCode.txt");
    dispatchText($chatId, 'سرویس خاموش شد 🚫', $backMenu);
} elseif ($text === 'روشن کردن 🔄') {
    dispatchAction($chatId, 'typing');
    storeData(STORAGE_PATH . $chatId . "/$uniqueCode.txt", $userId);
    dispatchText($chatId, 'سرویس روشن شد 🚀', $backMenu);
} elseif ($text === 'آمار ربات' && $userId === ADMIN_ID) {
    $users = explode("\n", fetchData(STORAGE_PATH . 'users.txt'));
    $count = count(array_filter($users)) - 1;
    dispatchAction($chatId, 'typing');
    dispatchText($chatId, "تعداد کاربران: {$count} 👥");
} elseif ($text === 'ارسال به همه' && $userId === ADMIN_ID) {
    storeData(STORAGE_PATH . $chatId . '/step.txt', 'broadcast');
    dispatchText($chatId, 'پیام برای پخش همگانی رو بنویس 👇', ['parse_mode' => 'MarkDown']);
} elseif ($currentStep === 'broadcast' && $userId === ADMIN_ID) {
    $users = explode("\n", fetchData(STORAGE_PATH . 'users.txt'));
    foreach ($users as $user) {
        if ($user) {
            dispatchText($user, $text);
        }
    }
    storeData(STORAGE_PATH . $chatId . '/step.txt', 'none');
} elseif ($text === 'فروارد همگانی' && $userId === ADMIN_ID) {
    storeData(STORAGE_PATH . $chatId . '/step.txt', 'forward');
    dispatchText($chatId, 'پیام برای فروارد همگانی رو بفرست 👇', ['parse_mode' => 'MarkDown']);
} elseif ($currentStep === 'forward' && $userId === ADMIN_ID) {
    $users = explode("\n", fetchData(STORAGE_PATH . 'users.txt'));
    foreach ($users as $user) {
        if ($user) {
            relayMessage($user, $chatId, $messageId);
        }
    }
    storeData(STORAGE_PATH . $chatId . '/step.txt', 'none');
}

if ($callbackQuery) {
    $callbackSender = $callbackQuery['from']['id'];
    $callbackData = $callbackQuery['data'];
    $callbackMsgId = $callbackQuery['message']['message_id'];
    
    if (strpos($callbackData, 'reply_') === 0) {
        $replyMsgId = str_replace('reply_', '', $callbackData);
        storeData(STORAGE_PATH . $callbackSender . '/step.txt', 'reply');
        storeData(STORAGE_PATH . $callbackSender . '/reply_to.txt', $replyMsgId);
        dispatchText($callbackSender, 'پاسخت رو بنویس و بفرست ✍️', $backMenu);
        BitCTel('answerCallbackQuery', ['callback_query_id' => $callbackQuery['id'], 'text' => 'آماده پاسخ دادن!']);
    }
}

$allUsers = fetchData(STORAGE_PATH . 'users.txt');
$userList = explode("\n", $allUsers);
if (!in_array($chatId, $userList)) {
    storeData(STORAGE_PATH . 'users.txt', $allUsers . $chatId . "\n");
}

/*
🚀 این سورس کد رو به‌صورت کاملاً رایگان از گنجینه برنامه‌نویسی بیت‌آموز دریافت کردی!  
🎯 جدیدترین سورس‌ها، آموزش‌ها و ابزارهای کاربردی رو همین الان از سایت ما دانلود کن:  
🌐 https://BitAmooz.com  

💡 دوست داری همیشه یک قدم جلوتر باشی؟  
هر روز کلی سورس رایگان، تکنیک‌های برنامه‌نویسی و نکات حرفه‌ای توی بیت‌آموز منتشر میشه!  
⏳ وقتشه که سطح کدنویسی خودتو ارتقا بدی!  
🔗 همین الان وارد سایت شو و سورس‌های بیشتری بگیر: https://BitAmooz.com  
*/
?>