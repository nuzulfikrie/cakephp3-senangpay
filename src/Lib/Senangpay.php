<?php
namespace Paanblogger\Cakephp3Senangpay\Lib;

class Senangpay
{
	protected $senangpay_url;
	protected $merchant_id;
	protected $secret_key;
	protected $order_id;
	protected $status_id;
	protected $transaction_id;
	protected $msg;
	public function __construct(array $config = [])
	{
		$config = array_merge([
            'senangpay_url' => "https://app.senangpay.my/payment/",
            "merchant_id" => "",
            "secret_key" => ""
        ], $config);
        $this->senangpay_url = $config['senangpay_url'];
        $this->merchant_id = $config['merchant_id'];
        $this->secret_key = $config['secret_key'];
	}
	private function sentHash($detail , $amount , $order_id)
	{
		$string = $this->secret_key.$detail.$amount.$order_id;
		$hash = md5($string);
		return $hash;
	}
	private function receiveHash($status_id , $order_id , $transaction_id , $msg)
	{
		$hashed_string = md5($this->secret_key.urldecode($status_id).urldecode($order_id).urldecode($transaction_id).urldecode($msg));
		return $hashed_string;
	}
	public function getOrderId()
	{
		return $this->order_id;
	}
	public function getStatusId()
	{
		return $this->status_id;
	}
	public function getTransactionId()
	{
		return $this->transaction_id;
	}
	public function getMsg()
	{
		return $this->msg;
	}
	public function checkReceiveHash(array $data = [])
	{
		extract($data);
		if(isset($status_id) AND isset($order_id) AND isset($msg) AND isset($transaction_id) AND isset($hash))
		{
			$hashed_string = $this->receiveHash($status_id , $order_id , $transaction_id , $msg);
			if($hashed_string == $hash)
			{
				$this->order_id = urldecode($order_id);
				$this->status_id = urldecode($status_id);
				$this->transaction_id = urldecode($transaction_id);
				$this->msg = str_replace("_" , " " , urldecode($msg));
				return true;
			}
			else
				return false;
		}
		return false;
	}
	public function generatePaymentLink(array $data = [])
	{
		extract($data);
		$url = "";
		if(isset($detail) AND isset($amount) AND isset($order_id))
		{
			if(!isset($name))
				$name = "";
			if(!isset($phone))
				$phone = "";
			if(!isset($email))
				$email = "";
			$hash = $this->sentHash($detail , $amount , $order_id);
			$url = $this->senangpay_url.$this->merchant_id."?detail=".$detail."&amount=".$amount."&order_id=".$order_id."&name=".urlencode($name)."&email=".urlencode($email)."&phone=".urlencode($phone)."&hash=".$hash;
		}
		return $url;
	}
}