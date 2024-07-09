<?php
ob_start();
if (headers_sent($filename, $linenum)) {
    echo "Headers already sent in $filename on line $linenum\n";
    exit;
}

$supportedLanguages = ['EN', 'FR']; 
$languageDirectory = __DIR__ . '/../lang/';

$userLanguage = 'EN';
if (isset($_GET["lang"]) && in_array(strtoupper($_GET['lang']), $supportedLanguages)) {
    $userLanguage = strtoupper($_GET['lang']);
    setcookie("lang", $userLanguage, time() + 365 * 24 * 3600, "/"); 
} elseif (isset($_COOKIE["lang"]) && in_array(strtoupper($_COOKIE["lang"]), $supportedLanguages)) {
    $userLanguage = strtoupper($_COOKIE['lang']);
} else {
    $browserLang = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    if (in_array($browserLang, $supportedLanguages)) {
        $userLanguage = $browserLang;
    }
    setcookie("lang", $userLanguage, time() + 365 * 24 * 3600, "/");
}

$userLanguageFile = $languageDirectory . "lang_" . $userLanguage . ".json";

if (file_exists($userLanguageFile)) {
    $file = file_get_contents($userLanguageFile);
    $data = json_decode($file, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON Error: " . json_last_error_msg();
        exit;
    }
} else {
    echo "Language file not found: " . htmlspecialchars($userLanguageFile);
    exit;
}
?>
<script>
function changeLanguage(lang) {
    window.location.href = window.location.pathname + "?lang=" + lang;
}
</script>
