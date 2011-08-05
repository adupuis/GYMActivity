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

?>
<!-- Here is the default application menu -->

<script type="text/javascript">
    $(function() {
        var d=300;
        $('#navigation a').each(function(){
            $(this).stop().animate({
                'marginTop':'-80px'
            },d+=150);
        });

        $('#navigation > li').hover(
        function () {
            $('a',$(this)).stop().animate({
                'marginTop':'-2px'
            },200);
        },
        function () {
            $('a',$(this)).stop().animate({
                'marginTop':'-80px'
            },200);
        }
    );
    });
</script>

<ul id="navigation">
 <li class="home"><a href="home.php"><span>Home</span></a></li>
 <li class="cra"><a href="home_cra.php"><span>CRA</span></a></li>
 <li class="conges"><a href="home_conges.php"><span>Congés</span></a></li>
 <li class="reporting"><a href="home_reporting.php"><span>Reporting</span></a></li>
 <li class="admin"><a href="home_admin.php"><span>Admin</span></a></li>
 <li class="contact"><a href="home_contact.php"><span>Contact</span></a></li>
 <li class="logout"><a href="logout.php"><span>Logout</span></a></li>
</ul>
<!-- End of menu -->
