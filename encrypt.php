<?php
/**
 * 宝付快捷支付-DEMO
 * 本实例依赖包在WEB-IF/lib文件夹内，证书在CER文件夹，配制文件在System_Config/app.properties
 * 实例仅供学习《宝付快捷支付》接口使用，仅供参考。商户可根据本实例写自已的代码
 * @author：宝付（大圣）
 * @date:20160620
 * 
 *测试卡具体信息如下：
 *银行卡号 		发卡行名称  姓名    身份证号    手机号
 *6222020111122220000	工商银行    张宝    320301198502169142	对接人员手机号	
 *6228480444455553333	农业银行    王宝    320301198502169142	对接人员手机号
 * 
 * 
 * @作者：宝付技术（大圣）
 */
require_once './Config/init.php';
Log::LogWirte("=====================支付交易====================");
//==================接收用户数据==========================

$user_id = isset($_POST["user_id"])? trim($_POST["user_id"]):"";//平台USER_ID
$id_card = isset($_POST["id_card"])? trim($_POST["id_card"]):"";//身份证号码
$id_holder = isset($_POST["id_holder"])? trim($_POST["id_holder"]):"";//姓名
$txn_amt = isset($_POST["txn_amt"])? trim($_POST["txn_amt"]):0;//交易金额额
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