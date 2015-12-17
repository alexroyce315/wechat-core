<?php  defined('BASEPATH') OR('No direct script access allowed');

/**
 * Wechatpay
 * 微信支付核心类
 * @package wechat-v2
 * @author alex.royce315@gmail.com
 * @copyright 2015
 * @version $Id$
 * @access public
 */
class Wechatpay {
    
    var $url            = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    var $mchId          = '';
    var $appId          = '';
    var $nofityUrl      = '';
    var $key            = '';
    var $certPath       = '';
    var $keyPath        = '';
    //=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
    var $reportLevel    = 1;
    var $curlTimeout    = 3;

    /**
     * 全部选项(不包括 sign)
     */
    protected $defined = array(
        'appid', 'mch_id', 'device_info', 'nonce_str', 'body',
        'detail','attach', 'out_trade_no', 'fee_type', 'total_fee',
        'spbill_create_ip', 'time_start', 'time_expire', 'goods_tag',
        'notify_url', 'trade_type', 'product_id', 'limit_pay', 'openid'
    );

    /**
     * 必填项目(不包括 sign)
     */
    protected $required = array(
        'appid', 'mch_id', 'nonce_str', 'body', 'out_trade_no',
        'total_fee', 'spbill_create_ip', 'notify_url', 'trade_type'
    );
    
    /**
     * 必传项目(不包括 sign)
     */
    //protected $requireBody  = array(
    //    'body', 'out_trade_no', 'total_fee', 'trade_type'
    //);

    /**
     * 有效的 trade_type 类型
     */
    protected $tradeType = array('JSAPI', 'NATIVE', 'APP', 'WAP');

    /**
     * Constructor
     *
     * @access	public
     * @param	array	initialization parameters
     */
    public function __construct($params = array()){
        if (count($params) > 0) {
            $this->initialize($params);
        }
        log_message('info', "Wechatpay Class Initialized @ " . current_url());
    }

    // --------------------------------------------------------------------

    /**
     * Initialize Preferences
     *
     * @access	public
     * @param	array	initialization parameters
     * @return	void
     */
    public function initialize(array $params = array()){
        foreach ($params as $key => $val) {
            if (isset($this->$key)) {
                $this->$key = $val;
            }
        }
    }
    
    /**
     * Wechatpay::checkRequire()
     * 检查提交参数的必填项
     * @param string $type report/nativeOrder/unifiOrder/refund/bill/micropay
     * @param array() $data
     * @param bool $return
     * @return
     */
    private function checkRequire($type = 'unifiOrder', $data, $return = FALSE){
        //检测必填参数
        $requireContent = array();
        
        switch($type){
            case 'report':
                $requireContent = array('interface_url','return_code','result_code','user_ip','execute_time_');
                break;
            case 'nativeOrder':
                $requireContent = array('prepay_id');
                break;
            case 'unifiOrder':
            default:
                $requireContent = array('body','out_trade_no','total_fee','trade_type');
                break;
            case 'refund':
                $requireContent = array('out_refund_no','total_fee','refund_fee','op_user_id');
                break;
            case 'bill':
                $requireContent = array('bill_date');
                break;
            case 'micropay':
                $requireContent = array('body', 'out_trade_no', 'total_fee', 'auth_code');
                break;
        }
        
        $_temp  = array();
        
        foreach($requireContent as $key => $val){
            if(!isset($data[$val]) || '' == $data[$val]){
                log_message('error', '缺少 '.$type.':'.$this->url.' 接口必填参数 '.$val.'!');
                die('缺少 '.$type.':'.$this->url.' 接口必填参数 '.$val.'!');
            } else{
                !$return || $_temp[$val]    = $data[$val];
            }
        }
        
        if($return) return $_temp;
    }
    
	/**
	 * Wechatpay::getPayUrl()
	 * 生成微信扫码支付模式二的地址
	 * @param mixed $data
	 * @return
	 */
	public function getPayUrl($data){
		// 生成签名，并提交 xml 到微信，解析返回的 xml
        $data   = $this->getResult($this->createUnifiedOrderXml($data));
        // 提取 prepay_id
        return 'SUCCESS' === $data['result_code'] ? $data['code_url'] : FALSE;
	}
	
	/**
	 * Wechatpay::getPrepayId()
	 * 获取 unifieOrder prepay_id
	 * @param array $data 包含必传参数的数组
	 * @return 预支付编号
	 */
	public function getPrepayId($data){
        // 生成签名，并提交 xml 到微信，解析返回的 xml
        $data   = $this->getResult($this->createUnifiedOrderXml($data));
        // 提取 prepay_id
        // 2015.12.14 解决针对已支付订单的处理
        if('OK' === $data['return_msg']){
            return 'SUCCESS' === $data['result_code'] ? $data['prepay_id'] : array(
                'result'    => 'PAYED'
            );
        } else{
            return FALSE;
        }
	}
    
	/**
	 * Wechatpay::createUnifiedOrderXml()
	 * 生成 unifieOrder xml，包含必填参数检查动作
	 * @param array $data
	 * @return post xml 格式
	 */
	private function createUnifiedOrderXml($data = array()){
        if(empty($data)){
            die('Error unifieOrder with empty data');
        }
        
		try{
            //检测必填参数
            $this->checkRequire('unifiOrder', $data);
            //foreach($this->requireBody as $key => $val){
            //    if(!isset($data[$val]) || '' == $data[$val]){
            //        die('缺少统一支付接口必填参数 '.$val.'!');
            //    }
            //}
            
            // JSAPI 下 openid 必填
            if('JSAPI' === $data['trade_type'] && (!isset($data['openid']) || '' == $data['openid'])){
                die('统一支付接口中，缺少必填参数 openid！trade_type 为 JSAPI 时，openid 为必填参数!');
            }
            
            // WAP 下 product_id 必填
            if('WAP' === $data['trade_type'] && (!isset($data['product_id']) || '' == $data['product_id'])){
                die('统一支付接口中，缺少必填参数 product_id！trade_type 为 WAP 时，product_id 为必填参数!');
            }
            
            $this->url  = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
            
            $parameter              = array_merge($data, $this->getParameter(), array(
                'notify_url'        => $this->nofityUrl,        // 通知异步回调地址
                'spbill_create_ip'  => $_SERVER['REMOTE_ADDR'] // 终端 IP
            ));
            
            ksort($parameter);
            $parameter['sign']      = $this->getSign($parameter);// 签名
		    return  $this->arrayToXml($parameter);
		} catch (Exception $e){
			log_message('error', var_export($e, TRUE));
		}
	}
    
    /**
     * Wechatpay::createNativeOrderXml()
     * 生成响应微信扫码支付模式一的回调 xml 数据
     * @param array $data
     * @return
     */
    public function createNativeOrderXml($data = array()){
        if(empty($data)){
            die('Error nativeOrder with empty data');
        }
        
        try{
            $this->checkRequire('nativeOrder', $data);
            $parameter  = array_merge($data, $this->getParameter(), array(
                'return_code'   => 'SUCCESS',
                'result_code'   => 'SUCCESS'
            ));
            ksort($parameter);
            $parameter['sign']      = $this->getSign($parameter);// 签名
		    return  $this->arrayToXml($parameter);
        } catch (Exception $e){
			log_message('error', var_export($e, TRUE));
		}
    }

	/**
	 * Wechatpay::orderQuery()
	 * 查询订单，WxPayOrderQuery 中 out_trade_no、transaction_id 至少填一个
	 * appid、mchid、spbill_create_ip、nonce_str 不需要填入
	 * @param array $data
	 * @param integer $timeOut
	 * @return
	 */
	public function orderQuery($data, $timeOut = 6){
		$this->url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $this->curlTimeout  = $timeOut;
        
		//检测必填参数
        if(isset($data['out_trade_no']) || isset($data['transaction_id'])){
            $parameter              = array_merge($data, $this->getParameter());
            ksort($parameter);
            $parameter['sign']      = $this->getSign($parameter);
		    $data = $this->arrayToXml($parameter);
            $startTimeStamp = $this->getMillisecond();
            $data   = $this->getResult($data);
            $this->reportCostTime(current_url(), $data, $startTimeStamp);
            return $data;
        } else{
            die('订单查询接口中，out_trade_no、transaction_id 至少填一个!');
        }
	}
	
	/**
	 * Wechatpay::closeOrder()
	 * 关闭订单，WxPayCloseOrder中out_trade_no必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param string $data out_trade_no
	 * @param integer $timeOut
	 * @return
	 */
	public function closeOrder($data, $timeOut = 2){
		$this->url = 'https://api.mch.weixin.qq.com/pay/closeorder';
        $this->curlTimeout  = $timeOut;
        
        $parameter              = array_merge(array(
            'out_trade_no'  => $data
        ), $this->getParameter());
        ksort($parameter);
        $parameter['sign']      = $this->getSign($parameter);
        $data = $this->arrayToXml($parameter);
        $startTimeStamp = $this->getMillisecond();
        $data   = $this->getResult($data);
        $this->reportCostTime(current_url(), $data, $startTimeStamp);
        return $data;
	}

	/**
	 * Wechatpay::refund()
	 * 申请退款，WxPayRefund中out_trade_no、transaction_id 至少填一个且
	 * out_refund_no、total_fee、refund_fee、op_user_id 为必填参数
	 * appid、mchid、spbill_create_ip、nonce_str 不需要填入
	 * @param array $data
	 * @param integer $timeOut
	 * @return
	 */
	public function refund($data, $timeOut = 6){
		$this->url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $this->curlTimeout  = $timeOut;
        
		//检测必填参数
        if(isset($data['out_trade_no']) || isset($data['transaction_id'])){
            $this->checkRequire('refund', $data);
            $parameter              = array_merge($data, $this->getParameter(), array(
                'op_user_id'    => $this->mchId
            ));
            ksort($parameter);
            $parameter['sign']      = $this->getSign($parameter);
		    $data = $this->arrayToXml($parameter);
            $startTimeStamp = $this->getMillisecond();
            $data   = $this->getResult($data);
            $this->reportCostTime(current_url(), $data, $startTimeStamp);
            return $data;
        } else{
            die('退款申请接口中，out_trade_no、transaction_id 至少填一个!');
        }
	}
	
	/**
	 * Wechatpay::refundQuery()
	 * 查询退款
	 * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
	 * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
	 * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param array() $data
	 * @param integer $timeOut
	 * @return
	 */
	public function refundQuery($data, $timeOut = 6){
		$this->url = 'https://api.mch.weixin.qq.com/pay/refundquery';
        $this->curlTimeout  = $timeOut;
        
        //检测必填参数
        if(isset($data['out_refund_no']) || isset($data['out_trade_no']) || isset($data['transaction_id']) || isset($data['refund_id'])){
            $parameter              = array_merge($data, $this->getParameter());
            ksort($parameter);
            $parameter['sign']      = $this->getSign($parameter);
		    $data = $this->arrayToXml($parameter);
            $startTimeStamp = $this->getMillisecond();
            $data   = $this->getResult($data);
            $this->reportCostTime(current_url(), $data, $startTimeStamp);
            return $data;
        } else{
            die('退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id 至少填一个!');
        }
	}
	
	/**
	 * Wechatpay::downloadBill()
	 * 下载对账单，WxPayDownloadBill中bill_date为必填参数
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param array() $data
	 * @param integer $timeOut
	 * @return
	 */
	public function downloadBill($data, $timeOut = 6){
		$this->url = 'https://api.mch.weixin.qq.com/pay/downloadbill';
        $this->curlTimeout  = $timeOut;
        
		//检测必填参数
		$this->checkRequire('bill', $data);
        $parameter              = array_merge($data, $this->getParameter());
        ksort($parameter);
        $parameter['sign']      = $this->getSign($parameter);
        $data   = $this->postXmlCurl($this->arrayToXml($parameter), $this->curlTimeout);
        return $data;
	}
	
	/**
	 * Wechatpay::micropay()
	 * 提交被扫支付API
	 * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
	 * 由商户收银台或者商户后台调用该接口发起支付。
	 * WxPayWxPayMicroPay中body、out_trade_no、total_fee、auth_code参数必填
	 * @param array() $data
	 * @param integer $timeOut
	 * @return
	 */
	public function micropay($data, $timeOut = 10){
		$this->url = 'https://api.mch.weixin.qq.com/pay/micropay';
        $this->curlTimeout  = $timeOut;
        
		//检测必填参数
        $this->checkRequire('micropay', $data);
        
        $parameter              = array_merge($data, $this->getParameter());
        ksort($parameter);
        $parameter['sign']      = $this->getSign($parameter);
        $data = $this->arrayToXml($parameter);
        $startTimeStamp = $this->getMillisecond();
        $data   = $this->getResult($data);
        $this->reportCostTime(current_url(), $data, $startTimeStamp);
        return $data;
	}
	
	/**
	 * Wechatpay::reverse()
	 * 撤销订单API接口，WxPayReverse中参数out_trade_no和transaction_id必须填写一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param mixed $data
	 * @param integer $timeOut
	 * @return
	 */
	public function reverse($data, $timeOut = 6){
		$this->url = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';
        $this->curlTimeout  = $timeOut;
        
		//检测必填参数
        if(isset($data['out_trade_no']) || isset($data['transaction_id'])){
            $parameter              = array_merge($data);
            ksort($parameter);
            $parameter['sign']      = $this->getSign($parameter);
		    $data = $this->arrayToXml($parameter);
            $startTimeStamp = $this->getMillisecond();
            $data   = $this->getResult($data);
            $this->reportCostTime(current_url(), $data, $startTimeStamp);
            return $data;
        } else{
            die('撤销订单API接口中，参数 out_trade_no和transaction_id 至少填一个!');
        }
	}
    
    public function getBizPayUrl($productId){
        $parameter              = array_merge($this->getParameter(), array(
            'product_id'    => $productId,
            'time_stamp'    => time()
        ));
        ksort($parameter);
        $parameter['sign']      = $this->getSign($parameter);
        return $this->shortUrl(array(
            'long_url'  => 'weixin://wxpay/bizpayurl?'.http_build_query($parameter)
        ));
    }
	
	/**
	 * Wechatpay::bizpayurl()
	 * 生成二维码规则,模式一生成支付二维码
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param array $data
	 * @param integer $timeOut
	 * @return
	 */
	public function bizpayurl($data, $timeOut = 6){
        $this->curlTimeout  = $timeOut;
        
        //检测必填参数
        if(isset($data['product_id'])){
            $parameter              = array_merge($data, array(
                //'time'  => time()
            ));
            ksort($parameter);
            return $this->getSign($parameter);
        } else{
            die('生成二维码，缺少必填参数 product_id!');
        }
	}
	
	/**
	 * Wechatpay::shorturl()
	 * 转换短链接
	 * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，
	 * 减小二维码数据量，提升扫描速度和精确度。
	 * @param array $data
	 * @param integer $timeOut
	 * @return
	 */
	public function shortUrl($data, $timeOut = 6){
		$this->url = 'https://api.mch.weixin.qq.com/tools/shorturl';
        $this->curlTimeout  = $timeOut;
        
		//检测必填参数
        if(isset($data['long_url'])){
            $parameter              = array_merge($data, $this->getParameter());
            ksort($parameter);
            $parameter['sign']      = $this->getSign($parameter);
		    $data = $this->arrayToXml($parameter);
            $startTimeStamp = $this->getMillisecond();
            $data   = $this->getResult($data);
            $this->reportCostTime(current_url(), $data, $startTimeStamp);
            return 'SUCCESS' === $data['return_code'] ? $data['short_url'] : $data['return_msg'];
        } else{
            die('需要转换的 URL，签名用原串，传输需 URL encode!');
        }
	}
    
	/**
	 * Wechatpay::getResult()
	 * 获取结果，默认使用证书
	 * @param mixed $xml
	 * @param bool $ssl
	 * @return
	 */
	public function getResult($xml, $ssl = TRUE){
        $data   = $ssl ? $this->postXmlSSLCurl($xml, $this->curlTimeout) : $this->postXmlCurl($xml, $this->curlTimeout);
		return $this->xmlToArray($data);
	}
    
	/**
	 * Wechatpay::checkSign()
	 * 检查签名
	 * @param mixed $data
	 * @return
	 */
	public function checkSign($data){
        $data       = $this->xmlToArray($data);
        $tmpData    = $data;
		unset($tmpData['sign']);
		$sign = $this->getSign($tmpData); // 本地签名
		return $data['sign'] === $sign ? TRUE : FALSE;
	}
    
    /**
     * Wechatpay::trimString()
     * 过滤字符串
     * @param mixed $value
     * @return
     */
    private function trimString($value){
		$ret = NULL;
		if (NULL != $value){
			$ret = $value;
			if (strlen($ret) == 0){
				$ret = NULL;
			}
		}
		return $ret;
	}
    
    /**
     * Wechatpay::getParameter()
     * 获取基础提交参数
     * @return
     */
    private function getParameter(){
        return array(
            'appid'             => $this->appId,
            'mch_id'            => $this->mchId,
            'nonce_str'         => $this->getNoncestr()
        );
    }
    
	/**
	 * Wechatpay::getMillisecond()
	 * 获取毫秒级别的时间戳
	 * @return
	 */
	private function getMillisecond(){
		//获取毫秒的时间戳
		$time = explode (' ', microtime());
		$time = $time[1].($time[0] * 1000);
		$time = explode('.', $time);
		return $time[0];
	}
    
	/**
	 * Wechatpay::getNoncestr()
	 * 产生随机字符串，不长于 32 位
	 * @param integer $length
	 * @return
	 */
	private function getNoncestr($length = 32){
		$chars    = 'abcdefghijklmnopqrstuvwxyz0123456789';  
		$str      = '';
		for( $i = 0; $i < $length; $i++ ){  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}
    
	/**
	 * Wechatpay::formatBizQueryParaMap()
	 * 格式化参数，签名过程需要使用
	 * @param mixed $paraMap
	 * @param mixed $urlencode
	 * @return
	 */
	public function formatBizQueryParaMap($paraMap, $urlencode = FALSE){
		$buff = '';
		ksort($paraMap);
		foreach ($paraMap as $k => $v){
		    if($urlencode){
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&';
			$buff .= $k . '=' . $v . '&';
		}
		return strlen($buff) > 0 ? substr($buff, 0, strlen($buff)-1) : FALSE;
	}
    
	/**
	 * Wechatpay::getSign()
	 * 生成签名
	 * @param mixed $data
	 * @return
	 */
	public function getSign($data){
		//签名步骤一：按字典序排序参数
		ksort($data);
		$String = $this->formatBizQueryParaMap($data, FALSE);
        // 检查 key 长度，截取前 32 位
        strlen($this->key) === 32 || $this->key = substr($this->key, 0, 32);
		//签名步骤二：在string后加入KEY
		$String = $String.'&key='.$this->key;
		//签名步骤三：MD5加密
		$String = md5($String);
		//签名步骤四：所有字符转为大写
        return strtoupper($String);
	}
    
	/**
	 * Wechatpay::arrayToXml()
	 * array 转 xml
	 * @param mixed $arr
	 * @return
	 */
	public function arrayToXml($arr){
        $xml = '<xml>';
        foreach ($arr as $key=>$val){
            if (is_numeric($val)){
                $xml.='<'.$key.'>'.$val.'</'.$key.'>'; 
            } else{
                $xml.='<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
            }	
        }
        return $xml.='</xml>'; 
    }
	
	/**
	 * Wechatpay::xmlToArray()
	 * 将 xml 转为array
	 * @param mixed $xml
	 * @return
	 */
	public function xmlToArray($xml){		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}

	/**
	 * Wechatpay::postXmlCurl()
	 * 以post方式提交 xml 到对应的接口 url
	 * @param mixed $xml
	 * @param integer $second
	 * @return
	 */
	private function postXmlCurl($xml, $second = 5){		
        //初始化curl        
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
        $data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else{ 
			$error = curl_errno($ch);
            log_message('error', 'curl出错，错误码 '.$error);
			curl_close($ch);
			return TRUE;
		}
	}

	/**
	 * Wechatpay::postXmlSSLCurl()
	 * 使用证书，以 post 方式提交 xml 到对应的接口 url
	 * @param xml $xml
	 * @param integer $second
	 * @return
	 */
	private function postXmlSSLCurl($xml, $second = 10){
		$ch = curl_init();
		//超时时间
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		//这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch, CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch, CURLOPT_SSLCERT, $this->certPath);
		//默认格式为PEM，可以注释
		curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch, CURLOPT_SSLKEY, $this->keyPath);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_errno($ch);
			log_message('error', 'curl出错，错误码 '.$error);
			curl_close($ch);
			return false;
		}
	}
    
	/**
	 * Wechatpay::reportCostTime()
	 * 上报数据， 上报的时候将屏蔽所有异常流程
	 * @param string $url
	 * @param int $startTimeStamp
	 * @param array $data
	 * @return
	 */
	private function reportCostTime($url, $data, $startTimeStamp){
		//如果不需要上报数据
		if(0 === $this->reportLevel){
			return;
		} 
        
		//如果仅失败上报
		if(1 === $this->reportLevel && array_key_exists("return_code", $data) && $data["return_code"] == "SUCCESS" && array_key_exists("result_code", $data) && $data["result_code"] == "SUCCESS"){
		 	return;
        }
		 
		//上报逻辑
        $report = array(
            'interface_url' => $url,
            'return_code'   => $data['return_code'],
            'result_code'   => $data['result_code'],
            'user_ip'       => $_SERVER['REMOTE_ADDR'],
            'execute_time_' => $this->getMillisecond() - $startTimeStamp
        );
        
        $this->report($report, 1);
	}
    
    /**
	 * 
	 * 测速上报，该方法内部封装在report中，使用时请注意异常流程
	 * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param array $data
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	private function report($data, $timeOut = 1){
		$this->url    = 'https://api.mch.weixin.qq.com/payitil/report';
        $this->curlTimeout  = $timeOut;
        
		//检测必填参数
        $this->checkRequire('report', $data, TRUE);
        
        $parameter              = array_merge($data, $this->getParameter(), array(
            'time'  => date("YmdHis")
        ));
        unset($data);
        unset($parameter['appid']);
        ksort($parameter);
        $parameter['sign']      = $this->getSign($parameter);// 签名
        $data = $this->arrayToXml($parameter);
        return $this->getResult($data);
	}
}