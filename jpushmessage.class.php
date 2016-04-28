<?php
/**
 * 极光推送 - 消息封装类
 * @author lixin@65535@126.com
 *
 */
class jpushmessage{
	
	public static $_PLATFORM_ANDROID = 'android' ;
	public static $_PLATFORM_IOS = 'ios' ;			//如果目标平台为(或包含) iOS 平台 需要在 options 中通过 apns_production 字段来制定推送环境。True 表示推送生产环境，False 表示要推送开发环境； 如果不指定则为推送生产环境
	public static $_PLATFORM_WINPHONE = 'winphone' ; 
	
	
	private $platform = 'all' ;			//推送平台，默认全平台   { "platform" : "all" } / { "platform" : ["android", "ios"] }
	private $audience = 'all' ;			//推送设备对象，默认全部     { "audience" : "all" } / { "audience": { "tag": [ "深圳", "北京" ] } }
    
	private $notification = array() ;	//分平台，通知内容体。是被推送到客户端的内容。与 message 一起二者必须有其一，可以二者并存
	private $message = array() ;		//消息内容体。是被推送到客户端的内容。与 notification 一起二者必须有其一，可以二者并存
	
	private $options = array() ;		//推送参数
	
	private $contentTree = array() ;	//最终生成的内容数组
	
	private $supportPlatforms = array('android', 'ios', 'winphone') ;
	private $errMsg = array() ;			//记录执行中的错误信息
	
	public function __construct( $isProduction=false )
	{
		$this->setIsProduction( $isProduction ) ; //默认测试环境,注意，这个标志只对ios有效，android需要单独设置（比如设置test tag）
	}
	
	public function __get($_key)
	{
		if (in_array($_key, array('audience'))){
			return $this->$_key;
		}
	}
	
	/**
	 * 获取推送体
	 * @return string 格式化后的json串，失败返回''空字符串
	 */
	public function getContent()
	{
		if (empty($this->notification) && empty($this->message))
			return '' ;
		
		if (!empty($this->errMsg))
			return '' ;
		
		$this->contentTree['platform'] = $this->platform ;
		$this->contentTree['audience'] = $this->audience ;
		!empty($this->notification) ? $this->contentTree['notification'] = $this->notification : null ;
		!empty($this->message) ? $this->contentTree['message'] = $this->message : null ;
		!empty($this->options) ? $this->contentTree['options'] = $this->options : null ;
		
		return json_encode($this->contentTree) ;
	}
	
	/**
	 * 设置推送参数
	 * @param string $value
	 * @param string $key
	 */
	public function setOption($value, $key)
	{
		$this->options[$key] = $value;
		
		return $this;
	}
	
	/**
	 * 批量设置推送参数
	 * @param array $options
	 */
	public function setOptions($options)
	{
		//$this->options[$key] = $value;
		if (!is_array($options))
		{
			$this->logError('批量设置推送参数，参数1必须是数组');
			return $this;
		}
		
		foreach ($options as $k=>$v)
		{
			$this->options[$k] = $v;
		}
		
		return $this;
	}
	
	/**
	 * 是否推送到生产环境
	 * @param bool $flag true/false 
	 * @return jpushMessage 支持串接操作
	 */
	public function setIsProduction( $flag ){
		if ($flag){
			$this->options['apns_production'] = true ;
		} else {
			$this->options['apns_production'] = false ;
		}
		
		return $this ;
	}
	
	/**
	 * 设置应用内消息。或者称作：自定义消息，透传消息。 此部分内容不会展示到通知栏上，JPush SDK 收到消息内容后透传给 App。App 需要自行处理。
	 * @param string $title			标题
	 * @param string $msg_content	内容
	 * @param string $content_type	类型 text/audio/xxx
	 * @param array $extras			用户参数，传索引数组
	 */
	public function setMessage($msg_content, $title='', $content_type='', $extras=array()){
		$this->message['msg_content'] = $msg_content ;
		!empty($title) ? $this->message['title'] = $title : null ;
		!empty($content_type) ? $this->message['content_type'] = $content_type : null ;
		!empty($extras) ? $this->message['extras'] = $extras : null ;
		
		return $this ;
	}
	
	/**
	 * 添加通知参数
	 * @param array $value 根据平台不同该参数不同，具体查阅极光手册notification节
	 * @param string $key   all/android/ios/winphone , all会将所有值添加到root层，子节点为各平台独有参数
	 */
	public function addNotification($value, $key='all'){
		
		if (!is_array($value))
		{
			$this->logError('添加通知参数，参数1必须是数组');
			return $this;
		}
		
		if ($key == 'all') {
			foreach ($value as $k=>$v)
			{
				$this->notification[$k] = $v ;
			}
		}else{
			$this->notification[$key] = $value ;
		}
		
		return $this;
	}
	
	/**
	 * 设置移动设备平台
	 * @param string $ps
	 */
	public function setPlatform( $ps='all' ){
		if (empty($ps) || $ps=='all'){
			$this->platform = 'all' ;
		}
		
		if (is_array($ps)){
			$this->platform = array_values($ps);
		} else {
			$this->platform = array( $ps ) ;
		}
		
		return $this ;
	}
	
	/**
	 * 设置推送设备对象
	 * @param array $value
	 * @param string $key
	 */
	public function addAudience( $value , $key ) {
		
		if (!is_array($value))
		{
			$this->logError('推送设备对象，参数1必须是数组');
			return $this;
		}
		
		if (!is_array($this->audience)){
			$this->audience = array() ;
		}
		
		$this->audience[$key] = array_values($value);
		
		return $this ;
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
	
}
?>