<?php
/**
 * gwshop
 * 微信交互类
 */ 
namespace Home\Controller;
use Think\Controller;
class WeixinController extends Controller {
    public $client;
    public function _initialize(){
        //parent::_initialize();
        //获取微信配置信息
        $wechat_config = M('wx_user')->where('1=1')->find();
        $this->token = $wechat_config['w_token'];
//        $options = array(
// 			//'token'=>$wechat_config['w_token'], //填写你设定的token
// 			'encodingaeskey'=>$wechat_config['aeskey'], //填写加密用的EncodingAESKey
// 			'appid'=>$wechat_config['appid'], //填写高级调用功能的app id
// 			'appsecret'=>$wechat_config['appsecret'], //填写高级调用功能的密钥
//        		);


    }

    public function index(){

        if (isset($_GET['echostr'])) {
            $this->valid();//验证成为开发者

        }else{
            $this->responseMsg();
        }
    }




    
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      	//extract post data
	 if (empty($postStr))                 	
        	exit("");
         
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                if(empty($keyword))
                    exit("Input something...");
                
                // 图文回复
                $wx_img = M('wx_img')->where("keyword like '%$keyword%'")->find();
                if($wx_img)
                {
                    $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <ArticleCount><![CDATA[%s]]></ArticleCount>
                                <Articles>
                                    <item>
                                        <Title><![CDATA[%s]]></Title> 
                                        <Description><![CDATA[%s]]></Description>
                                        <PicUrl><![CDATA[%s]]></PicUrl>
                                        <Url><![CDATA[%s]]></Url>
                                    </item>                               
                                </Articles>
                                </xml>";                                        
                    $resultStr = sprintf($textTpl,$fromUsername,$toUsername,$time,'news','1',$wx_img['title'],$wx_img['desc']
                            , $wx_img['pic'], $wx_img['url']);
                    exit($resultStr);                   
                }
                
                
                // 文本回复
                $wx_text = M('wx_text')->where("keyword like '%$keyword%'")->find();
                if($wx_text)
                {
                    $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                <FuncFlag>0</FuncFlag>
                                </xml>";                    
                    $contentStr = $wx_text['text'];
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                    exit($resultStr);                   
                }
                
                
                // 其他文本回复                
                    $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                <FuncFlag>0</FuncFlag>
                                </xml>";                    
                    $contentStr = '欢迎来到GWshop商城!';
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
                    exit($resultStr);                   
      
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    //验证
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }






}