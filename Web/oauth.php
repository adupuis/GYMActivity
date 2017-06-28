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
require_once 'third_parties/google-api-php-client-2.1.3/vendor/autoload.php';

// needed by google client. Also seems like a php property can be used
// so we should test without it before
date_default_timezone_set('UTC');


function get_google_email($oauth_code) {
    $client = create_google_client();
    $client->fetchAccessTokenWithAuthCode($oauth_code);
    $access_token = $client->getAccessToken();
    $oauth2Service = new Google_Service_Oauth2($client);
    $me = $oauth2Service->userinfo_v2_me->get();
    $email = $me->getEmail();
    return $email;
}

function create_google_client(){
    $client = new Google_Client();
    $client->setAuthConfig('client_secrets.json');
    $client->addScope('profile');
    $client->addScope('email');
    return $client;
}

?>
