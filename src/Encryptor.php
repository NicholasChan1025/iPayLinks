<?php
/**
 * Created by PhpStorm.
 * User: sinchan
 * Date: 2018/12/8
 * Time: 11:57
 */

namespace IPayLinks;


class Encryptor
{
    /**
     * 生成签名
     *
     * @param string     $method 请求方法 "POST"
     * @param string     $urlPath
     * @param array     $params 表单参数
     * @param string     $secret 密钥
     */
    static public function makeSig($params, $secret)
    {
        $mk = self::makeSource($params);
        $mk .= "&pkey=".$secret;
        $mySign = strtolower(md5($mk));
        return $mySign;
    }

    static private function makeSource($params)
    {
        ksort($params);
        $queryString = array();
        foreach ($params as $key => $val )
        {
            array_push($queryString, $key . '=' . trim($val));
        }
        $queryString = join('&', $queryString);

        return $queryString;
    }
}