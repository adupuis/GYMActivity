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


include_once 'GenyWebConfig.php';
include_once 'GenyDatabaseTools.php';

class GenyIdeaMessage extends GenyDatabaseTools {
	public function __construct( $id = -1 ) {
		parent::__construct("IdeaMessages",  "idea_message_id");
		$this->id = -1;
		$this->content = '';
		$this->submission_date = '';
		$this->profile_id = -1;
		$this->idea_id = -1;
		if( $id > -1 ) {
			$this->loadIdeaMessageById( $id );
		}
	}

	public function insertNewIdeaMessage( $id, $idea_message_content, $submission_date, $profile_id, $idea_id ) {
		if( !is_numeric( $id ) && $id != 'NULL' ) {
			return -1;
		}
		if( !is_numeric( $profile_id ) ) {
			return -1;
		}
		if( !is_numeric( $idea_id ) ) {
			return -1;
		}
		$query = "INSERT INTO IdeaMessages VALUES($id,'".mysql_real_escape_string( $idea_message_content )."','".$submission_date."','".$profile_id."','".$idea_id."')";
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyIdeaMessage MySQL query : $query",0);
		}
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyIdea MySQL query : $query",0);
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}

	public function removeIdeaMessage( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM IdeaMessages WHERE idea_message_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getIdeaMessagesListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("idea_id=1","idea_status_id=2")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT idea_message_id,idea_message_content,idea_message_submission_date,profile_id,idea_id FROM IdeaMessages";
		if( count( $restrictions ) > 0 ) {
			$query .= " WHERE ";
			foreach( $restrictions as $key => $value ) {
				$query .= $value;
				if( $key != $last_index ) {
					$query .= " AND ";
				}
			}
		}
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyIdeaMessage MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$idea_message_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_idea_message = new GenyIdeaMessage();
				$tmp_idea_message->id = $row[0];
				$tmp_idea_message->content = $row[1];
				$tmp_idea_message->submission_date = $row[2];
				$tmp_idea_message->profile_id = $row[3];
				$tmp_idea_message->idea_id = $row[4];
				$idea_message_list[] = $tmp_idea_message;
			}
		}
// 		mysql_close();
		return $idea_message_list;
	}

	public function getAllIdeaMessages() {
		return $this->getIdeaMessagesListWithRestrictions( array() );
	}

	public function getLastIdeaMessage() {
		$query = "SELECT idea_message_id,idea_message_content,idea_message_submission_date,profile_id,idea_id FROM IdeaMessages ORDER BY idea_message_submission_date DESC LIMIT 1";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIdeaMessage MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );

		if( mysql_num_rows( $result ) != 0 ) {
			$row = mysql_fetch_row( $result );
			$tmp_idea_message = new GenyIdeaMessage();
			$tmp_idea_message->id = $row[0];
			$tmp_idea_message->content = $row[1];
			$tmp_idea_message->submission_date = $row[2];
			$tmp_idea_message->profile_id = $row[3];
			$tmp_idea_message->idea_id = $row[4];
		}
		return $tmp_idea_message;
	}

	public function getIdeaMessagesListByProfileId( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		return $this->getIdeaMessagesListWithRestrictions( array("profile_id=$id") );
	}

	public function getIdeaMessagesListByIdeaId( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		return $this->getIdeaMessagesListWithRestrictions( array("idea_id=$id") );
	}

	public function getIdeaMessagesListByProfileAndIdeaId( $profile_id, $idea_id ){
		if( !is_numeric( $profile_id ) || !is_numeric( $idea_id ) ) {
			return array();
		}
		return $this->getIdeaMessagesListWithRestrictions( array("profile_id=$profile_id", "idea_id=$idea_id") );
	}

	public function loadIdeaMessageById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$idea_messages = $this->getIdeaMessagesListWithRestrictions( array( "idea_message_id=".mysql_real_escape_string( $id ) ) );
		$idea_message = $idea_messages[0];
		if( isset( $idea_message ) && $idea_message->id > -1 ) {
			$this->id = $idea_message->id;
			$this->content = $idea_message->content;
			$this->submission_date = $idea_message->submission_date;
			$this->profile_id = $idea_message->profile_id;
			$this->idea_id = $idea_message->idea_id;
		}
	}
}

?>