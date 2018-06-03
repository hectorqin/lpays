<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
use LPAY\Transfers\TransfersAdapter;
class Utils{
	const BROWSER_IOS=1;
	const BROWSER_WECHAT=2;
	const BROWSER_QQ_IM=3;
	const BROWSER_QQ=4;
	const BROWSER_WEIBO=4;
	const BROWSER_WAP=4;
	const BROWSER_REBOT=4;
	/**
	 * 检测是否是指定类型浏览器
	 * @param int $browser
	 * @param string $user_agent
	 * @return boolean
	 */
	public static function user_agent($browser,$user_agent=null){
		empty($user_agent)&&$user_agent=@$_SERVER['HTTP_USER_AGENT'];
		$status=false;
		switch ($browser){
			case self::BROWSER_IOS:
				if((stripos($user_agent, "iOS")!==false||stripos($user_agent, "iphone")!==false)) return true;
				break;
			case self::BROWSER_WECHAT:
				if(!(strpos($user_agent, 'MicroMessenger') === false)) return true;
				break;
			case self::BROWSER_QQ:
				if(preg_match("/mobile.*qq/is",$user_agent)) return true;
				break;
			case self::BROWSER_QQ_IM:
				if(preg_match("/mqqbrowser/is",$user_agent)) return true;
				break;
			case self::BROWSER_WEIBO:
				if(preg_match("/weibo/is",$user_agent)) return true;
				break;
			case self::BROWSER_WAP:
				if(self::user_agent(self::BROWSER_IOS)
				||self::user_agent(self::BROWSER_WECHAT)
				||self::user_agent(self::BROWSER_QQ)
				||self::user_agent(self::BROWSER_QQ_IM)
				||self::user_agent(self::BROWSER_WEIBO)
				)	return true;
				$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
				$mobile_browser = 0;
				if(!(strpos($user_agent, 'MicroMessenger') === false)){
					$mobile_browser++;
				}
				if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower(@$_SERVER['HTTP_USER_AGENT']))){
					$mobile_browser++;
				}
				if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)){
					$mobile_browser++;
				}
				if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){
					$mobile_browser++;
				}
				if(isset($_SERVER['HTTP_PROFILE'])){
					$mobile_browser++;
				}
				$mobile_ua = strtolower(substr(@$_SERVER['HTTP_USER_AGENT'],0,4));
				$mobile_agents = array(
						'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
						'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
						'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
						'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
						'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
						'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
						'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
						'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
						'wapr','webc','winw','winw','xda','xda-'
				);
				if(in_array($mobile_ua, $mobile_agents)){
					$mobile_browser++;
				}
				if(strpos(strtolower(@$_SERVER['ALL_HTTP']), 'operamini') !== false){
					$mobile_browser++;
				}
				// Pre-final check to reset everything if the user is on Windows
				if(strpos(strtolower($user_agent), 'windows') !== false){
					$mobile_browser=0;
				}
				// But WP7 is also Windows, with a slightly different characteristic
				if(strpos(strtolower(@$_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false){
					$mobile_browser++;
				}
				if($mobile_browser>0) return true;
				else	return false;
				break;
			case self::BROWSER_REBOT:
				if (!empty($user_agent))return true;
				$spiderSite= array(
						"TencentTraveler",
						"Baiduspider+",
						"BaiduGame",
						"Googlebot",
						"msnbot",
						"Sosospider+",
						"Sogou web spider",
						"ia_archiver",
						"Yahoo! Slurp",
						"YoudaoBot",
						"Yahoo Slurp",
						"MSNBot",
						"Java (Often spam bot)",
						"BaiDuSpider",
						"Voila",
						"Yandex bot",
						"BSpider",
						"twiceler",
						"Sogou Spider",
						"Speedy Spider",
						"Google AdSense",
						"Heritrix",
						"Python-urllib",
						"Alexa (IA Archiver)",
						"Ask",
						"Exabot",
						"Custo",
						"OutfoxBot/YodaoBot",
						"yacy",
						"SurveyBot",
						"legs",
						"lwp-trivial",
						"Nutch",
						"StackRambler",
						"The web archive (IA Archiver)",
						"Perl tool",
						"MJ12bot",
						"Netcraft",
						"MSIECrawler",
						"WGet tools",
						"larbin",
						"Fish search",
				);
				foreach($spiderSite as $val) {
					$str = strtolower($val);
					if (strpos($user_agent, $str) !== false) {
						return true;
					}
				}
				break;
		}
		return false;
	}
	/**
	 * 获取库文件路径
	 * @param string $file
	 * @throws Exception
	 */
	public static function lib_path($file){
		$lib_dir=__DIR__."/../../libs/";
		return $lib_dir.$file;
	}
	/**
	 * 比较两个金额
 	 * @param string $money
	 * @param string $moeny1
	 * @return boolean
	 */
	public static function money_equal($money,$moeny1){
		return round(floatval($money),2)==round(floatval($moeny1),2);
	}
	/**
	 * 格式化金额
	 * @param float $money
	 * @return number
	 */
	public static function money_format($money){
		return round(floatval($money),2);
	}
	/**
	 * 获取客户端IP
	 * @return string
	 */
	public static function client_ip(){
		$ip=false;
		if(isset($_SERVER["HTTP_CLIENT_IP"])){
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
			for ($i = 0; $i < count($ips); $i++) {
				if (!preg_match ("/^(10|172\.16|192\.168)\./", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		}
		$ip=$ip ? $ip : (isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null);
		if ($ip=='::1') $ip="127.0.0.1";
		return $ip;
	}
	/**
	 * 创建一个支付ID
	 * @param string $prefix 前缀
	 * @return string
	 */
	public static function snno_create($prefix){
		return $prefix.date("ymdHis").rand(100, 999);
	}
	/**
	 * 计算可提款金额
	 * @param float $money　总金额
	 * @param float $fee　费率
	 * @param number $min_fee_money 最小手续费
	 * @param number $max_fee_money　最大手续费
	 * @return number　可提现金额
	 */
	public static function transfers_money($money,$fee,$min_fee_money=0,$max_fee_money=0){
	    $money=floatval($money);
	    if($fee==0) return $money;
	    if($money<=$min_fee_money) return 0;
	    $min_money=$min_fee_money/$fee;
	    if($money<=$min_money) return $money-$min_fee_money>0?$money-$min_fee_money:0;
	    $max_money=$max_fee_money/$fee;
	    if($money>=$max_money) return $money-$max_fee_money;
	    $pay_fee=$money*$fee/($fee+1);
	    return $money-round($pay_fee,2);
	}
	/**
	 * 计算一个提款费率
	 * @param float $fee　手续费率
	 * @param float $money　付款金额
	 * @param float $min_fee_money　最小手续费
	 * @param float $max_fee_money 最大手续费　0为不限制最大
	 * @return float　需要手续费
	 */
	public static function transfers_fee($fee,$money,$min_fee_money=0,$max_fee_money=0){
	    $pay_fee=$money*$fee;
	    if($pay_fee<$min_fee_money) return $min_fee_money;
	    if ($max_fee_money>0&&$pay_fee>$max_fee_money) return $max_fee_money;
		return round($pay_fee,2);
	}
	/**
	 * 重定向地址
	 * @param string $url
	 */
	public static function redirect_url($url,$code=301){
		if ($code!=301)$code=302;
		if(empty($url))die("redirect url can't be null");
		$url=str_replace(array("\n","\r","\t"), " ", $url);
		if(!headers_sent()){
			header("HTTP/1.1 {$code} Moved Permanently");//这个是说明返回的是301
			header("Location:".$url);//这个是重定向后的网址
		}
		$url=strip_tags($url);
		$url=str_replace("'", "", $url);
		$url=str_replace('"', "", $url);
		echo <<<REDICECTDOC
	<html>
		<head>
		<title>redirect...</title>
		<meta http-equiv="refresh" content="0;url={$url}">
		</head>
		<script>
		window.location.href='{$url}';
		</script>
	<body>redirect...</body>
	</html>
REDICECTDOC;
		die();
	}
	/**
	 * 得到一个加密字符串
	 * @param string $string
	 * @param string $key
	 * @return string
	 */
	public static function encode_url($string,$key){
		$string=trim($string);
		$key=trim($key);
		return $string.'-'.md5($string.$key);
	}
	/**
	 * 解析一个加密字符串
	 * @param string $string
	 * @param string $key
	 * @return NULL|string
	 */
	public static function decode_url($string,$key){
		if(empty($string)) return null;
		$string=trim($string);
		$key=trim($key);
		$hash=substr($string,-32);
		$string=substr($string,0,strlen($string)-33);
		if (md5($string.$key)!=$hash) return null;
		return $string;
	}
	/**
	 * 遍历账单
	 * @param Bill $bill
	 * @return \LPAY\Bill\Result|boolean
	 */
	public static function each_result(Bill $bill){
		if ($bill instanceof \LPAY\Bill\Download){
			$result=$bill->get_data_file()->get_result();
			//遍历完成清除下载文件
			if ($result == false) $bill->get_downloader()->delete(get_class($bill), $bill->get_tag());
			return $result;
		}
		if ($bill instanceof \LPAY\Bill\Data){
			return $bill->get_result();
		}
		return false;
	}
	
}
