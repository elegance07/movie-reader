<?php
/**
******************************************************
* @file curl.class.php
* @brief wArLeY_cURL: Use this class to get full webpage, send data, get data and all universe possibilities.
* @author Evert Ulises German Soto
*
* CHANGELOG:
* v1.1: Fix "type" parameter, now allows all methods (GET, POST, PUT, DELETE), default is GET method.
* v1.2: Added two new options: "http_header" to send authentication headers or others and "ssl_verify" to force set to false this value if you need it.
*
* @version 1.0
* @date August 2012
*******************************************************/

class wArLeY_cURL{
	private $err_msg = "";
	private $opt_followlocation = false;
	private $options = array(
		"url" => "",
		"type" => "GET",
		"redirect" => "0",
		"timeout" => "0",
		"referer" => "",
		"return_transfer" => "0",
		"user_agent" => "",
		"header" => "0",
		"http_header" => null,
		"post" => "0",
		"post_fields" => "",
		"data" => "plain",
		"data_filename" => "example.html",
		"ssl_verify" => false,
		"proxy" => "",
		"proxy_userpwd" => "",
		"proxy_type" => CURLPROXY_HTTP //CURLPROXY_SOCKS5
	);

	/** 
	* @brief Constructor, initialize class values.
	* @param array $options, load the required values for the user.
	*/
	public function __construct($options){
		if($options==null){
			$options = $this->options;
		}

		if((string)$options['url']==""){
			$this->err_msg = "Error: the argument url is required.";
			return false;
		}

		foreach($this->options as $c=>$v){
			if(isset($options[$c])) $this->options[$c] = $options[$c];
			if(trim($c)=="redirect" && (integer)$this->options[$c]>0) $this->opt_followlocation = true;
		}
	}

	/** 
	* @brief Execute, this execute the curl function.
	* @return object, this object can be string with full request, or the filename with full request for work with this.
	*/
	public function Execute(){
		$data = $this->options['data'];
		$data_filename = $this->options['data_filename'];

		// Check if cURL installed
		if(!function_exists('curl_init')){
			$this->err_msg = "Error: Sorry cURL is not installed!";
			return false;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->options['url']);
		((string)$this->options['referer']!=="") ? curl_setopt($ch, CURLOPT_REFERER, $this->options['referer']) : curl_setopt($ch, CURLOPT_REFERER, "http://www.gopanga.com/about-me.php");
		((string)$this->options['user_agent']!=="") ? curl_setopt($ch, CURLOPT_USERAGENT, $this->options['user_agent']) : curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 ;Windows NT 6.1; WOW64; Trident/7.0; rv:11.0; like Gecko");
		((string)$this->options['header']==="1") ? curl_setopt($ch, CURLOPT_HEADER, 1) : curl_setopt($ch, CURLOPT_HEADER, 0);
		if(gettype($this->options['http_header'])==="array"){ curl_setopt($ch, CURLOPT_HTTPHEADER, $this->options['http_header']); }
		if((string)$this->options['return_transfer']==="0"){
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		}else{
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		}
		((integer)$this->options['timeout']>0) ? curl_setopt($ch, CURLOPT_TIMEOUT, $this->options['timeout']) : curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		((string)$this->options['type']!="") ? curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->options['type']) : curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

		$fp = "";
		switch($data){
			case "web":
			case "file":
				$fp = fopen($data_filename, "w");
				curl_setopt($ch, CURLOPT_FILE, $fp);
				break;
		}

		if($this->opt_followlocation===true){
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $this->options['redirect']);
		}else{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->options['ssl_verify']);
		if((string)$this->options['proxy']!==""){
			curl_setopt($ch, CURLOPT_PROXYTYPE, $this->options['proxy_type']);
			curl_setopt($ch, CURLOPT_PROXY, $this->options['proxy']);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->options['proxy_userpwd']);
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		}

		if((string)$this->options['post']==="1" && gettype($this->options['post_fields'])==="string"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->options['post_fields']);
		}else{
			curl_setopt($ch, CURLOPT_POST, 0);
		}

		$tmp_output = curl_exec($ch);
		$tmp_error = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		if($httpCode === 404){
			$this->err_msg = "Error: 404, Page Not Found.";
			return false;
		}elseif($httpCode !== 200){
			$this->err_msg = "Error: ". $httpCode .", operation denied".(trim($tmp_error)!='' ? '('.$tmp_error.')' : '').".";
			return false;
		}

		if($tmp_error){
			$this->err_msg = "Error: ". $tmp_error;
			return false;
		}

		if($data!="plain"){ fclose($fp); return $data_filename; }else{ return $tmp_output; }
	}

	/** 
	* @brief getError, get the latest error ocurred in the class.
	* @return string, this is the latest error description.
	*/
	public function getError(){
		return trim($this->err_msg)!="" ? "<span style='display:block;color:#FF0000;background:#FFEDED;font-weight:bold;border:2px solid #FF0000;padding:2px 4px 2px 4px;margin-bottom:5px'>".$this->err_msg."</span><br />" : "";
	}
}

function checker(){ 
GLOBAL $config;

if($config['choice_skin'] == 0){
$theme = $config['skin'];	
}else{
//$choice_skin = $_COOKIE['skin'];
$theme = (!isset($_COOKIE['skin'])?$config['skin']:$_COOKIE['skin']);
}
 
$file = file_get_contents("theme/" . $theme . "/footer.html");
if(!strpos($file, "license")) {
$str = 'PGRpdiBpZD0iYm94ZXMiPg0KICA8ZGl2IHN0eWxlPSJ0b3A6IDE5OS41cHg7IGxlZnQ6IDU1MS41cHg7IGRpc3BsYXk6IG5vbmU7IiBpZD0iZGlhbG9nIiBjbGFzcz0id2luZG93Ij4gPGIgc3R5bGU9J2NvbG9yOnJlZDsnPkFUVEVOVElPTiEhPC9iPg0KICAgIDxkaXYgaWQ9ImxvcmVtIj4NCiAgICAgIDxwPjxiPkxlIGNvcHlyaWdodCBhIMOpdMOpIGVubGV2w6ksIHZldWlsbGV6IGxlIHJlbWV0dHJlIG1lcmNpISEgVmV1aWxsZXogcmVtZXR0cmUgY2UgY29kZSA8PCBlY2hvIGxpY2Vuc2UoKTsgPj4gZGFucyBsZSBmb290ZXIgZGUgdm90cmUgdGjDqG1lLCBwYXIgcmVzcGVjdCBkZSBsJ2F1dGV1ciEhPC9iPjwvcD4NCg0KICAgICAgPHA+PGI+QXVjdW5lIGFpZGUgbmUgc2VyYSBmb3Vybmkgc2kgdm91cyBuZSBsZSByZW1ldHRleiBwYXMhIFNpIHZvdXMgbidhcnJpdmV6IHBhcyDDoCBsZSByZW1ldHRyZSB2ZXVpbGxleiB2b3VzIHJlbmRyZSBpY2kgcG91ciBhdm9pciBkZSBsJ2FpZGU8L2I+PC9wPg0KICAgIDwvZGl2Pg0KICAgIDxkaXYgaWQ9InBvcHVwZm9vdCI+PGI+PGEgaHJlZj0iaHR0cHM6Ly93d3cucGFzc2lvbjJyb3Vlcy5uZXQvIiB0YXJnZXQ9Il9ibGFuayI+UGFjYVByZXogViAyLjEuMDwvYT4gQ29weXJpZ2h0IMKpIDIwMTgtMjAxOSBieSBEYXJrTGFuZTwvYj48L2Rpdj4NCiAgPC9kaXY+DQogIDxkaXYgc3R5bGU9IndpZHRoOiAxNDc4cHg7IGZvbnQtc2l6ZTogMzJwdDsgY29sb3I6d2hpdGU7IGhlaWdodDogNjAycHg7IGRpc3BsYXk6IG5vbmU7IG9wYWNpdHk6IDAuODsiIGlkPSJtYXNrIj48L2Rpdj4NCjwvZGl2Pg==';
echo base64_decode($str);
}
}

	function httpGet($url){
    $ch = curl_init();   
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output=curl_exec($ch);
    curl_close($ch);
    return $output;
}
?>