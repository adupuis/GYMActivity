#!/usr/bin/perl

use strict;
use warnings;

sub processFile($){
	my $f = shift;
	print "pf: f=$f\n";
	open(my $ifh,"<$f");
	open(my $fh, ">submodules/$f");
	open(my $fhm, ">submodules/$f.meta");
	print $fhm "<?php\n";
	my $write = 0;
	my $phase = '';
	my @bi = ();
	while(<$ifh>){
# 		print "\t\t>> $_";
		chomp;
		if(/^<\?php/ && $phase ne 'noway'){
			$write = 1;
			$phase = 'php';
			print "\t[debug] entering $phase phase.\n";
			print "[debug] write ON\n";
		}
		elsif(/^\?>/ && $write){
			print $fh "$_\n";
			$write = 0;
			print "\t[debug] out of $phase phase.\n";
			print "[debug] write OFF\n";
		}
		elsif(/^<div id="mainarea">/){
			$write=1;
			$phase = 'mainarea';
			print "\t[debug] entering $phase phase.\n";
			print "[debug] write ON\n";
		}
		elsif(/^<div id="bottomdock">/){
			$write=0;
			$phase = 'bottomdock';
# 			$_ .= "\n<h3 class='italic'>Liens rapides</h3>\n<div id='services' class='widget clearfix'>\n";
			print "\t[debug] entering $phase phase.\n";
			print "[debug] write ON\n";
		}
		elsif( /^<\/div>/ && $phase eq 'bottomdock' ){
			$write = 0;
# 			print $fh "</div>\n$_\n";
			print $fh "<?php\n\t\$bottomdock_items = array(".join(',',@bi).");\n?>\n";
			print "\t[debug] out of $phase phase.\n";
			$phase = "noway";
			print "[debug] write OFF\n";
		}
		elsif(/^\s*include[^']*'([^']+)'/ && $phase eq 'bottomdock' ){
			print "\t[debug] push $1 on \@bi.\n";
			push @bi, "'$1'";
		}
		elsif(/<script/ && !$write && $phase ne 'mainarea'){
			$write=1;
			$phase='lonely_script';
			print "\t[debug] entering $phase phase.\n";
			print "[debug] write ON\n";
		}
		elsif(/<\/script>/ && $write && $phase eq 'lonely_script'){
			$write=0;
			print $fh "$_\n";
			print "\t[debug] out of $phase phase.\n";
			print "[debug] write OFF\n";
		}
		
		if(/^include_once 'header.php';/){
			next;
		}
		if(/^include_once 'menu.php';/){
			next;
		}
		if(/^\$required_group_rights/){
			print $fhm "$_\n";
			next;
		}
		if(/^\$header_title/){
			print $fhm "$_\n";
			next;
		}

		print $fh "$_\n" if($write);
	}
	close($ifh);
	close($fh);
	print $fhm "?>\n";
	close($fhm);
}

my $ls_cmd = "ls -1";
$ls_cmd .= " $ARGV[0]" unless $ARGV[0] eq "";
-e 'plainFilesMoved' || mkdir 'plainFilesMoved' ;
foreach my $file (`$ls_cmd`){
	chomp($file);
	print "- About to process: $file\n";
	processFile($file);
	system("mv $file plainFilesMoved/") && die "Unable to move $file to plainFilesMoved/.\n";
}