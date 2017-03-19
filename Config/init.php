<?php
/**
 * 
 * 宝付快捷支付-DEMO
 * 本实例证书在CER文件夹，配制文件在Config
 * 实例仅供学习《宝付快捷支付》接口使用，仅供参考。商户可根据本实例写自已的代码
 * @author：宝付（大圣）
 * @date:20160620
 * 
 *测试卡具体信息如下：
 *银行卡号 		发卡行名称	姓名	身份证号	手机号			支付结果 
 *6222020111122220000	工商银行	张宝	320301198502169142	对接人员手机号	
 *6228480444455553333	农业银行	王宝	320301198502169142	对接人员手机号	
 */

\header("Content-type: text/html; charset=utf-8"); 
//====================配置商户的宝付接口授权参数============================
$path = './';
$pathcer = $path."/CER/";	//证书路径

require_once($path."/Function/BFRSA.php");
require_once($path."/Function/SdkXML.php");
require_once($path."/Function/Log.php");
require_once($path."/Function/HttpClient.php");
require_once($path."/Function/SelectUrl.php");


Log::LogWirte("=================快捷支付=====================");
//====================配置商户的宝付接口授权参数==============

$version = "4.0.0.0";//版本号
$member_id = "100000178";	//商户号
$terminal_id = "100000916";	//终端号
$input_charset = "1";//字符集
$language = "1";//语言
$data_type="xml";//加密报文的数据类型（xml/json）


$private_key_password = "100000178_204500";	//商户私钥证书密码
$pfxfilename = $pathcer."bfkey_100000178@@100000916.pfx";  //注意证书路径是否存在
$cerfilename = $pathcer."bfkey_100000178@@100000916.cer";//注意证书路径是否存在

$IsTest = false;//正式（true）/测试（false）

$PcWap = 1;    //PC(1)、WAP(2)

$page_url="http://localhost:8019/";//页面跳转地址
$return_url="http://10.1.60.131:8019/Action/ReturnAction.php";//服务器跳转地址

if(!file_exists($pfxfilename))
{
    die("私钥证书不存在！<br>");
}
if(!file_exists($cerfilename))
{
    die("公钥证书不存在！<br>");
}

function get_transid(){//生成时间戳
	return strtotime(date('Y-m-d H:i:s',time()));	
}
function rand4(){//生成四位随机数
	return rand(1000,9999);
}
function return_time(){//生成时间

	return date('YmdHis',time());
	
}