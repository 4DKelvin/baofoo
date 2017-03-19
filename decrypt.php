<?php
date_default_timezone_set('PRC');
require_once './Config/init.php';

Log::LogWirte("===================接收异步通知========================");

if(!isset($argv[1])){
    die(json_encode(array('code'=>500,'data'=>'参数错误')));
}

Log::LogWirte("异步通知原文：".$EndataContent);

$EndataContent =  trim($argv[1]);



try{
    $BFRsa = new BFRSA($GLOBALS["pfxfilename"], $GLOBALS["cerfilename"], $GLOBALS["private_key_password"]); //实例化加密类。
    $ReturnDecode = $BFRsa->decryptByPublicKey($EndataContent);//解密返回的报文

    Log::LogWirte("异步通知解密原文：".$ReturnDecode);

    if(!empty($ReturnDecode)){//解析
        $ArrayContent=array();
        if($GLOBALS["data_type"] =="xml"){
            $ArrayContent = SdkXML::XTA($ReturnDecode);
        }else{
            $ArrayContent = json_decode($ReturnDecode,TRUE);
        }
    }
    echo json_encode(array('code'=>200,'data'=> $ArrayContent));
} catch (Exception $e){
    echo json_encode(array('code'=>500,'data'=>$e));
}