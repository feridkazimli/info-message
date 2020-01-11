# info-message
**Php ilə müvəqqəti məlumat mesajlarları yaratmaq üçün kiçik bir sinif.**

**Codeİgniter** vəya **Laravel** istifadə edənlər bilər, misal istifadəçi bir form doldurur və müəyyən bir səhifəyə yönləndiririk, orada göndərilən məlumatları yoxladıqdan sonra, əyər xəta varsa istifadəçini təkrar form səhifəsinə yönləndirib və xəta mesajını ekrana yazırıq. Və səhifə yeniləndiyi zamanda bu mesaj silinir. Hazırladığım bu sinif vasitəsiylə kiçik proyektlərinizdə rahatlıqla eyni sistemi istifadə edə bilərsiz. İstifadəsi olduqca rahatdır.

    // Sinifi daxil edirik və başladırıq
    require  'classes/class.info_message.php';
    $info = new QaraBala\Info_Message();
    if(isset($_POST['ad']) && empty($_POST['ad'])) {
        // mesajımızı yaradırıq
        // ilk parametrimiz məlumatını yoludur
        // əvvəla məlumatın tipini sonra yolunu qeyd edirik
        // ikinci parametrdə isə mətnimizi daxil edirik
		$info->set_message('error.ad', 'Adinizi daxil edin');
		$info->info_message_run();
	}

Yaranan bütün məlumat mesajlarını göstərmək üçün:

    // Bütün məlumat mesajlarını göstərmək üçün istifadə edin
	foreach ($info->get_all_message() as $info) {
		print($info['ad']);
	}
Sadəcə verilən açara görə məlumatı göstərmək üçün:

    Sadəcə verilən açara görə məlumatı göstərmək üçün istifadə edin
    print($info->only('mail'));

İstədiyiniz kimi sinifi genişlədə bilərsiniz.
