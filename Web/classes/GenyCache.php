<?php
//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
//  http://www.genymobile.com
// 
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 3 of the License, or
//  (at your option) any later version.
// 
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
// 
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the
//  Free Software Foundation, Inc.,
//  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

class GenyCache {
	private $m_buffer;
	private $m_encryption_key;
	private $m_cache_file;
	private $m_expiration_timestamp;
	private $m_cache_directory;

// Constructor
	
	public function __construct($direcory = "./", $file="",$encryption_key="ABCD123456789azerty",$expiration_timestamp=0){
		$this->m_buffer = "";
		$this->m_cache_directory = $direcory;
		$this->m_cache_file = $file;
		$this->m_encryption_key = $encryption_key;
		$this->m_expiration_timestamp = $expiration_timestamp;
	}
	
// Cache interaction functions
	
	// Start caching (and reset the previous cache buffer if it was set)
	public function startCaching(){
		$this->setBuffer("");
		ob_start();
	}
	
	// Stop caching and save the cached data in the current buffer (over any previous content). Flush the cache buffer too.
	public function stopCaching(){
		$this->setBuffer( ob_get_contents() );
		ob_end_flush();
	}
	
	// Return the current cache buffer (alias for buffer() function).
	public function getCache(){
		return $this->buffer();
	}
	
	// Write the cache onto disk
	public function storeCache(){
		// Encrypt content
		$content = "<?php \$stored_expiration_timestamp=$this->m_expiration_timestamp; \$stored_cache='".base64_encode($this->buffer())."' ; ?>";
		$encrypted_content = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->encryptionKey()), $content, MCRYPT_MODE_CBC, md5(md5($this->encryptionKey()))));

		$result = file_put_contents("$this->m_cache_directory/$this->m_cache_file",$encrypted_content);
		if($result === false){
			return false;
		}
		elseif ($result <= 0) {
			return false;
		}
		return true;
	}
	
	// Load cache from file
	public function loadCache(){
		// decrypt
		$encrypted_content = file_get_contents("$this->m_cache_directory/$this->m_cache_file");
		if( ! $encrypted_content ){
			return false;
		}
		$decrypted_content = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->encryptionKey()), base64_decode($encrypted_content), MCRYPT_MODE_CBC, md5(md5($this->encryptionKey()))), "\0");
		eval($decrypted_content);
		if( isset($stored_cache) && isset($stored_expiration_timestamp) ){
			$this->setBuffer( $stored_cache ); // $stored_cache is defined in the php eval()-ed.
			$this->setExpirationTimestamp($stored_expiration_timestamp);
			return true;
		}
		else {
		    return false;
		}
		
	}
	
	public function isCacheExpired(){
		if( $this->expirationTimestamp() < time() ){
			return true;
		}
		return false;
	}
	
// Accessors

// Getters
	
	public function buffer(){
		return $this->m_buffer;
	}
	
	public function encryptionKey(){
		return $this->m_encryption_key;
	}
	
	public function cacheDirectory(){
		return $this->m_cache_directory;
	}
	
	public function cacheFile(){
		return $this->m_cache_file;
	}
	
	public function expirationTimestamp(){
		return $this->m_expiration_timestamp;
	}

// Setters

	public function setBuffer($data){
		$this->m_buffer = $data;
	}
	
	public function setEncryptionKey($data){
		$this->m_encryption_key = $data;
	}
	
	public function setCacheDirectory($data){
		if( ! file_exists($data) ){
			if( ! mkdir($data)){
				return false;
			}
		}
		if( ! file_exists($data.'/.htaccess') ){
			file_put_contents($data.'/.htaccess',"deny from all\n");
		}
		$this->m_cache_directory = $data;
	}
	
	public function setCacheFile($data){
		$this->m_cache_file = $data;
	}
	
	public function setExpirationTimestamp($data){
		$this->m_expiration_timestamp = $data;
	}
}

?>