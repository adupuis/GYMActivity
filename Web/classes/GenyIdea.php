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

class GenyIdea extends GenyDatabaseTools  {
	public function __construct( $id = -1 ) {
		parent::__construct("Ideas",  "idea_id", $id);
		$this->title = '';
		$this->description = '';
		$this->votes = -1;
		$this->status_id = -1;
		$this->submitter = -1;
		$this->submission_date = '';
		if( $id > -1 ) {
			$this->loadIdeaById( $id );
		}
	}
	
	public function insertNewIdea( $id, $idea_title, $idea_description, $idea_votes, $idea_status_id, $idea_submitter, $idea_submission_date ) {
		if( $this->config->debug ) {
			echo "<!-- DEBUG: GenyIdea new idea insertion - id: $id - idea_title: $idea_title - idea_description: $idea_description - idea_votes: $idea_votes - idea_status_id: $idea_status_id - idea_submitter: $idea_submitter - submission_date: $idea_submission_date -->\n";
		}
		if( !is_numeric( $id ) && $id != 'NULL' ) {
			return -1;
		}
		if( !is_numeric( $idea_status_id ) ) {
			return -1;
		}
		if( !is_numeric( $idea_submitter ) ) {
			return -1;
		}
		$query = "INSERT INTO Ideas VALUES($id,'".mysql_real_escape_string( $idea_title )."','".mysql_real_escape_string( $idea_description )."','".$idea_votes."','".$idea_status_id."','".$idea_submitter."','".$idea_submission_date."')";
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

	public function removeIdea( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM Ideas WHERE idea_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getIdeasListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("idea_id=1","idea_status_id=2")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT idea_id,idea_title,idea_description,idea_votes,idea_status_id,idea_submitter,idea_submission_date FROM Ideas";
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
			error_log("[GYMActivity::DEBUG] GenyIdea MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$idea_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_idea = new GenyIdea();
				$tmp_idea->id = $row[0];
				$tmp_idea->title = $row[1];
				$tmp_idea->description = $row[2];
				$tmp_idea->votes = $row[3];
				$tmp_idea->status_id = $row[4];
				$tmp_idea->submitter = $row[5];
				$tmp_idea->submission_date = $row[6];
				$idea_list[] = $tmp_idea;
			}
		}
// 		mysql_close();
		return $idea_list;
	}

	public function getAllIdeas() {
		return $this->getIdeasListWithRestrictions( array() );
	}

	public function getAllIdeasSortedByVotes() {
		$query = "SELECT idea_id,idea_title,idea_description,idea_votes,idea_status_id,idea_submitter,idea_submission_date FROM Ideas ORDER BY idea_votes DESC";
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyIdea MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$idea_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_idea = new GenyIdea();
				$tmp_idea->id = $row[0];
				$tmp_idea->title = $row[1];
				$tmp_idea->description = $row[2];
				$tmp_idea->votes = $row[3];
				$tmp_idea->status_id = $row[4];
				$tmp_idea->submitter = $row[5];
				$tmp_idea->submission_date = $row[6];
				$idea_list[] = $tmp_idea;
			}
		}
// 		mysql_close();
		return $idea_list;

	}

	public function getIdeasListByTitle( $title ) {
		return $this->getIdeasListWithRestrictions( array("idea_title='".mysql_real_escape_string( $title )."'") );
	}

	public function getIdeasListByStatusId( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		return $this->getIdeasListWithRestrictions( array("idea_status_id=$id") );
	}

	public function getIdeasListBySubmitter( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		return $this->getIdeasListWithRestrictions( array("idea_submitter=$id") );
	}

	public function loadIdeaByTitle( $title ) {
		$ideas = $this->getIdeasListWithRestrictions( array("idea_title='".mysql_real_escape_string($title)."'") );
		$idea = $ideas[0];
		if( isset( $idea ) && $idea->id > -1 ) {
			$this->id = $idea->id;
			$this->title = $idea->title;
			$this->description = $idea->description;
			$this->votes = $idea->votes;
			$this->status_id = $idea->status_id;
			$this->submitter = $idea->submitter;
			$this->submission_date = $idea->submission_date;
		}
	}

	public function loadIdeaById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$ideas = $this->getIdeasListWithRestrictions( array("idea_id=".$id) );
		$idea = $ideas[0];
		if( isset( $idea ) && $idea->id > -1 ) {
			$this->id = $idea->id;
			$this->title = $idea->title;
			$this->description = $idea->description;
			$this->votes = $idea->votes;
			$this->status_id = $idea->status_id;
			$this->submitter = $idea->submitter;
			$this->submission_date = $idea->submission_date;
		}
	}
}

?>