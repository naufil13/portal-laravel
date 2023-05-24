<?php
use Illuminate\Support\Str;

class Token
{
	function generateAppToken(){

		$token = Token::generateRandomString();
		$token = $token.base64_encode(date("Y-m-d H:i:s"));
		
		return $token;
	}

	function generateRandomString() 
	{
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str = substr(str_shuffle($permitted_chars), 0, 16);
		
		return $str;
	}
	
    public function generateTicketNo(){
    	
		return "TN-".rand(pow(10, 5-1), pow(10, 5)-1);

    }
}
