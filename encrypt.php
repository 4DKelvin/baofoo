<?php
date_default_timezone_set('PRC');
require_once './Config/init.php';
Log::LogWirte("=====================支付交易====================");
//==================接收用户数据==========================

$user_id = isset($argv[1])? trim($argv[1]):"";//平台USER_ID
$id_card = isset($argv[2])? trim($argv[2]):"";//身份证号码
$id_holder = isset($argv[3])? trim($argv[3]):"";//姓名
$txn_amt = isset($argv[4])? trim($argv[4]):0;//交易金额额
$txn_amt *=100;//金额以分为单位（把元转换成分）

//================报文组装=================================
$DataContentParms =ARRAY();

$DataContentParms["member_id"] = $GLOBALS["member_id"];//商户号
$DataContentParms["terminal_id"] = $GLOBALS["terminal_id"];//终端号
$DataContentParms["id_card_type"] ="01" ;
$DataContentParms["id_card"] =$id_card ;
$DataContentParms["id_holder"] =$id_holder;

$DataContentParms["trans_id"] = "PHPID".get_transid().rand4();
$DataContentParms["trans_serial_no"] = "PHPTSN".get_transid().rand4();
$DataContentParms["txn_amt"] = $txn_amt;

$DataContentParms["trade_date"] = return_time();
$DataContentParms["commodity_name"] = "商品名称";
$DataContentParms["commodity_amount"] = "1";//商品数量

$DataContentParms["page_url"] = $GLOBALS["page_url"];//页面通知地址
$DataContentParms["return_url"] = $GLOBALS["return_url"];//异步接收通知地址。

$DataContentParms["additional_info"] = "附加信息";
$DataContentParms["req_reserved"] = "保留" ;
$DataContentParms["bind_id"] ="";//首次可传空（第二次可传bind_id）
$DataContentParms["user_id"] =$user_id ;//平台USER_ID
/**
 * -----------风控参数--------------
 * 本处只示例话费充值，商户根据自已的行业按照风控参数列表设置参数。
 *
 */
/*--------风控基础参数------------- */
$RiskItem = array();
$RiskItem["goods_category"]="1010";//商品类目 详见附录《商品类目》
$RiskItem["user_no"]="123456";//用户在商户系统中的标识,如user_id
$RiskItem["user_email"]="";
$RiskItem["user_mobile"]="15821788630";
$RiskItem["user_type"]="";
$RiskItem["register_time"]=return_time();
$RiskItem["register_ip"]="";
/*--------行业参数  (以下为实名类商户风控参数，请参看接口文档附录风控参数)------------- */
$RiskItem["recharge_mobile"]="15821788630";//被充值手机号

$DataContentParms["risk_item"] = json_encode($RiskItem);//加入风控参数(固定为JSON字串)
Log::LogWirte("输出风控参数：".$DataContentParms["risk_item"]);
//==================转换数据类型=============================================
if($GLOBALS["data_type"] == "json"){
	$Encrypted_string = str_replace("\\/", "/",json_encode($DataContentParms,TRUE));//转JSON
}else{
	$toxml = new SdkXML();	//实例化XML转换类
	$Encrypted_string = $toxml->toXml($DataContentParms);//转XML
}

Log::LogWirte("序列化结果：".$Encrypted_string);
$BFRsa = new BFRSA($GLOBALS["pfxfilename"], $GLOBALS["cerfilename"], $GLOBALS["private_key_password"]); //实例化加密类。
$Encrypted = $BFRsa->encryptedByPrivateKey($Encrypted_string);	//先BASE64进行编码再RSA加密

$result = array(
	'action'=>SelectUrl::Url($GLOBALS["IsTest"],$GLOBALS["PcWap"],1),
	'version'=>$GLOBALS["version"],
	'input_charset'=>$GLOBALS["input_charset"],
	'language'=>$GLOBALS["language"],
	'terminal_id'=>$GLOBALS["terminal_id"],
	'member_id'=>$GLOBALS["member_id"],
	'data_type'=>$GLOBALS["data_type"],
	'data_content'=>$Encrypted
	);

echo json_encode($result);
die();
?>