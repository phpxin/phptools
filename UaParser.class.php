<?php




class UaParser{



	private $ua ;

	public $os ='' ;
	public $osVersion = '' ;
	public $browser = '' ;
	public $bVersion = '' ;


	public function __construct($ua=null){
		if (!$ua) {
			$this->ua = $_SERVER['HTTP_USER_AGENT'] ;
		}else{
			$this->ua = $ua ;
		}
		
		//$this->ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11';
		$this->parseOs();
		$this->parseBrowser() ;
	}

	public function getRet(){
		return [
			'os' => $this->os ,
			'os_version' => $this->osVersion ,
			'browser' => $this->browser ,
			'browser_version' => $this->bVersion ,

		] ;
	}

	public function parseBrowser(){
		if (preg_match('/(Chrome|Firefox|Opera|Safari)\/([0-9\.]+)/i', $this->ua,  $matcher)) {
			# code...
			//var_dump($matcher) ;
			$this->browser = $matcher[1] ;
			$this->bVersion = $matcher[2] ;
		}

		if (preg_match('/(MSIE) ([0-9\.]+);/i', $this->ua, $matcher)) {
			# code...
			//var_dump($matcher) ;
			$this->browser = $matcher[1] ;
			$this->bVersion = $matcher[2] ;
		}
	}

	public function parseOs(){
		
		if(preg_match('/(iphone|ipad|ipod)/i', $this->ua)){
			$this->os = 'ios' ;
			$this->parseIos() ;
		}else if(preg_match('/(android)/i', $this->ua)){
			$this->os = 'android' ;
			$this->parseAndroid() ;
		}else if(preg_match('/(Macintosh)/i', $this->ua)){
			$this->os = 'osx' ;
			$this->parseOsx() ;
		}else if(preg_match('/(Windows)/i', $this->ua)){
			$this->os = 'windows' ;
			$this->parseWin() ;
		}else{
			$this->parseOther();
		}

		
	}

	public function parseOther(){

		if(preg_match('/(hpwOS|SymbianOS)\/([0-9\_\.]+)/i', $this->ua , $matcher)){
			$this->os = $matcher[1] ;
			$this->osVersion = str_replace('_', '.', $matcher[2]) ;
		}else if(preg_match('/(BlackBerry) ([0-9\_\.]+)/i', $this->ua , $matcher)){
			$this->os = $matcher[1] ;
			$this->osVersion = str_replace('_', '.', $matcher[2]) ;
		}
	}

	public function parseOsx(){
		if(preg_match('/Mac OS X ([0-9\_\.]+)/i', $this->ua, $matcher)){
			$this->osVersion = str_replace('_', '.', $matcher[1]) ;
		}
	}

	public function parseAndroid(){
		if(preg_match('/Android ([0-9\_\.]+)/i', $this->ua, $matcher)){
			$this->osVersion = str_replace('_', '.', $matcher[1]) ;
		}
	}

	public function parseIos(){
		if(preg_match('/(iPad|iPhone).* OS ([0-9\_\.]+) like Mac OS X/i', $this->ua, $matcher)){
			$this->osVersion = str_replace('_', '.', $matcher[2]) ;
		}
	}

	public function parseWin(){
		if(preg_match('/Windows NT (5.1|6.0|6.1|10.0)/i', $this->ua, $matcher)){
			switch ($matcher[1]) {
				case '5.1': $this->osVersion = 'xp' ; break;
				case '6.0': $this->osVersion = 'Vista' ; break;
				case '6.1': $this->osVersion = '7' ; break;
				case '10.0': $this->osVersion = '10' ; break;
				default: break;
			}
		}
		
	}


}