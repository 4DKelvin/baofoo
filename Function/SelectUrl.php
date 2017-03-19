<?php
/**
 * Description of SelectUrl
 *
 * @author Administrator
 */
class SelectUrl {
    /**
     * 
     * @param type $IsTest  正式（true）/测试(false)
     * @param int $InterfaceType   1(PC),2(WAP)
     * @param type $RequestType   1(交易接口),2(订单查询),3(绑定ID查询)
     * @return string
     */
    public static function Url($IsTest,$InterfaceType,$RequestType){
        $UrlString="";
        
        if($InterfaceType ==1){
                $UrlString .= "quickpay/pc/";//PC
            }else{
                $UrlString .= "quickpay/wap/";//WAP
            }
        switch ($RequestType){
            case 1:
                $UrlString .= "order";//交易接口
                break;
            case 2:
                $UrlString .= "queryorder";//订单查询
                break;
            case 3:
                $UrlString .= "querybind";//绑定ID查询
                break;
        }
        if($IsTest){
            $UrlString = "https://gw.baofoo.com/".$UrlString;
        }else{
            $UrlString = "https://vgw.baofoo.com/".$UrlString;
        }
        RETURN $UrlString;
    }
    //put your code here
}
