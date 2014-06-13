<?php $validateurl = url_for('ap_authlocaluser_validate', $login, true); 

$text = apAuthLocalUserMain::getPlugin()->getConfigValue('validation_message', "You must validate your account.  To do so, please click on the link below: %url%");
if (strpos($text, '%url%') !== FALSE) {
    $text = str_replace('%url%', $validateurl, $text);
}
else {
    $text .= "  $validateurl";
}
$text = str_replace("\n", "<br/>", $text);
echo $text;

?>

