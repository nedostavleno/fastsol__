<?php
/**
 * BelovTest
 * Utils
 */
namespace App\Services;

use App\Services\App;
use App\Services\DB;

class Utils
{

	// Генерация случайного токена
	public static function genToken()
	{
		return password_hash(str_shuffle('abcdefghjkmnpqrstuvwxyz0123456789'), PASSWORD_DEFAULT);
	}

	// Установка cookie
	public static function setCookie($name, $value, $expires)
	{
		$expires ? $expires = time() + ($expires * 86400) : $expires = false;
		return setcookie($name, $value, $expires, '/');
	}

	// Запись логов
	public static function setLogs($infoLog)
	{
		if(!empty($infoLog) && App::$info['auth'] === true)
		{
			// Обновление последнего доступа пользователя
			DB::unassoc("UPDATE `{prefix}_users` SET ?u WHERE ?w", [
				'ip' => \App\Models\User::getIp(),
				'lastdate' => time()
			], [
				'id' => App::$info['user']['id']
			]);

			// Добавление записи в логи
			DB::unassoc("INSERT INTO `{prefix}_logs` SET ?u", [
				'userId' => App::$info['user']['id'],
				'info' => $infoLog,
				'date' => time()
			]);
			DB::close();
		}
	}

	// Формирование JSON-ответа
	public static function jsonResponse($data, $type, $add = [ 'response' => 'alert' ])
	{
		$json['type'] = $type;
		$json['data'] = $data;

		foreach($add as $key => $value) $json[$key] = $value;
		
		die(json_encode($json));
	}

	// Шифрование текста
	public static function crypt($cryptKey, $text)
	{
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($text, $cipher, $cryptKey, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $cryptKey, $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
		return $ciphertext;
	}

	// Дешифрование текста
	public static function decrypt($cryptKey, $text)
	{
		$c = base64_decode($text);
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$plaintext = openssl_decrypt($ciphertext_raw, $cipher, $cryptKey, $options=OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $cryptKey, $as_binary=true);
		if(hash_equals($hmac, $calcmac)) return $plaintext;
	}
}

?>
