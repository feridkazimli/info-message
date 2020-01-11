<?php 
ob_start();
session_start();
require 'classes/class.info_message.php';
$info = new QaraBala\Info_Message();
if($_POST) {
	if(empty($_POST['ad'])) {
		$info->set_message('error.ad', 'Adinizi daxil edin');
	}

	if(empty($_POST['mail'])) {
		$info->set_message('error.mail', 'Emailinizi daxil edin');
	}

	if(!empty($_POST['ad']) && !empty($_POST['mail'])) {
		$info->set_message('success.save', 'Yaddasa yazildi');
	}

	$info->info_message_run();
}

    // Bütün məlumat mesajlarını göstərmək üçün istifadə edin
	foreach ($info->get_all_message() as $key => $value) {
		print($value['mail']);
	}
	
	// Sadəcə verilən açara görə məlumatı göstərmək üçün istifadə edin
	// print($info->first('mail'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Info Message</title>
</head>
<body>
	<form action="" method="post">
		<input type="text" name="ad" placeholder="Ad">
		<input type="text" name="mail" placeholder="Email">
		<button type="submit">Yoxla</button>
	</form>
</body>
</html>