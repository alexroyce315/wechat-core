<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    
/**
 * Wechat
 * 微信公众号基础类
 * @package wechat
 * @author alex.royce315@gmail.com
 * @copyright 2014
 * @version 1.0
 * @access public
 */
class Wechat {

    /**
     * Wechat::__construct()
     * 构造方法
     * @return void
     */
    public function __construct()
    {
        log_message('debug', "Wechat Class Initialized");
    }
    
    //空格 (&#x20;) 
    //Tab (&#x09;) 
    //回车 (&#x0D;) 
    //换行 (&#x0A;)
    
    /**
     * Wechat::responseText()
     * 回复文本消息
     * @param string $toUsername
     * @param string $fromUsername
     * @param string $contentStr
     * @param integer $flag
     * @return xml
     */
    public function responseText($toUsername, $fromUsername, $contentStr, $flag = 0){
        $tpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<Content>%s</Content>
			<FuncFlag>0</FuncFlag>
			</xml>";             
         $resultStr = sprintf($tpl, $toUsername, $fromUsername, time(), "text", $contentStr, $flag);
         echo $resultStr;
    }

    /**
     * Wechat::responseImage()
     * 回复图片消息
     * @param string $toUsername
     * @param string $fromUsername
     * @param string $media_id
     * @return xml
     */
    public function responseImage($toUsername, $fromUsername, $media_id){
        $tpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>
            </xml>";
        $resultStr = sprintf($tpl, $toUsername, $fromUsername, time(), "image", $media_id);
        echo $resultStr;
    }

    /**
     * Wechat::responseVoice()
     * 回复音频消息
     * @param string $toUsername
     * @param string $fromUsername
     * @param string $media_id
     * @return xml
     */
    public function responseVoice($toUsername, $fromUsername, $media_id){
        $tpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Voice>
            <MediaId><![CDATA[%s]]></MediaId>
            </Voice>
            </xml>";
        $resultStr = sprintf($tpl, $toUsername, $fromUsername, time(), "voice", $media_id);
        echo $resultStr;
    }

    /**
     * Wechat::responseVideo()
     * 回复视频消息
     * @param string $toUsername
     * @param string $fromUsername
     * @param string $media_id
     * @param string $title
     * @param string $description
     * @return xml
     */
    public function responseVideo($toUsername, $fromUsername, $media_id, $title = NULL, $description = NULL){
        $tpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Video>
            <MediaId><![CDATA[%s]]></MediaId>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            </Video> 
            </xml>";
        $resultStr = sprintf($tpl, $toUsername, $fromUsername, time(), "video", $media_id, $title, $description);
        echo $resultStr;
    }

    /**
     * Wechat::responseMusic()
     * 回复音乐消息
     * @param string $toUsername
     * @param string $fromUsername
     * @param string $media_id
     * @param string $title
     * @param string $description
     * @param string $music_url
     * @param string $hq_music_url
     * @param string $thumb_media_id
     * @return xml
     */
    public function responseMusic($toUsername, $fromUsername, $media_id, $title = NULL, $description = NULL, $music_url = NULL, $hq_music_url = NULL, $thumb_media_id){
        $tpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Music>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <MusicUrl><![CDATA[%s]]></MusicUrl>
            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
            <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
            </Music>
            </xml>";
        $resultStr = sprintf($tpl, $toUsername, $fromUsername, time(), "Music", $media_id, $title, $description, $music_url, $hq_music_url, $thumb_media_id);
        echo $resultStr;
    }

    /**
     * Wechat::responseNews()
     * 回复图文消息
     * @param string $toUsername
     * @param string $fromUsername
     * @param array $article
     * @return xml
     */
    public function responseNews($toUsername, $fromUsername, array $article){
        if(is_array($article) && ($articlecount = count($article)) <= 10){ // $article 数组不能超过 10 条
            $tpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <ArticleCount>%s</ArticleCount>
                <Articles>";
            $resultStr = sprintf($tpl, $toUsername, $fromUsername, time(), "News", $articlecount);
            foreach($article as $item){
                $tempTpl = "<item>
                    <Title>%s</Title> 
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl>%s</PicUrl>
                    <Url>%s</Url>
                    </item>";
                $resultStr = $resultStr . sprintf($tempTpl, $item['title'], $item['description'], $item['picurl'], $item['url']);
            }
            echo $resultStr . "</Articles></xml>";
        } else{
            echo NULL;
        }
    }

    /**
     * Wechat::customText()
     * 回复文本客服信息
     * @param mixed $accessToken
     * @param mixed $toUsername
     * @param mixed $context
     * @return void
     */
    public function customText($accessToken, $toUsername, $context){
        $tpl = '{
            "touser":"%s",
            "msgtype":"%s",
            "text":
            {
                 "content":"%s"
            }
        }';
        $resultStr = sprintf($tpl, $toUsername, "text", $context);
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
        return $this->dataPost($resultStr, $url, true);
    }

    /**
     * Wechat::customImage()
     * 回复图片客服信息
     * @param mixed $accessToken
     * @param mixed $toUsername
     * @param mixed $mediaId
     * @return void
     */
    public function customImage($accessToken, $toUsername, $mediaId){
        $tpl = '{
            "touser":"%s",
            "msgtype":"$s",
            "image":
            {
              "media_id":"%s"
            }
        }';
        $resultStr = sprintf($tpl, $toUsername, "image", $mediaId);
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
        return $this->dataPost($resultStr, $url, true);
    }

    /**
     * Wechat::customVoice()
     * 回复音频客服信息
     * @param mixed $accessToken
     * @param mixed $toUsername
     * @param mixed $mediaId
     * @return void
     */
    public function customVoice($accessToken, $toUsername, $mediaId){
        $tpl = '{
            "touser":"%s",
            "msgtype":"$s",
            "voice":
            {
              "media_id":"%s"
            }
        }';
        $resultStr = sprintf($tpl, $toUsername, "voice", $mediaId);
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
        return $this->dataPost($resultStr, $url, true);
    }

    /**
     * Wechat::customVideo()
     * 回复视频客服信息
     * @param mixed $accessToken
     * @param mixed $toUsername
     * @param mixed $mediaId
     * @param mixed $title
     * @param mixed $description
     * @return void
     */
    public function customVideo($accessToken, $toUsername, $mediaId, $title = NULL, $description = NULL){
        $tpl = '{
            "touser":"%s",
            "msgtype":"%s",
            "video":
            {
              "media_id":"%s",
              "title":"%s",
              "description":"%s"
            }
        }';
        $resultStr = sprintf($tpl, $toUsername, "video", $mediaId, $title, $description);
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
        return $this->dataPost($resultStr, $url, true);
    }

      /**
       * Wechat::customMusic()
       * 回复音乐客服信息
       * @param mixed $accessToken
       * @param mixed $toUsername
       * @param mixed $title
       * @param mixed $description
       * @param mixed $musicUrl
       * @param mixed $hqMusicUrl
       * @param mixed $thumbMediaId
       * @return void
       */
      public function customMusic($accessToken, $toUsername, $title = NULL, $description = NULL, $musicUrl, $hqMusicUrl, $thumbMediaId){
        $tpl = '{
            "touser":"%s",
            "msgtype":"%s",
            "music":
            {
              "title":"%s",
              "description":"%s",
              "musicurl":"%s",
              "hqmusicurl":"%s",
              "thumb_media_id":"%s" 
            }
        }';
        $resultStr = sprintf($tpl, $toUsername, "music", $title, $description, $musicUrl, $hqMusicUrl, $thumbMediaId);
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
        return $this->dataPost($resultStr, $url, true);
    }

    /**
     * Wechat::customArticle()
     * 回复文章客服信息
     * @param mixed $accessToken
     * @param mixed $toUsername
     * @param mixed $article
     * @return void
     */
    public function customArticle($accessToken, $toUsername, $article){
        if(is_array($article) && $articlecount = count($article) < 10){ // $article 数组不能超过 10 条
            $tpl = '{
                "touser":"%s",
                "msgtype":"%s",
                "news":{
                    "articles": [';
            $resultStr = sprintf($tpl, $toUsername, 'news');
            foreach($article as $key => $item){
                $tempTpl = '{
                     "title":"%s",
                     "description":"%s",
                     "url":"%s",
                     "picurl":"%s"
                 }';
                $resultStr = $resultStr . sprintf($tempTpl, $item['title'], $item['description'], $item['url'], $item['picurl']);
                $resultStr = $articlecount == $key + 1 ? $resultStr : $resultStr . ',';
            }
            $resultStr = $resultStr . ']
                    }
                }';

            $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
            log_message('debug', $resultStr);
            return $this->dataPost($resultStr, $url, true);
        }
    }
    
    /**
     * Wechat::listUser()
     * 拉取关注用户列表
     * @param mixed $accessToken
     * @param integer $nextOpenId
     * @return
     */
    public function getUserList($accessToken, $nextOpenId = null){
        $url    = $nextOpenId == null ? "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $accessToken : "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $accessToken . "&next_openid=" . $nextOpenId;
        $data   = $this->curlGet($url);
        $resultArr = json_decode($data, true);
        return $resultArr;
    }

    /**
     * Wechat::userInfo()
     * 拉取用户资料
     * @param mixed $accessToken
     * @param mixed $openId
     * @return void
     */
    public function userInfo($accessToken, $openId){
        $this->getUserInfo($accessToken, $openId);
    }
    
    /**
     * Wechat::getUserInfo()
     * 拉取用户资料
     * @param mixed $accessToken
     * @param mixed $openId
     * @return
     */
    public function getUserInfo($accessToken, $openId){
        $url    = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $accessToken . "&openid=" . $openId . "&lang=zh_CN";
        $data   = $this->curlGet($url);
        $resultArr = json_decode($data, true);
        return $resultArr;
    }
    
    /**
     * Wechat::getUserGroup()
     * 获得指定用户的分组编号
     * @param mixed $accessToken
     * @param mixed $openId
     * @return
     */
    public function getUserGroup($accessToken, $openId){
        $url        = "https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=" . $accessToken;
        $resultStr  = '{"openid":"' . $openId . '"}';
        $data       = $this->dataPost($resultStr, $url, true); // {"groupid": 102}
        $resultArr  = json_decode($data, true);
        return isset($resultArr['groupid']) ? $resultArr['groupid'] : '0';
    }
    
    /**
     * Wechat::getGroupList()
     * 获得用户分组
     * @param mixed $accessToken
     * @return
     */
    public function getGroupList($accessToken){
        $url    = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=" . $accessToken;
        $data   = $this->curlGet($url);
        $resultArr = json_decode($data, true);
        return $resultArr;
    }

    /**
     * Wechat::deleteMenu()
     * 删除菜单
     * @param string $accessToken
     * @return json
     */
    public function deleteMenu($accessToken){
        $menuGetUrl = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$accessToken;//POST的url
        $result     = json_decode($this->dataGet($menuGetUrl), true);
		return $result;
    }

    /**
     * Wechat::getMenu()
     * 获取菜单
     * @param string $accessToken
     * @return json
     */
    public function getMenu($accessToken){
        $menuGetUrl = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$accessToken;
        $result     = $this->dataGet($menuGetUrl);
        return $result;
    }
    
    /**
     * Wechat::creatMenu()
     * 创建菜单
     * @param string $accessToken
     * @param json $menu
     * @return json
     */
    public function creatMenu($menu, $accessToken){
        $menuPostUrl    = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;
        $result         = $this->curlPost($menuPostUrl, $menu);
        log_message('debug', $result);
        return json_decode($result);
    }
    
    /**
     * Wechat::menuMake()
     * 菜单生成方法
     * @param mixed $menu
     * @return
     */
    public function menuMake($menu){
        $menu = is_array($menu) ? $menu : $this->object2array($menu);
        $menuString = '{"button":[';
        $arrayNum = 0;
        $arrayCount = count($menu);
        for($i = 0; $i < $arrayCount; $i++){
            $item = $menu[$i];
            $item = is_array($item) ? $item : $this->object2array($item);
            $arrayNum ++;
            if($arrayNum == $arrayCount){
                $menuString = $menuString . $this->menuItemAdd($item['type'], $item['name'], $item['clickId'], $item['url']);
                $menuString = trim($menuString,','). ']}';
            } else{
                $itemNext = $menu[$i + 1];
                $itemNext = is_array($itemNext) ? $itemNext : $this->object2array($itemNext);
                if($item['parentId'] == 0){
                    if($item['menuId'] == $itemNext['parentId']){
                        $menuString = $menuString . '{"name":"' . $item['name'] . '","sub_button":[';
                    } else{
                       $menuString = $menuString . $this->menuItemAdd($item['type'], $item['name'], $item['clickId'], $item['url']); 
                    }
                } else{
                    $menuString = $menuString . $this->menuItemAdd($item['type'], $item['name'], $item['clickId'], $item['url']);
                    if($itemNext['parentId'] == 0){
                        $menuString = trim($menuString,',') . ']},';
                    }
                }
            }
        }
        return $menuString;
    }
    
    /**
     * Wechat::menuItemAdd()
     * 子菜单添加方法
     * @return void
     */
    private function menuItemAdd($type = 'click', $name, $clickId = null, $url = null){
        switch($type){ // 如果是子菜单
            case 'click':
            default:
                return '{"type":"' . $type . '","name":"' . $name . '","key":"' . $clickId . '"},';
                break;
            case 'view':
                return '{"type":"' . $type . '","name":"' . $name . '","url":"' . $url . '"},';
                break;
        }
    }
    
    
    /**
     * Wechat::getQrTicket()
     * 获取二维码 ticket 方法
     * @param mixed $accessToken
     * @param integer $qrcodeId action 为 QR_SCENE 时为 32 位非 0 的整数，QR_LIMIT_SCENE 时不超过 100000 的整数
     * @param string $action
     * @param integer $expire 不超过 1800，即半小时
     * @return
     */
    public function getQrTicket($accessToken, $qrcodeId, $action = 'QR_SCENE', $expire = 1800){
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accessToken;
        $postString = $action == 'QR_SCENE' ? '{"expire_seconds": ' . $expire . ', "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": ' . $qrcodeId . '}}}' : '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": ' . $qrcodeId . '}}}';
        $result = $this->dataPost($postString, $postUrl, true); // {"ticket":"gQG28DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0FuWC1DNmZuVEhvMVp4NDNMRnNRAAIEesLvUQMECAcAAA==","expire_seconds":1800}
        $result = json_decode($result, true);
        return $result;
    }
    
    /**
     * Wechat::getQrcode()
     * 通过 ticket 获取微信生成的二维码
     * @param mixed $ticket
     * @param string $action
     * @return
     */
    public function getQrcode($ticket = null, $action = 'QR_SCENE'){
        if($ticket != null){
            $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
            if($action == 'QR_SCENE'){ // 如果是临时二维码，如用于支付等形式
                return $url;
            } else{ // 否则是永久二维码，本地保存
                $date = date('Ym');
        	    $dirname = 'uploads/' . $date . '/';
                $localdir = 'D:\\ykwxggzh\\wechat\\uploads\\' . $date . '\\';
                $filename = time().rand(100,999);
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, 0);    
                curl_setopt($ch, CURLOPT_NOBODY, 0);    //对body进行输出。
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
                curl_setopt($ch, CURLOPT_SSLVERSION,3); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $package = curl_exec($ch);
                $httpinfo = curl_getinfo($ch);
                //var_dump($package);
                //var_dump(curl_error($ch));
                curl_close($ch);

                $media = array_merge(array('mediaBody' => $package), $httpinfo);
                //log_message('debug', var_export($media, true));
                //var_dump($media);
                //求出文件格式
                preg_match('/\w\/(\w+)/i', $media["content_type"], $extmatches);
                $fileExt = $extmatches[1];
                $filename = $filename.".".$fileExt;
                file_put_contents($localdir.$filename,$media['mediaBody']);
                
                return $dirname.$filename;
            }
        }
    }

	/**
	 * Wechat::valid()
	 * 微信 token 认证
	 * @param string $token
	 * @return string
	 */
	public function valid($token = NULL){
        $CI         = &get_instance();
        $echoStr    = $CI->input->get('echostr');

        //valid signature , option
        if($this->checkSignature($token)){
        	echo $echoStr;
        }
    }

	/**
	 * Wechat::checkSignature()
	 * 检查签名
	 * @param string $token
	 * @return string
	 */
	public function checkSignature($token){
        $CI         = &get_instance();

        $signature  = $CI->input->get('signature');
        $timestamp  = $CI->input->get('timestamp');
        $nonce      = $CI->input->get('nonce');
        		
		$tmpArr     = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr     = sha1(implode($tmpArr));

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

    /**
     * Wechat::getAccessToken()
     * 获取 access_token 并尝试写入缓存，如有存在缓存则读取缓存，缓存时间为 7000s
     * @param string $appid
     * @param string $appsecret
     * @return string
     */
    public function getAccessToken($appid, $appsecret) {
        $tokenFile = sha1($appid);
        $CI = &get_instance();
        $CI->load->driver('cache');
        $getToken = $CI->cache->file->get($tokenFile);
        if (!$getToken || empty($getToken) || $getToken == ''){ // 如果不存在文件或读取失败则重新获取
            $url        = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $data       = $this->curlGet($url);//通过自定义函数curlGet得到https的内容
            $result     = json_decode($data, true);//转为数组
            $result     = $result["access_token"];
            $CI->cache->file->save($tokenFile, $result, 7000);
            return $result;//获取access_token
        } else{
            return $getToken;
        }
    }
    
    public function getAuthToken($appid, $appsecret, $code){
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
        $data       = $this->curlGet($url);//通过自定义函数curlGet得到https的内容
        $result     = json_decode($data, true);//转为数组
        //log_message('debug', var_export($result));
        return $result;
    }

    /**
     * Wechat::curlGet()
     * get https的内容
     * @param string $url
     * @return json
     */
    public function curlGet($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); // https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=1414562353&version=12&lang=zh_CN，1 ==> CURL_SSLVERSION_TLSv1
        $result =  curl_exec($ch);
        curl_close ($ch);
        return $result;
    }
    
    public function curlPost($url , $postString){
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HEADER, 0);    //不取得返回头信息  
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); // 防止 post 数据过长导致无法获取返回数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); // https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=1414562353&version=12&lang=zh_CN，1 ==> CURL_SSLVERSION_TLSv1
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        $result = curl_exec($ch);
        curl_close($ch);
        //log_message('debug', $result);
        echo $result;
    }

    /**
     * Wechat::dataPost()
     * POST方式提交数据，判断使用 file_get_contents 或者 curl
     * @param string $post_string
     * @param string $url
     * @return json
     */
    public function dataPost($post_string, $url, $usingSSL = false) {
        if(function_exists('file_get_contents') && !$usingSSL) {
            //$post_string = http_build_query($post_string);
            $options = array(
                'http' => array(
                    'header'    => "Content-Type: application/x-www-form-urlencoded\r\n".
                                "Content-Length: ".strlen($post_string)."\r\n".
                                "User-Agent:MyAgent/1.0\r\n",
                    'method'    => "POST",
                    'content'   => $post_string,
                ),
            );
            $context = stream_context_create($options);
            $data = file_get_contents($url, false, $context, -1, 40000);
            if (false == $data){
			    $data  	= $this->dataPost($post_string, $url, true);
			}
            return $data;
        } else {
            $ch         = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); // 防止 post 数据过长导致无法获取返回数据
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
            curl_setopt($ch, CURLOPT_FAILONERROR,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1); // https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=1414562353&version=12&lang=zh_CN，1 ==> CURL_SSLVERSION_TLSv1
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            
            $file_contents = curl_exec($ch);
            curl_close($ch);
            return $file_contents;
        }
    }

    /**
     * Wechat::dataGet()
     * get 方式获取数据，判断使用 file_get_contents 或者 curl
     * @param string $url
     * @return json
     */
    public function dataGet($url, $usingSSL = false){
        $data = NULL;
        if(function_exists('file_get_contents') && !$usingSSL) {
            $data       = file_get_contents($url);
            if (false == $data){
			    $data  	= $this->dataGet($url, true);
			}
        } else {
            $ch = curl_init();
            $timeout    = 4; 
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1); // https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=1414562353&version=12&lang=zh_CN，1 ==> CURL_SSLVERSION_TLSv1
            $data = curl_exec($ch);
            curl_close($ch);
        }
        return $data;
    }

    /**
     * Wechat::fileGet()
     * 拉取媒体文件
     * @param string $accessToken
     * @param string $mediaId
     * @return
     */
    public function fileGet($accessToken, $mediaId){
    	$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $accessToken . '&media_id=' . $mediaId;
    	
    	$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);    
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //对body进行输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); // https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=1414562353&version=12&lang=zh_CN，1 ==> CURL_SSLVERSION_TLSv1
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        
        $media = array_merge(array('mediaBody' => $package), $httpinfo);
        //log_message('debug', var_export($media, true));
        
        //求出文件格式
        preg_match('/\w\/(\w+)/i', $media["content_type"], $extmatches);
        $fileExt = $extmatches[1];
        $filename = time().rand(100,999).".{$fileExt}";
        $date = date('Ym');
	    $dirname = 'uploads/' . $date . '/';
        $localdir = 'D:\\ykwxggzh\\wechat\\uploads\\' . $date . '\\';
        if(!file_exists($localdir)){
            mkdir($localdir,0777,true) ? null : mkdir($localdir);
        }
        file_put_contents($localdir.$filename,$media['mediaBody']);
        return $dirname.$filename;
    }

    /**
     * Wechat::filePost()
     * 上传媒体文件
     * @param mixed $accessToken
     * @param mixed $media
     * @return
     */
    public function filePost($accessToken, $type = 'image', $mediaUrl){
        $fields['media'] = '@'.realpath($mediaUrl); //要上传的文件
        $url    = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $accessToken . "&type=" . $type;
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); // https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=1414562353&version=12&lang=zh_CN，1 ==> CURL_SSLVERSION_TLSv1
        if (!empty($fields)){
	        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSLVERSION, 1); // https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=1414562353&version=12&lang=zh_CN，1 ==> CURL_SSLVERSION_TLSv1
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    }
        $result = curl_exec($ch);
        curl_close($ch);
        //log_message('debug', $result);
        return json_decode($result, true);
    }
    
    /**
     * Wechat::errorMsg()
     * 错误信息查询
     * @param mixed $id
     * @return
     */
    public function errorMsg($id = null){
        switch($id){
            default:
            case '-1':
                return '系统繁忙';
                break;
            case '0':
                return '请求成功';
                break;
            case '40001':
                return '获取access_token时AppSecret错误，或者access_token无效';
                break;
            case '40002':
                return '不合法的凭证类型';
                break;
            case '40003':
                return '不合法的OpenID';
                break;
            case '40004':
                return '不合法的媒体文件类型';
                break;
            case '40005':
                return '不合法的文件类型';
                break;
            case '40006':
                return '不合法的文件大小';
                break;
            case '40007':
                return '不合法的媒体文件id';
                break;
            case '40008':
                return '不合法的消息类型';
                break;
            case '40009':
                return '不合法的图片文件大小';
                break;
            case '40010':
                return '不合法的语音文件大小';
                break;
            case '40011':
                return '不合法的视频文件大小';
                break;
            case '40012':
                return '不合法的缩略图文件大小';
                break;
            case '40013':
                return '不合法的APPID';
                break;
            case '40014':
                return '不合法的access_token';
                break;
            case '40015':
                return '不合法的菜单类型';
                break;
            case '40016':
            case '40017':
                return '不合法的按钮个数';
                break;
            case '40018':
                return '不合法的按钮名字长度';
                break;
            case '40019':
                return '不合法的按钮KEY长度';
                break;
            case '40020':
                return '不合法的按钮URL长度';
                break;
            case '40021':
                return '不合法的菜单版本号';
                break;
            case '40022':
                return '不合法的子菜单级数';
                break;
            case '40023':
                return '不合法的子菜单按钮个数';
                break;
            case '40024':
                return '不合法的子菜单按钮类型';
                break;
            case '40025':
                return '不合法的子菜单按钮名字长度';
                break;
            case '40026':
                return '不合法的子菜单按钮KEY长度';
                break;
            case '40027':
                return '不合法的子菜单按钮URL长度';
                break;
            case '40028':
                return '不合法的自定义菜单使用用户';
                break;
            case '40029':
                return '不合法的oauth_code';
                break;
            case '40030':
                return '不合法的refresh_token';
                break;
            case '40031':
                return '不合法的openid列表';
                break;
            case '40032':
                return '不合法的openid列表长度';
                break;
            case '40033':
                return '不合法的请求字符，不能包含\uxxxx格式的字符';
                break;
            case '40035':
                return '不合法的参数';
                break;
            case '40038':
                return '不合法的请求格式';
                break;
            case '40039':
                return '不合法的URL长度';
                break;
            case '40050':
                return '不合法的分组id';
                break;
            case '40051':
                return '分组名字不合法';
                break;
            case '41001':
                return '缺少access_token参数';
                break;
            case '41002':
                return '缺少appid参数';
                break;
            case '41003':
                return '缺少refresh_token参数';
                break;
            case '41004':
                return '缺少secret参数';
                break;
            case '41005':
                return '缺少多媒体文件数据';
                break;
            case '41006':
                return '缺少media_id参数';
                break;
            case '41007':
                return '缺少子菜单数据';
                break;
            case '41008':
                return '缺少oauth code';
                break;
            case '41009':
                return '缺少openid';
                break;
            case '42001':
                return 'access_token超时';
                break;
            case '42002':
                return 'refresh_token超时';
                break;
            case '42003':
                return 'oauth_code超时';
                break;
            case '43001':
                return '需要GET请求';
                break;
            case '43002':
                return '需要POST请求';
                break;
            case '43003':
                return '需要HTTPS请求';
                break;
            case '43004':
                return '需要接收者关注';
                break;
            case '43005':
                return '需要好友关系';
                break;
            case '44001':
                return '多媒体文件为空';
                break;
            case '44002':
                return 'POST的数据包为空';
                break;
            case '44003':
                return '图文消息内容为空';
                break;
            case '44004':
                return '文本消息内容为空';
                break;
            case '45001':
                return '多媒体文件大小超过限制';
                break;
            case '45002':
                return '消息内容超过限制';
                break;
            case '45003':
                return '标题字段超过限制';
                break;
            case '45004':
                return '描述字段超过限制';
                break;
            case '45005':
                return '链接字段超过限制';
                break;
            case '45006':
                return '图片链接字段超过限制';
                break;
            case '45007':
                return '语音播放时间超过限制';
                break;
            case '45008':
                return '图文消息超过限制';
                break;
            case '45009':
                return '接口调用超过限制';
                break;
            case '45010':
                return '创建菜单个数超过限制';
                break;
            case '45015':
                return '回复时间超过限制';
                break;
            case '45016':
                return '系统分组，不允许修改';
                break;
            case '45017':
                return '分组名字过长';
                break;
            case '45018':
                return '分组数量超过上限';
                break;
            case '46001':
                return '不存在媒体数据';
                break;
            case '46002':
                return '不存在的菜单版本';
                break;
            case '46003':
                return '不存在的菜单数据';
                break;
            case '46004':
                return '不存在的用户';
                break;
            case '47001':
                return '解析JSON/XML内容错误';
                break;
            case '48001':
                return 'api功能未授权';
                break;
            case '50001':
                return '用户未授权该api';
                break;
        }
    }

    /**
     * Wechat::json_to_array()
     * json 转 array
     * @param json $json
     * @return array
     */
    public function json_to_array($json){
        $array = array();
        foreach($json as $k=>$w){
            if(is_object($w)){
                $json[$k]=json_to_array($w); //判断类型是不是object
            }
            else $json[$k]=$w;
        }
        return $array;
    }

    /**
     * Wechat::object_to_array()
     * object 转 array
     * @param object $obj
     * @return array
     */
    public function object_to_array($obj){
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        $arr = array();
        foreach ($_arr as $key => $val){
            $arr[$key] = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
        }
        return $arr; 
    } 
    
    function object2array($object) {  
        if (is_object($object)) {  
            foreach ($object as $key => $value) {  
                $array[$key] = $value;  
            }  
        }  
        else {  
            $array = $object;  
        }  
        return $array;  
    }
    
    public function objectToArray($d){
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d); //将第一层对象转换为数组
        }
        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
            */
            return array_map(__FUNCTION__, $d);//如果是数组使用array_map递归调用自身处理数组元素
        } else{
            // Return array
            return $d;
        }
    }
    
    /** 
    * 多维数组的叠加合并
     * @param array $array
     * @return
     */  
    public function array_multi2single($array){
        $result_array=array();
        foreach($array as $value){
            if(is_array($value)){
                $this->array_multi2single($value);
            }else{
                $result_array[]=$value;
            }
        }
        return $result_array;
    }
}
/* End of file Wechat.php */
/* Location: ./system/libraries/Wechat.php */
