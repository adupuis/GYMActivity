<?php

include_once 'GenyWebConfig.php';

class GenyApiKey {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->name = '';
		$this->timestamp = -1;
		if($id > -1)
			$this->loadApiKeyById($id);
	}
	public function insertNewApiKey($id,$profile_id,$data){
		if( !is_numeric($id) || !is_numeric($profile_id) )
			return -1;
		$timestamp = time();
		// Il est impossible d'avoir 2 clés pour un profil, donc nous supprimons au préalable toutes les clés de ce profil.
		$query = "DELETE FROM ApiKeys WHERE profile_id=$profile_id";
		mysql_query( $query, $this->handle );
		$query = "INSERT INTO ApiKeys VALUES($id,$profile_id,'".mysql_real_escape_string($data)."',$timestamp)";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyApiKey MySQL query : $query -->\n";
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getApiKeysListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT api_key_id,profile_id,api_key_data,api_key_timestamp FROM ApiKeys";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " AND ";
				}
			}
		}
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyApiKey MySQL query : $query -->\n";
		$result = mysql_query($query, $this->handle);
		$api_key_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_client = new GenyApiKey();
				$tmp_client->id = $row[0];
				$tmp_client->profile_id = $row[1];
				$tmp_client->data = $row[2];
				$tmp_client->timestamp = $row[3];
				$api_key_list[] = $tmp_client;
			}
		}
// 		mysql_close();
		return $api_key_list;
	}
	public function getAllApiKeys(){
		return $this->getApiKeysListWithRestrictions( array() );
	}
	public function loadApiKeyByProfileId($id){
		if( is_numeric($id) ){
			$apikeys = $this->getApiKeysListWithRestrictions(array("profile_id=".$id));
			$apikey = $apikeys[0];
			if(isset($apikey) && $apikey->id > -1){
				$this->id = $apikey->id;
				$this->profile_id = $apikey->profile_id;
				$this->data = $apikey->data;
				$this->timestamp = $apikey->timestamp;
			}
		}
	}
	public function loadApiKeyById($id){
		if( is_numeric($id) ){
			$apikeys = $this->getApiKeysListWithRestrictions(array("api_key_id=".$id));
			$apikey = $apikeys[0];
			if(isset($apikey) && $apikey->id > -1){
				$this->id = $apikey->id;
				$this->profile_id = $apikey->profile_id;
				$this->data = $apikey->data;
				$this->timestamp = $apikey->timestamp;
			}
		}
	}
	public function loadApiKeyByData($data){
		$apikeys = $this->getApiKeysListWithRestrictions(array("api_key_data='".mysql_real_escape_string($data)."'"));
		$apikey = $apikeys[0];
		if(isset($apikey) && $apikey->id > -1){
			$this->id = $apikey->id;
			$this->profile_id = $apikey->profile_id;
			$this->data = $apikey->data;
			$this->timestamp = $apikey->timestamp;
		}
	}
	// Generation d'une clé API plutôt sécurisé à partir des informations d'un objet profile.
	public function generateApiKey($profile_object){
		$seed = $profile_object->id.$profile_object->login.rand().time();
		return sha1(str_rot13(base64_encode(hash('sha512', $seed))));
	}
	public function updateString($key,$value){
		$this->updates[] = "$key='".mysql_real_escape_string($value)."'";
	}
	public function updateInt($key,$value){
		$this->updates[] = "$key=".mysql_real_escape_string($value)."";
	}
	public function updateBool($key,$value){
		$this->updates[] = "$key=".mysql_real_escape_string($value)."";
	}
	public function commitUpdates(){
		$query = "UPDATE ApiKeys SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE api_key_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyApiKey MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
}
?>