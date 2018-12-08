<?php
/**
 * Created by PhpStorm.
 * User: sinchan
 * Date: 2018/12/7
 * Time: 10:40
 */

namespace IPayLinks\Payment\Order;


use IPayLinks\Encryptor;
use IPayLinks\Kernel\BaseClient;

class Client extends BaseClient
{
    public function unify(array $param){
        $param += [
            'version'=>$this->app['config']['version'],
            'merchantId'=>$this->app['config']['merchant_id'],
            'accessType'=>$this->app['config']['access_type'],
            'transTimeout'=>$this->app['config']['trans_timeout'],
            'carrierId'=>$this->app['config']['carrier_id'],
            'terminalType'=>$this->app['config']['terminal_type'],
            'charset'=>$this->app['config']['charset'],
            'signType'=>$this->app['config']['sign_type']
        ];
        $param['sign'] = Encryptor::makeSig($param,$this->app['config']['pkey']);

        return json_encode($param);
    }

    public function query($param){

        $param += [
            'version'=>$this->app['config']['version'],
            'merchantId'=>$this->app['config']['merchant_id'],
            'transType'=>'query',
            'charset'=>$this->app['config']['charset'],
            'signType'=>$this->app['config']['sign_type']
        ];
        $param['sign'] = Encryptor::makeSig($param,$this->app['config']['pkey']);
        return $this->httpPost($this->app['config']['url'],$param);
    }


    public function refund($param){
        $param += [
            'version'=>$this->app['config']['version'],
            'merchantId'=>$this->app['config']['merchant_id'],
            'transType'=>'refund',
            'charset'=>$this->app['config']['charset'],
            'signType'=>$this->app['config']['sign_type']
        ];
        $param['sign'] = Encryptor::makeSig($param,$this->app['config']['pkey']);
        return $this->httpPost($this->app['config']['url'],$param);
    }
}