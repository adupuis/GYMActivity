
<!-- Here is the application menu -->

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
