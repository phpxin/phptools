<?php
/**
 * 极光推送 - 推送基础类
 * @author lixin@65535@126.com
 * 
 */
define('JPUSH_WEBSITE', 'https://api.jpush.cn/v3/push') ;
define('JPUSH_REPORTURL', 'https://report.jpush.cn/v3/received') ;
define('UTHING_JP_KEY', '') ;
define('UTHING_JP_MS', '') ;

class jpush{
	
	private $appKey ;
	private $masterSecret ;
	
	private $errMsg = array() ;	//记录执行中的错误信息
	private $msgId = 0 ;		//推送后极光返回的msgid
	
	/*
	public function __construct($appKey, $masterSecret)
	{
		$this->appKey = $appKey ;
		$this->masterSecret = $masterSecret ;
	}
	*/
	
	public function __construct()
	{
		$this->appKey = UTHING_JP_KEY ;
		$this->masterSecret = UTHING_JP_MS ;
	}
	
	/**
	 * 记录错误
	 * @param string $errMsg 错误信息
	 */
	private function logError( $errMsg )
	{
		$this->errMsg[] = $errMsg ;
	}
	
	/**
	 * 获取全部错误信息
	 * @return array 错误信息
	 */
	public  function getErrorMsg()
	{
		return $this->errMsg ;
	}
	
	/**
	 * 获取最后发送的推送返回的消息ID
	 * @return string 极光返回的msgid
	 */
	public function getLastMsgId()
	{
		return $this->msgId ;
	}
	
	
	/**
	 * 解析极光返回值
	 * 
	 * @param string $response 内容
	 * @return bool true/false  请求成功返回 true/失败 false
	 */
	public function parseResponse($response)
	{
		$responseMsg = json_decode($response, true);
		
		if (!empty($responseMsg['msg_id'])){
			$this->msgId = $responseMsg['msg_id'];
		}else {
			$this->logError("request failed raw data : ".$response) ;
			return false;
		}
		
		return true;
	}
	
	/**
	 * 获取极光推送结果 
	 * @param array $msgids 以英文半角逗号分隔的极光msgid
	 * @return 失败返回false, 成功返回结果数组
	 */
	public function report( $msgids )
	{
		if(empty($msgids)){
			return false;
		}
		
		if(count($msgids)>50){
			$msgids = array_slice($msgids, 0, 50);
		}
		
		$url = JPUSH_REPORTURL.'?msg_ids='.implode(',',$msgids);
		//var_dump($url);
		$authorization = base64_encode($this->appKey.':'.$this->masterSecret);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url) ;
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false) ;
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true) ;
		curl_setopt($curl, CURLOPT_USERPWD, $this->appKey.':'.$this->masterSecret);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json','Authorization: Basic '.$authorization)) ;

		$response = curl_exec($curl) ;
		
		if(curl_errno($curl)){
			$this->logError(curl_error($curl));
			return false;
		}
		
		return json_decode($response, true);
	}
	
	/**
	 * 发送一个jpush请求
	 * @param string $msg 消息体 
	 * @return bool true/false  请求成功返回 true/失败 false
	 */
	public function send( $msg )
	{
		if (empty($msg))
		{
			$this->logError("消息体没有内容") ;
			return false;
		}
		
		$authorization = base64_encode($this->appKey.':'.$this->masterSecret);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, JPUSH_WEBSITE) ;
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false) ;
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true) ;
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_USERPWD, $this->appKey.':'.$this->masterSecret);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json','Authorization: Basic '.$authorization)) ;

		curl_setopt($curl, CURLOPT_POSTFIELDS, $msg);	//'{"platform":"all","audience":"all","notification":{"alert":"Hi,Lx!"}}'
		
		$response = curl_exec($curl) ;
		
		if(curl_errno($curl)){
			$this->logError(curl_error($curl));
			return false;
		}
		
		
		return $this->parseResponse($response);
	}
	
}

?>