<?php 
/**
 * İnfo mesajları yaratmaq üçün kiçik bir class
 *
 * PHP version > 5.4
 *
 * LICENSE:  The GNU General Public License is a free, copyleft license for
 * software and other kinds of works.
 * The licenses for most software and other practical works are designed
 * to take away your freedom to share and change the works.  By contrast,
 * the GNU General Public License is intended to guarantee your freedom to
 * share and change all versions of a program--to make sure it remains free
 * software for all its users.  We, the Free Software Foundation, use the
 * GNU General Public License for most of our software; it applies also to
 * any other work released this way by its authors.  You can apply it to
 * your programs, too.
 *
 * @package    Info Message
 * @author     Farid K <ferid.kazimli@gmail.com>
 * @license    https://choosealicense.com/licenses/gpl-3.0/ 
 * @version    1.0.0
 * @link       http://pear.php.net/package/PackageName
 */

namespace QaraBala;

class Info_Message {
	/**
     * Yaradılan məlumat mesajının yolu(ları)
     *
     * @var array
     */
	private array $routes;

	/**
     * Yaradılan məlumatın mesaj mətni(ləri)
     *
     * @var array
     */
	private array $messages;

	/**
	 * Əyər session yaradımayıbsa sessionu başladırıq
     *
     * @access public
     */
	public function __construct()
	{
		if (session_status() == PHP_SESSION_NONE) 
		{
			session_start();
		}
	}

	/**
	 * Mesajımızın məlumatlarını alırıq
     *
     * @param string $route məlumat yolu (exp: info.test)
     * @param string $message məlumatın mətni
     *
     * @access public
     */
	public function set_message(string $route, string $message)
	{
		// $this->parse_routes string məlumatı arraya çeviririk
		$this->routes[] = $this->parse_routes($route);
		$this->messages[] = $message;
	}
	
	/**
	 * Yaradılan mesajları işə salırıq
	 * 
	 * @throws Exception [description]
     * @access public
     */
	public function info_message_run()
	{
		/*
         * Əgər $this->routes arraydısa və ölçüsü 0dan böyükdüsə 
		 * məlumatları sessiona yazırıq
		 * Əyər deyilsə xəta mesajını atırıq
         */
		if(is_array($this->routes) && sizeof($this->routes) > 0) 
		{
			$this->set_session($this->set_info());
		}
		else
		{
			throw new Exception(self::error_message('route'), 1);
		}
	}

	/**
	 * Məlumat(lar)ı yaradırıq
	 * 
	 * @return array     array tipində datanı geri döndəririk
     * @access protected
     */
	protected function set_info()
	{
		foreach ($this->routes as $key => $route) {
			// Mesajın tipini təyin edirik
			$info_type = array_shift($route);
			$info[$info_type][$key] = $this->add_info_message($route, $this->messages[$key]);
	
			$info[$info_type] = $this->up($info[$info_type], $key, 0);
		}

		return $info;
	}

	/**
	 * Məlumat(lar)ı hiyerarşik nizama salıb mesajın mətnini əlavə edirik
	 * 
	 * @param  array     $data array tipində məlumat dataları alınmalıdır
     * @param  string    $message string tipində məlumat mesajı alınmalıdır
	 * @return array     array tipində məlumatı geri döndəririk
     * @access protected
     */
	protected function add_info_message(array $data, string $message)
	{
		$first_key = array_shift($data);
		$info_message[$first_key] = $data;
		/*
         * Əgər $info_message[$first_key] in ölçüsü 0dan böyükdürsə
		 * metodu təkrar işə salırıq
         */
		if(sizeof($info_message[$first_key]) > 0) {
			$info_message[$first_key] = $this->add_info_message($info_message[$first_key], $message);
		}
		/*
         * Əgər $info_message[$first_key] in ölçüsü 0a bərabərdirsə
		 * mesaj mətnini son itemə əlavə edirik
         */
		if(sizeof($info_message[$first_key]) == 0) {
			$info_message[$first_key] = $message;
		}
		return $info_message;
	}

	/**
	 * Array elementini öz üst elemeninin yerinə çıxardırıq
	 * 
	 * @param array  $arr      array tipində məlumatlar alınmalıdır
	 * @param int    $up_where int tipində məlumat alınmalıdır
	 *                         üst elementi seçirik
     * @param int    $which    int tipində məlumat alınmalıdır
	 *                         yuxarı daşınacaq elementi seçirik
	 * @return array array tipində məlumatı geri döndəririk
     * @access protected
     */
	protected function up(array $arr, $up_where, $which)
	{
		if(is_array($arr)) {
			// yuxarı daşınacaq elementi tapırıq
			$find_arr_element = array_slice($arr[$up_where], $which, TRUE);
			// açar adını alırıq
			$key_name = key($find_arr_element);
			// dəyərini alırıq
			$value = current($find_arr_element);
			// göndərilən arrayda yeni element yaradırıq
			$arr[$key_name] = $value;
			// üst elementi yaradılan yeni elementə bərabərləşdirib
			$arr[$up_where] = $arr[$key_name];
			// köhnə üst elementi silirik
			unset($arr[$up_where]);
	
			return $arr;
		}
	}

	/**
	 * Gələn dəyəri nöqtə ayrıcına əsasən parçalayıb
	 * mesaj yolunu yaradırıq
	 * 
	 * @param  string    $route string tipində məlumatı alınmalıdır
	 * @return array     array tipində məlumatı geri döndəririk
     * @access protected 
     */
	protected function parse_routes($route)
	{
		/*
         * Əgər $route tərkibində (.) nöqtə ayrıcı yoxdursa
		 * vəya ayrıcdan sonrakı dəyər boşdursa
		 * xəta mesajını atırıq
         */
		if(!strpos($route, '.') || !explode('.', $route)[1])
		{
			throw new \Exception(self::error_message('separator'), 1);
		}
		else 
		{
			// Verilən dəyəri nöqtə ayırıcına əsasən parçalayırıq
			$parse = explode('.', $route);
			return $parse;
		}
	}

	/**
	 * Sessionu yaradırıq
	 * 
	 * @param  array     $info_data array tipində məlumatı alınmalıdır
	 * @return array     array tipində məlumatı geri döndəririk
     * @access protected 
     */
	protected function set_session(array $info_data)
	{
		$_SESSION['info_message'] = $info_data;
	}

	/**
	 * Bütün mesajları çəkirik
	 * 
	 * @return array  array tipində məlumatı geri döndəririk
     * @access public 
     */
	public function get_all_message() 
	{
		$session = array();
		/*
         * Əgər $_SESSION['info_message'] varsa
		 * $_SESSION['info_message'] nu $session a bərabərləşdirib
		 * $_SESSION['info_message'] ı silirik
         */
		if(isset($_SESSION['info_message'])) 
		{
			$session = $_SESSION['info_message'];
			unset($_SESSION['info_message']);  
		}

		return $session;
	}

	/**
	 * Verilən məlumat mesajarında istənilən açar sözə əsasən ilə məlumatı alırıq
	 * 
	 * @param  string $key_name string tipində məlumatı alınmalıdır
	 * @return array  array tipində məlumatı geri döndəririk
     * @access public 
     */
	public function first($key_name)
	{
		$session = array();
		/*
         * Əgər $_SESSION['info_message'] varsa
		 * Əgər $_SESSION['info_message'] da $key_name sütunu varsa varsa
		 * $key_name sütununu $session a bərabərləşdirib
		 * $_SESSION['info_message'] ı silirik
         */
		if(isset($_SESSION['info_message'])) 
		{
			if($key_name = array_column($_SESSION['info_message'], $key_name))
			{
				$session = current($key_name);
				unset($_SESSION['info_message']);  
			}
			else 
			{
				throw new \Exception(self::error_message('not_find_column'), 1);
			}
		}

		return $session;
	}

	/**
	 * Verilən məlumat mesajarında istənilən açar sözə əsasən ilə məlumatı alırıq
	 * 
	 * @param  string    $error_code string tipində məlumat alınmalıdır
	 *                               istənilən xətanın kodunu daxil etməlisiniz
	 * @return array     array tipində məlumatı geri döndəririk
     * @access protected 
     */
	protected function error_message(string $error_code)
	{
		switch ($error_code) {
			case 'separator':
				return 'Bilinməyən məlumat daxil etmisiz: Ayırıc istifadə olunmayıb.';
				break;
			
			case 'not_find_column':
				return 'Daxil etdiyiniz açar massivdə mövcud deyil.';
				break;

			case 'route':
				return 'Məlumat yolu hiyerarşiyası xətalıdır.';
				break;	
			default:
				return 'Xəta kodu mövcud deyil.';
				break;	
		}
	}

}


?>