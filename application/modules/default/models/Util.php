<?php

class Fof_Model_Util {

	public static function deleteDir($dir) {
		$iterator = new RecursiveDirectoryIterator($dir);
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
			if ($file->isDir()) {
				rmdir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}
		rmdir($dir);
	}
	
	public static function padScriptureReference($reference){
		$paddedRef = '';
		$referenceArray = explode('.', $reference);
		foreach ($referenceArray as $index => $part){
			if ($index != 0){
				$paddedRef .= '.';
			}
			$paddedRef .= str_pad($part, 3, '0', STR_PAD_LEFT);
		}
		return $paddedRef;
	}

	/**
	 * Saves the guest email to the guest_email table.
	 * @param string $email
	 */
	public static function saveGuestEmail($email) {
		$dbtable = new Fof_Model_Dbtable_GuestEmail();
		$data = array('email'   => $email);
		$dbtable->insert($data);
	}

	/**
	 * This function encrypts, then encodes a string.
	 * @param string $string
	 */
	public static function encodeEncrypt($string) {
		return preg_replace('/=+$/','',base64_encode(pack('H*',md5($string))));
	}

	/**
	 * This function takes a string and camel cases it based on the "_" character.
	 * @param string $str
	 * @param boolean $capitalise_first_char
	 */
	public static function toCamelCase($str, $capitalise_first_char = false) {
		if($capitalise_first_char) {
			$str[0] = strtoupper($str[0]);
		}
		$func = create_function('$c', 'return strtoupper($c[1]);');
		return preg_replace_callback('/_([a-z])/', $func, $str);
	}

	/**
	 * This function creates a random string of a given length from the given characters.
	 * @param int $length
	 * @param string $chars
	 */
	public static function getRandomString($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		$chars_length = (strlen($chars) - 1);
		$string = $chars{rand(0, $chars_length)};
		for ($i = 1; $i < $length; $i = strlen($string)){
			$r = $chars{rand(0, $chars_length)};
			if ($r != $string{$i - 1}) {
				$string .=  $r;
			}
		}
		return $string;
	}

	/**
	 * This function adds an email address to the campaign monitor list.
	 * @param string $email
	 * @param string $name
	 */
	public static function addUserToCampaignList($email, $name) {
		$client = new Zend_Http_Client('http://api.createsend.com/api/api.asmx/Subscriber.AddAndResubscribe?ApiKey=' . Fof_Model_Util::getProperty('cm_api_key') . '&ListID=' . Fof_Model_Util::getProperty('cm_list_id') . '&Email=' .$email . '&Name=' . urlencode($name));
		$response = $client->request();
	}

	/**
	 * This function removes an email address from the campaign monitor list.
	 * @param string $email
	 */
	public static function removeUserFromCampaignList($email) {
		$client = new Zend_Http_Client('http://api.createsend.com/api/api.asmx/Subscriber.Unsubscribe?ApiKey=' . Fof_Model_Util::getProperty('cm_api_key') . '&ListID=' . Fof_Model_Util::getProperty('cm_list_id') . '&Email=' . $email);
		$response = $client->request();
	}

	/**
	 * Gets the property from the application.ini file.
	 * @param string $key
	 */
	public static function getProperty($key){
		return $application = Zend_Registry::getInstance()->get('application')->getOption($key);

	}

	/**
	 * Converts a date that is in the MySQL format to a specified date format.
	 * @param string $date
	 * @param string $format
	 */
	public static function convertMySQLDateToDateStringWithFormat($date, $format) {
		$months = array('January','February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$returnDate = '';
		if ($date) {
			$dateParts = explode('-', $date);
			if ($format == 'DD/MM/YYYY'){
				$returnDate = $dateParts[2].'/'.$dateParts[1].'/'.$dateParts[0];
			} else if ($format == 'MMM DD, YYYY'){
				$returnDate = $months[(int) ($dateParts[1] -1)].' '.$dateParts[2].', '.$dateParts[0];
			} else if ($format == 'MM/DD'){
				$returnDate = $dateParts[1].'/'.$dateParts[2];
			} else {
				$returnDate = $dateParts[1].'/'.$dateParts[2].'/'.$dateParts[0];
			}

		}
		return $returnDate;
	}

	/**
	 * This function surrounds a given string with something in the front and back.
	 * @param string $string
	 * @param string $find
	 * @param string $front
	 * @param string $back
	 * @param boolean $ignorecase
	 */
	public static function surroundText($string, $find, $front, $back, $ignorecase) {
		if ($ignorecase){
			$string = preg_replace("/($find)/i", $front."$1".$back, $string);
		} else {
			$string = preg_replace("/($find)/", $front."$1".$back, $string);
		}
		return $string;
	}

	/**
	 * Increment the current date by a specified number (can be negative)
	 * @param int $days
	 */
	public static function incrementCurrentDate($days = 1){
		$thedate = mktime(0, 0, 0, date("m"), date("d")+$days, date("y"));
		return date('Y-m-d', $thedate);

	}

	public static function incrementGivenDate($givenDate, $days = 1){
		return date("Y-m-d", (strtotime(date("Y-m-d", strtotime($givenDate)) . " +".$days." day")));
		
	}
	
	/**
	 * Gets the current date
	 */
	public static function getCurrentDate(){
		return date('Y-m-d');
	}

	public static function getCurrentYear(){
		return date('Y');
	}

	/**
	 * Gets the current date/time
	 */
	public static function getCurrentDateTime(){
		return date('Y-m-d H:i:s');
	}

	/**
	 * Gets the base directory for the application
	 *
	 */
	public static function getBaseDir()
	{
		return BASE_PATH;
	}

	/**
	 * Loads up all of the system examens for a certain date.
	 * @param string $date
	 */
	public static function loadSystemExamens($date = null){
		if (is_null($date)){
			$date = Fof_Model_Util::getCurrentDate();
		}
		$examenType = new Fof_Model_ExamenType();
		$systemExamenTypes = $examenType->fetchSystemExamenTypesWithScriptureByExamenTrackType('date');
		foreach($systemExamenTypes as $type){
			if ($type->getScripture() == null || ($type->getScripture()->getScriptureReference() == null && strpos($type->getScripture()->getScriptureHtml(), 'ERROR') === false)){
				$examen = new Fof_Model_Examen();
				$examen->addData(
				array(
					'examen_type_id' => $type->getExamenTypeId(),
					'description' => $type->getExamenTypeUniqueName() . ' - ' . $date
				)
				);
				$examen->save();
					
				$defaultQuestions = new Fof_Model_ExamenQuestionsDefault();
				$defaultQuestionsArray = $defaultQuestions->fetchAllByExamenTypeId($type->getExamenTypeId());
				foreach($defaultQuestionsArray as $defaultQuestion){
					$examenQuestion = new Fof_Model_ExamenQuestions();
					$examenQuestion->setExamenId($examen->getExamenId());
					$examenQuestion->setSequence($defaultQuestion->getSequence());
					$examenQuestion->setQuestion($defaultQuestion->getQuestion());
					$examenQuestion->setPrompt($defaultQuestion->getPrompt());
					$examenQuestion->setShowScripture($defaultQuestion->getShowScripture());
					$examenQuestion->save();
				}
					
				$method = 'load' . self::toCamelCase($type->getExamenTypeUniqueName(), true);
				if (method_exists('Fof_Model_Util', $method)){
					self::$method($date, $examen->getExamenId());
				}
				$track = new Fof_Model_ExamenTrackDate();
				$track->setExamenTypeId($type->getExamenTypeId());
				$track->setExamenId($examen->getExamenId());
				$track->setDate($date);
				$track->save();
			}
		}
	}

	/**
	 * Gets and saves the scripture for the new testiment examen
	 * @param string $date
	 * @param int $examenId
	 */
	public static function loadScriptureNt($date, $examenId){
		$scriptureCalendar = self::getScriptures($date, 2, 'end');
		$scriptureCalendar->setExamenId($examenId);
		$scriptureCalendar->save();

	}

	/**
	 * Gets and saves the scripture for the old testiment examen
	 * @param string $date
	 * @param int $examenId
	 */
	public static function loadScriptureOt($date, $examenId){
		$scriptureCalendar = self::getScriptures($date, 3, 'end');
		$scriptureCalendar->setExamenId($examenId);
		$scriptureCalendar->save();

	}

	/**
	 * Gets and saves the scripture for the psalm examen
	 * @param string $date
	 * @param int $examenId
	 */
	public static function loadScripturePs($date, $examenId){
		$scriptureCalendar = self::getScriptures($date, 0, 'start');
		$scriptureCalendar->setExamenId($examenId);
		$scriptureCalendar->save();
	}

	/**
	 * Gets and saves the scripture for the gospel examen
	 * @param string $date
	 * @param int $examenId
	 */
	public static function loadScriptureGospel($date, $examenId){
		$scriptureCalendar = self::getScriptures($date, 1, 'end');
		$scriptureCalendar->setExamenId($examenId);
		$scriptureCalendar->save();
	}

	/**
	 * Gets the scripture from the ESV site.
	 * @param string $date
	 * @param int $scripturePosition The position of the type of scripture when ripped from the page and put into the array.
	 * @param string $startPosition Look for the $scripturePosition from the start or end.
	 */
	protected static function getScriptures($date, $scripturePosition, $startPosition){
		$settings = array('timeout'=>30);


		//$date = Fof_Model_Util::getCurrentDate();
		$site = 'http://www.gnpcb.org/esv/devotions/bcp/?date='.$date;
		$client = new Zend_Http_Client($site, $settings);
		$response = $client->request();

		$html = $response->getBody();
		$startHrIndex = strpos($html, '<h3>') + 4;
		$startIndex = strpos($html, '<br />', $startHrIndex) + 6;
		$endIndex = strpos($html, '</h3>');
		$scriptures = substr($html, $startIndex, ($endIndex - $startIndex));
		$scriptureArray = explode(';', $scriptures);
		$currentScripture;

		if ($startPosition == 'end'){
			$currentScripture = $scriptureArray[count($scriptureArray) - $scripturePosition];
		} else if ($startPosition == 'start'){
			$currentScripture = $scriptureArray[$scripturePosition];
		}

		$currentScripture = str_replace('(', ',', $currentScripture);
		$currentScripture = str_replace(')', ',', $currentScripture);
		$currentScripture = str_replace('[', '', $currentScripture);
		$currentScripture = str_replace(']', '', $currentScripture);
		$currentScripture = str_replace(',,', ',', $currentScripture);
		$currentScripture = str_replace(', ,', ',', $currentScripture);
			
		if (',' == substr($currentScripture, strlen($currentScripture) -1)){
			$currentScripture = substr($currentScripture, 0, strlen($currentScripture) -1);
		}

		$html = self::getEsvHtmlScripture($currentScripture);

		$text = self::getEsvTextScripture($currentScripture);

		$scriptureCalendar = new Fof_Model_ExamenScripture();
		$scriptureCalendar->setScriptureReference(trim($currentScripture));
		$scriptureCalendar->setScriptureHtml($html);
		$scriptureCalendar->setScriptureText($text);

		return $scriptureCalendar;
	}

	public static function getEsvHtmlScripture($scriptureReference){
		$settings = array('timeout'=>30);
		$site = 'http://www.esvapi.org/v2/rest/passageQuery?key=fa3fcb1877bb3241&include-headings=false&include-verse-numbers=true&include-footnotes=false&passage='.urlencode($scriptureReference);
		$client = new Zend_Http_Client($site, $settings);
		$html = $client->request()->getBody();
		return $html;
	}

	public static function getEsvTextScripture($scriptureReference){
		$settings = array('timeout'=>30);
		$site = 'http://www.esvapi.org/v2/rest/passageQuery?key=fa3fcb1877bb3241&line-length=100&include-headings=false&include-first-verse-numbers=true&output-format=plain-text&include-verse-numbers=true&include-footnotes=false&include-passage-horizontal-lines=false&include-heading-horizontal-lines=false&include-headings=false&include-passage-references=false&passage='.urlencode($scriptureReference);
		$client = new Zend_Http_Client($site, $settings);
		$text = $client->request()->getBody();
		return $text;
	}

	/**
	 * Sends the administrators an email if an error occured.
	 * @param string $message
	 * @param string $location
	 */
	public static function sendAdminEmail($message, $location = 'Undefined'){
		$mail = new Zend_Mail();
		$mail->setBodyText("[Location: $location] " . $message);
		$mail->setFrom('messages@examen.me', 'Examen.me');
		$mail->addTo('brobie@gmail.com', 'Ben Robie');
		$mail->addTo('brentminter@gmail.com', 'Brent Minter');
		$mail->setSubject('An Error Has Occured On Examen.me');
		$mail->send();
	}

	/**
	 * Logs an action made by the user on the site.
	 * @param string $action
	 * @param array $data
	 */
	public static function logUserAction($action, $data = ''){
		$userId = 0;
		if (Zend_Auth::getInstance()->hasIdentity()){
			$userId = Zend_Auth::getInstance()->getStorage()->read()->user_id;
		}
		$logging = new Fof_Model_Logging();
		$logging->setIp($_SERVER['REMOTE_ADDR']);
		$logging->setAgent($_SERVER['HTTP_USER_AGENT']);
		$logging->setUserId($userId);
		$logging->setDate(date("Y-m-d"));
		$logging->setTime(date("H:i:s"));
		$logging->setAction($action);
		$logging->setLoggingData($data);
		$logging->save();
	}


	/**
	 * This function creates a Zend_Log with a writer
	 *
	 * @param string $msg         to be written to the writer
	 * @param string $loggingFile The file to be written to (Default: logging.txt)
	 *
	 * @return nothing
	 */
	public static function log($msg, $loggingFile = 'logging.txt')
	{
		if ( self::getProperty('logging_active') == 1 ){
			try {
				$stack = debug_backtrace();
				$class = $stack[1]['class'];
				$function = $stack[1]['function'];

				// Building the Directory and File name
				$loggingDirectory = self::getProperty('logging_directory');

				// If the directory does not exist, create it
				if (!is_dir($loggingDirectory)) {
					mkdir($loggingDirectory, 0777);
				}


				// IF the file does not exist, create it
				if (!file_exists($loggingDirectory.$loggingFile)) {
					file_put_contents($loggingFile, '');
					chmod($loggingFile, 0777);
				}

				// create the writere for the log
				$writer = new Zend_Log_Writer_Stream($loggingDirectory.$loggingFile);

				// create a logger
				$logger = new Zend_Log($writer);

				
				$filterEnabled = self::getProperty('logging_filter_enabled');
				$classFilter = self::getProperty('logging_class_filter');
				$msgFilter = self::getProperty('logging_message_filter');
				
				// Write the message to the writer
				if ($filterEnabled){
					if ((!$classFilter || strpos(strtolower($class), strtolower($classFilter)) !== false)
					     && (!$msgFilter || strpos(strtolower($msg), strtolower($msgFilter)) !== false)){
						$logger->info(str_pad($class, 30) . '||' . str_pad($function , 25). '||' . $msg );
					}
				} else {
					$logger->info(str_pad($class, 30) . '||' . str_pad($function , 25). '||' . $msg );
				}

			} catch (Exception $e){

			}
		}
	}

	/**
	 * Puts the Set Id -> Examen Id mapping into session.
	 * @param int $setId
	 * @param int $examenId
	 */
	public static function startGuestUserExamen($setId, $examenId){
		$session = new Zend_Session_Namespace('user_examen_set_session');
		$sets = array();
		if (isset($session->sets)){
			$sets = $session->sets;
		}
		$sets[$setId] = $examenId;
		$session->sets = $sets;
	}

	/**
	 * Gets the Examen Id using the Set Id from session.
	 * @param int $setId
	 */
	public static function getGuestExamenIdBySet($setId){
		$session = new Zend_Session_Namespace('user_examen_set_session');
		if (isset($session->sets)){
			$sets = $session->sets;
			if (isset($sets[$setId])){
				$examenId = $sets[$setId];
				return $examenId;
			}
		}
		return false;
	}

	/**
	 * Helps to save the guest users answer to the correct place in session.
	 * @param Fof_Model_UserExamenResponse $response
	 * @param Fof_Model_Examen $examen
	 */
	public static function saveGuestUserAnswer(Fof_Model_UserExamenResponse $response,  Fof_Model_Examen $examen){
		$session = new Zend_Session_Namespace('user_examen_response_session');
		$responses = array();
		if (isset($session->responses)){
			$responses = $session->responses;
		}
		$examenresponses = array();
		if (isset($responses[$examen->getExamenId()])){
			$examenresponses = $responses[$examen->getExamenId()];
		}
		$examenresponses[$response->getSequence()] = $response;
		$responses[$examen->getExamenId()] = $examenresponses;
		$session->responses = $responses;
	}

	/**
	 * Gets the guest user's response from the correct place in session
	 * @param Fof_Model_UserExamenResponse $response
	 * @param unknown_type $sequence
	 * @param Fof_Model_Examen $examen
	 */
	public static function getGuestUserQuestion(Fof_Model_UserExamenResponse $response, $sequence, Fof_Model_Examen $examen){
		//$question = new Fof_Model_UserExamenQuestion();
		$session = new Zend_Session_Namespace('user_examen_response_session');
		if (isset($session->responses)){
			$responses = $session->responses;
			if (isset($responses[$examen->getExamenId()])){
				$examenresponses = $responses[$examen->getExamenId()];
				if (isset($examenresponses[$sequence])){
					$response = $examenresponses[$sequence];
				}
			}
		}
		$response = unserialize(serialize($response));
		if ($response->getQuestion() == null && $response->getPrompt() == null){
			return $examen->getQuestionBySequence($sequence);

		}
		return $response;
	}

	/**
	 * Gets all of the guest user's responses for an examen
	 * @param int $examenId
	 */
	public static function getGuestUserQuestions($examenId, $removeFromSession = false)
	{
		$session = new Zend_Session_Namespace('user_examen_response_session');
		if (isset($session->responses)){
			$responses = $session->responses;
			if (isset($responses[$examenId])){
				$examenresponses = $responses[$examenId];
				if ($removeFromSession){
					unset($responses[$examenId]);
					$session->responses = $responses;
				}
				return $examenresponses;
			}
		}
		return false;
	}
	/**
	 * Shortens a string to a given length.
	 * 
	 * @param string $string
	 * @param int $characterNumber
	 */
	public static function getShortenedString($string, $characterNumber = null){
		if (strlen($string) > $characterNumber && !is_null($characterNumber)){
			return substr($string, 0, $characterNumber) . '...';
		}
		return $string;
	}
	
	public static function getCacheValue($key){
		$cache = Zend_Registry::get('cache');
		return $cache->load($key);
	}
	
	public static function setCacheValue($key, $value){
		if (self::getProperty('cache_enabled')){
			$cache = Zend_Registry::get('cache');
			$cache->save($value, $key);
		}
	}
	
	public static function cleanOldCache(){
		$cache = Zend_Registry::get('cache');
		$cache->clean(Zend_Cache::CLEANING_MODE_OLD);
	}
	
	public static function isMobile() {
		$is_mobile = '0';
	
		if(preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$is_mobile=1;
		}
	
		if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$is_mobile=1;
		}
	
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		$mobile_agents = array('w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-');
	
		if(in_array($mobile_ua,$mobile_agents)) {
			$is_mobile=1;
		}
	
		if (isset($_SERVER['ALL_HTTP'])) {
			if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
				$is_mobile=1;
			}
		}
	
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
			$is_mobile=0;
		}
	
		return $is_mobile;
	}
	
}