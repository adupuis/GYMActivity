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
	
	// Stop caching and save the cahced data in the current buffer (over any previous content)
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
		// TODO: encrypt content
		file_put_contents("$this->m_cache_directory/$this->m_cache_file","<?php \$stored_expiration_timestamp=$this->m_expiration_timestamp; \$stored_cache='".base64_encode($this->buffer())."' ; ?>";
	}
	
	// Load cache from file
	public function loadCache(){
		$content = file_get_contents("$this->m_cache_directory/$this->m_cache_file");
		// decrypt
		eval($uncrypted_content);
		$this->setBuffer( base64_decode($stored_cache));
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