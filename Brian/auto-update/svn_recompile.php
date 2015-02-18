<?php
//===== rAthena Script =======================================
//= Automated SVN updates
//===== By: ==================================================
//= Brian
//===== Current Version: =====================================
//= 1.1
//===== Compatible With: =====================================
//= rAthena SVN (SQL only)
//===== Description: =========================================
//= Automatically 'svn update', recompile, and restart server.
//===== Additional Comments: =================================
//= 
//============================================================

$local_path = '/home/rathena/trunk/';           // your rAthena server directory (including trailing slash)
$output_dir = '/home/rathena/update_log/';      // where to save the .log files (including trailing slash)
$rathena_url = 'https://rathena.svn.sourceforge.net/svnroot/rathena/'; // URL for checking rAthena HEAD revision
$host = 'localhost';    // MySQL host
$user = 'ragnarok';     // MySQL user
$pass = '';             // MySQL password
$log_db = 'log';        // ragnarok log database

//============================================================

// get info from last entry in `svn_log` table
$cxn = mysqli_connect($host,$user,$pass,$log_db);
$result = mysqli_query($cxn, "SELECT `server_rev`,`rathena_rev`,`restart` FROM `svn_log` ORDER BY `id` DESC LIMIT 1");
$line = mysqli_fetch_row($result);

$server_rev = (int)$line[0];
$rathena_rev = (int)$line[1];
$restart = (int)$line[2];

// if no restart scheduled, nothing to do
if ($restart < 1) {
	exit(0);
}


// 4:42 - stop login server
$cmd='ps -fu `whoami` | grep login-server_sql | grep -v grep | awk \'{print $2}\' | xargs kill &> /dev/null';	`$cmd`;

// sleep for 3 mins (180 seconds)
sleep(180);
// 4:45 - @mapexit (run by NPC script)

// sleep for 15 seconds (give the map server time to stop)
sleep(15);
// stop all servers
$cmd='ps -fu `whoami` | grep -server_sql | grep -v grep | awk \'{print $2}\' | xargs kill &> /dev/null';	`$cmd`;

// remove the lines we added to MOTD
$cmd='echo "`sed \'$d\' '.$local_path.'conf/motd.txt | sed \'$d\'`" > '.$local_path.'conf/motd.txt';	`$cmd`;

// move current compiled servers to a sub-folder (for backup)
`mkdir $local_path$server_rev`;
`mv $local_path*-server_sql $local_path$server_rev &> /dev/null`;
$cmd='mv '.$local_path.'log/* '.$local_path.$server_rev.' &> /dev/null';	`$cmd`;

// compile servers
`cd $local_path; ./configure`;
`cd $local_path; make clean`;
`cd $local_path; make sql >> $output_dir$server_rev-$rathena_rev.log`;


// start servers
`cd $local_path; screen -amdS login ./login-server_sql`;
`cd $local_path; screen -amdS char ./char-server_sql`;
`cd $local_path; screen -amdS map ./map-server_sql`;

// sleep 10 seconds
sleep(10);
// check if servers are up
$status = 0;
$cmd='ps -fu `whoami` | grep login-server_sql | grep -cv grep';	$status += `$cmd` * 1;
$cmd='ps -fu `whoami` | grep char-server_sql | grep -cv grep';	$status += `$cmd` * 2;
$cmd='ps -fu `whoami` | grep map-server_sql | grep -cv grep';	$status += `$cmd` * 4;

// if they are not, copy the previous servers from the backup folder
if ($status < 7) {
	// stop servers
	$cmd='ps -fu `whoami` | grep -server_sql | grep -v grep | awk \'{print $2}\' | xargs kill &> /dev/null';	`$cmd`;
	
	// copy from backup
	$cmd='cp '.$local_path.$server_rev.'/* '.$local_path;	`$cmd`;
	
	// start servers
	`cd $local_path; ./login-server_sql &> /dev/null &`;
	`cd $local_path; ./char-server_sql &> /dev/null &`;
	`cd $local_path; screen -A -m -d -S map-server ./map-server_sql &`;
} else {
	// log successful update
	$result = mysqli_query($cxn, "UPDATE `svn_log` SET `restart`=`restart`+1 ORDER BY `id` DESC LIMIT 1");
}

?>
