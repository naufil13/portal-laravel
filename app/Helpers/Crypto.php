<?php

class Crypto
{
	public static function decryptData($ciphertext, $key)
	{
		if (strlen($ciphertext) < 88) {
			return $ciphertext;
		}
		$c = base64_decode($ciphertext);
		$ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len = 32);
		$ciphertext_raw = substr($c, $ivlen + $sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);

		if (hash_equals($hmac, $calcmac)) {
			return $original_plaintext;
		} else return 0;
	}

	public static function encryptData($plaintext, $key)
	{
		$ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
		$ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

		return $ciphertext;
	}

	public static function getAwsEncryptionKey()
	{
		return "WrgLy063sS6VqlIASRxTnXKKCaaYCbQl";
		// if($value != false){
		// 	if($value == false){
				$client = new SecretsManagerClient([
					'profile'=>'default',
					'version' => 'latest',
					'region' => 'us-east-2',
				]);
				$secretName = env('AWS_SECRET_NAME');
				$result = $client->getSecretValue([
					'SecretId' => $secretName,
				]);
				try {
					$result = $client->getSecretValue([
						'SecretId' => $secretName,
					]);
				} catch (AwsException $e) {
					$error = $e->getAwsErrorCode();
					if ($error == 'DecryptionFailureException') {
						// Secrets Manager can't decrypt the protected secret text using the provided AWS KMS key.
						// Handle the exception here, and/or rethrow as needed.
						throw $e;
					}
					if ($error == 'InternalServiceErrorException') {
						// An error occurred on the server side.
						// Handle the exception here, and/or rethrow as needed.
						throw $e;
					}
					if ($error == 'InvalidParameterException') {
						// You provided an invalid value for a parameter.
						// Handle the exception here, and/or rethrow as needed.
						throw $e;
					}
					if ($error == 'InvalidRequestException') {
						// You provided a parameter value that is not valid for the current state of the resource.
						// Handle the exception here, and/or rethrow as needed.
						throw $e;
					}
					if ($error == 'ResourceNotFoundException') {
						// We can't find the resource that you asked for.
						// Handle the exception here, and/or rethrow as needed.
						throw $e;
					}
				}
				if (isset($result['SecretString'])) {
					$secret = $result['SecretString'];
				} else {
					$secret = base64_decode($result['SecretBinary']);
				}
				$arr= explode(":",$secret);
				if (preg_match('/"([^"]+)"/', $arr[1], $m)) {
				} else {
				}
				;
				return $m[1];
			// }
			return env('ENCRYP_KEY');
		// }
	}
}
