<?php use_helper('I18N');

$text = apAuthLocalUserMain::getPlugin()->getConfigValue('new_password_message', "Hi %username%,\n\nThis e-mail is being sent because you requested a new password.\n\nYour new password is: %password%");

$text = str_replace('%password%', $new_pwd, $text);
$text = str_replace('%username%', $login->getUsername(), $text);

$text = str_replace("\n", "<br/>", $text);
echo $text;

?>


