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

class GenyIdeaVote extends GenyDatabaseTools {
	public $id = -1;
	public $positive_vote = -1;
	public $negative_vote = -1;
	public $profile_id = -1;
	public $idea_id = -1;
	public function __construct( $id = -1 ) {
		parent::__construct("IdeaVotes",  "idea_vote_id");
		$this->id = -1;
		$this->positive_vote = -1;
		$this->negative_vote = -1;
		$this->profile_id = -1;
		$this->idea_id = -1;
		if( $id > -1 ) {
			$this->loadIdeaVoteById( $id );
		}
	}

	public function insertNewIdeaVote( $id, $idea_positive_vote, $idea_negative_vote, $profile_id, $idea_id ) {
		if( !is_numeric( $id ) && $id != 'NULL' ) {
			return -1;
		}
		if( !is_numeric( $idea_positive_vote ) ) {
			return -1;
		}
		if( !is_numeric( $idea_negative_vote ) ) {
			return -1;
		}
		if( !is_numeric( $profile_id ) ) {
			return -1;
		}
		if( !is_numeric( $idea_id ) ) {
			return -1;
		}
		$query = "INSERT INTO IdeaVotes VALUES($id,'".$idea_positive_vote."','".$idea_negative_vote."','".$profile_id."','".$idea_id."')";
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyIdeaVote MySQL query : $query",0);
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

	public function removeIdeaVote( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM IdeaVotes WHERE idea_vote_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getIdeaVotesListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("idea_id=1","idea_status_id=2")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT idea_vote_id,idea_positive_vote,idea_negative_vote,profile_id,idea_id FROM IdeaVotes";
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
			error_log("[GYMActivity::DEBUG] GenyIdeaVote MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$idea_vote_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_idea_vote = new GenyIdeaVote();
				$tmp_idea_vote->id = $row[0];
				$tmp_idea_vote->idea_positive_vote = $row[1];
				$tmp_idea_vote->idea_negative_vote = $row[2];
				$tmp_idea_vote->profile_id = $row[3];
				$tmp_idea_vote->idea_id = $row[4];
				$idea_vote_list[] = $tmp_idea_vote;
			}
		}
		mysql_close();
		return $idea_vote_list;
	}

	public function getAllIdeaVotes() {
		return $this->getIdeaVotesListWithRestrictions( array() );
	}

	public function getIdeaVotesListByProfileId( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		return $this->getIdeaVotesListWithRestrictions( array("profile_id=$id") );
	}

	public function getIdeaVotesListByIdeaId( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		return $this->getIdeaVotesListWithRestrictions( array("idea_id=$id") );
	}

	public function getIdeaVotesListByProfileAndIdeaId( $profile_id, $idea_id ) {
		if( !is_numeric( $profile_id ) || !is_numeric( $idea_id ) ) {
			return array();
		}
		return $this->getIdeaVotesListWithRestrictions( array("profile_id=$profile_id", "idea_id=$idea_id") );
	}

	public function loadIdeaVoteById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$idea_votes = $this->getIdeaVotesListWithRestrictions( array( "idea_vote_id=".mysql_real_escape_string( $id ) ) );
		$idea_vote = $idea_votes[0];
		if( isset( $idea_vote ) && $idea_vote->id > -1 ) {
			$this->id = $idea_vote->id;
			$this->positive_vote = $idea_vote->positive_vote;
			$this->negative_vote = $idea_vote->negative_vote;
			$this->profile_id = $idea_vote->profile_id;
			$this->idea_id = $idea_vote->idea_id;
		}
	}
}

?>