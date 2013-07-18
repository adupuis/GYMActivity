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

include_once 'GenyProfile.php';
class CheckIdentity {
	public function __construct(){
	}
	public function isAllowed($username,$needed_rights){
		$tmp_profile = new GenyProfile();
		$tmp_profile->loadProfileByUsername($username);
		if ( $tmp_profile->id <= 0 )
			return false;
		if( is_array($needed_rights) ){
			if( ! in_array( $tmp_profile->rights_group_id, $needed_rights ) )
				return false;
		}
		else{
			return false;
		}
		if(!$tmp_profile->is_active)
			return false;
		// If we arrive here the user exists in database, is loggedin and have a group rights id compatible with required rights
		return true;
	}
}
?>