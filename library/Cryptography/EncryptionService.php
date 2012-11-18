<?php
/**
 * Provides the ability to hash with salt and verify plaintext 
 *
 * @package     Cryptography
 * @subpackage  Services
 * @version     1
 * @author      Ozan Turgut <ozanturgut@gmail.com>
 */
class Cryptography_EncryptionService {
	const DEFAULT_ENCYRPTION_ALGORITHM = MCRYPT_RIJNDAEL_128;
	const DEFAULT_ENCRYPTION_MODE = MCRYPT_MODE_CBC;

	private $cipher = NULL;
	private $iv_size = 0;
	private $key = NULL;

	function __construct($key = NULL, $cipher = NULL){
		if(!is_null($cipher)) $this->setCipher($cipher);
		else $this->setCipher(mcrypt_module_open(Cryptography_EncryptionService::DEFAULT_ENCYRPTION_ALGORITHM, '', Cryptography_EncryptionService::DEFAULT_ENCRYPTION_MODE, ''));

		if(!is_null($key)) $this->setKey($key);
	}

	// The cipher must be created via mcrypt_modele_open
	public function setCipher($cipher){
		$this->cipher = $cipher;

		// set initialization vector size
		$this->iv_size = mcrypt_enc_get_iv_size($cipher);
	}

	// Secret password
	public function setKey($key){
		$this->key = $key;
	}

	// Initialization vector with length based on current cipher
	public function generateIV(){
		$iv = mcrypt_create_iv($this->iv_size, MCRYPT_RAND);
		return $iv;
	}

	// Encrypt a string and prepend the initialization vector to the first block
	public function encrypt($clearText, $iv = NULL){
		if(is_null($iv)) $iv = $this->generateIV();

		if (mcrypt_generic_init($this->cipher, $this->key, $iv) != -1)
		{
			$cipherText = mcrypt_generic($this->cipher, $clearText );
			mcrypt_generic_deinit($this->cipher);

			return $iv . $cipherText;
		}
	}

	// Decrypt a string which was encrypted using the encrypt method of this class
	public function decrypt($encryptedTextWithIV){
		$iv = substr($encryptedTextWithIV, 0, $this->iv_size);
		$encryptedText = substr($encryptedTextWithIV, $this->iv_size);

		if (mcrypt_generic_init($this->cipher, $this->key, $iv) != -1)
		{
			$clearText = mdecrypt_generic($this->cipher, $encryptedText);
			mcrypt_generic_deinit($this->cipher);

			return $clearText;
		}
	}
}

?>