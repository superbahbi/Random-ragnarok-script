<?php
error_reporting(0);
date_default_timezone_set('America/Los_Angeles');
$cxn = new mysqli('HOST_OR_IP', 'MYSQL_USERNAME', 'MYSQL_PASSWORD', 'DATABASE');
$boss_spawn_delay = 100; // ../conf/battle/monster.conf
// check connection
if (mysqli_connect_errno()) {
	echo "Connection failed.\n";
	// printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$mvp = array(
	//     map        mob_id, delay, variance,  name
	array("moc_pryd06",	1511, 3600000,600000,	"AmonRa"),
	array("ra_fild02",	1785,14400000,600000,	"Atroce"),
	array("ra_fild03",	1785,10800000,600000,	"Atroce"),
	array("ra_fild04",	1785,18000000,600000,	"Atroce"),
	array("ve_fild01",	1785,10800000,0,		"Atroce"),
	array("ve_fild02",	1785,21600000,0,		"Atroce"),
	array("lou_dun03",	1630, 7000000,600000,	"Bacsojin"),
	array("prt_maze03",	1039, 7200000,600000,	"Baphomet"),
	array("abbey03",	1874,43200000,600000,	"Beelzebub"),
	array("lhz_dun03",	   0, 6000000,1800000,	"Bio3"),
	array("gl_chyard",	1272, 3600000,600000,	"DarkLord"),
	array("abyss_03",	1719,10800000,600000,	"Detale"),
	array("gef_dun02",	1046, 7200000,600000,	"Doppelganger"),
	array("gef_dun01",	1389, 3600000,600000,	"Dracula"),
	array("treasure02",	1112, 7200000,600000,	"Drake"),
	array("pay_fild11",	1115, 7200000,600000,	"Eddga"),
	array("gon_dun03",	1418, 5650000,600000,	"EvilSnakeLord"),
	array("abbey02",	1871, 7200000,600000,	"FallenBishop"),
	array("xmas_fild01",1252, 7200000,600000,	"Garm"),
	array("ra_san05",	1768,18000000,600000,	"GloomUnderNight"),
	array("prt_sewb4",	1086, 3600000,600000,	"GoldenThiefBug"),
	array("mosk_dun03",	1885, 7200000,600000,	"Gopinich"),
	// array("man_fild03",	1990,14400000,0,		"HardrockMammoth"),
	array("thor_v03",	1832,39600000,600000,	"Ifrit"),
	array("ama_dun03",	1492, 5460000,600000,	"IncantationSamurai"),
	array("kh_dun02",	1734, 7200000,3600000,	"KielD01"),
	array("ice_dun03",	1779, 7200000,0,		"Ktullanux"),
	array("ayo_dun02",	1688,25200000,600000,	"LadyTanee"),
	array("niflheim",	1373, 7980000,0,		"LordofDeath"),
	array("anthell02",	1147, 7200000,600000,	"Maya"),
	array("mjolnir_04",	1059, 7200000,600000,	"Mistress"),
	array("pay_dun04",	1150, 3600000,600000,	"MoonlightFlower"),
	array("gef_fild02",	1087,86400000,600000,	"OrcHero"),
	array("gef_fild14",	1087, 3600000,600000,	"OrcHero"),
	array("gef_fild10",	1190, 7200000,600000,	"OrcLord"),
	array("moc_pryd04",	1038, 3600000,600000,	"Osiris"),
	array("in_sphinx5",	1157, 3600000,600000,	"Pharaoh"),
	array("moc_fild17",	1159, 7200000,600000,	"Phreeoni"),
	array("ein_dun02",	1623, 7500000,600000,	"RSX0806"),
	array("moc_fild22",	1917,43200000,3600000,	"SatanMorroc"),
	array("xmas_dun02",	1251, 3600000,600000,	"StormyKnight"),
	array("beach_dun",	1583,18000000,600000,	"TaoGunka"),
	// array("spl_fild03",	1991, 3600000,0,		"TendrilLion"),
	array("thana_boss",	1708, 7200000,0,		"Thanatos"),
	array("tur_dun04",	1312, 3600000,600000,	"TurtleGeneral"),
	array("odin_tem03",	1751,28800000,600000,	"ValkyrieRandgris"),
	array("jupe_core",	1685, 7200000,600000,	"Vesper"),
	array("lhz_dun02",	1658, 7200000,600000,	"Ygnizem")
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<?php // automatic page refreshing
if (isset($_GET['refresh']) && is_numeric($_GET['refresh']))
	echo sprintf("<meta http-equiv=\"refresh\" content=\"%d;url=http://%s%s\" />", min(60,max(intval($_GET['refresh']),600)),$_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI']);
echo "\n";
?>
<title>MVP times</title>
<style type="text/css">body {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>

<body>

<table style="width: 500px">
<?php
if ($result = $cxn->query("SELECT UNIX_TIMESTAMP(`time`) FROM loginlog WHERE `log`='login server started' ORDER BY `time` DESC LIMIT 1")) {
	$row = $result->fetch_row();
	$last_restart = $row[0];
	$result->close();
}

for ($i = 0; $i < count($mvp); $i++) {
	if ($result = $cxn->query("SELECT UNIX_TIMESTAMP(mvp_date) FROM mvplog WHERE map='".$mvp[$i][0]."' ORDER BY mvp_id DESC LIMIT 1")) {
		// fetch object array
		$row = $result->fetch_row();
		$delay = in_array($mvp[$i][0], array("lhz_dun03","ice_dun03","niflheim","thana_boss")) ? ($mvp[$i][2]/1000) : ($mvp[$i][2]/1000*$boss_spawn_delay/100);
		
		// date("Y-m-d H:i:s", $row[0])		2009-11-26 08:41:24
		// date("m/d/Y g:i a", $row[0])		11/26/2009 08:41 am
		echo "<tr><td><a href=\"http://ratemyserver.net/index.php?page=mob_db&amp;mob_id=".$mvp[$i][1]."\" target=\"_blank\" title=\"".$mvp[$i][0]."\">".$mvp[$i][4]."</a></td> <td><font color=\"";
		if (time() > ($row[0] + $delay + $mvp[$i][3]/1000)) {
			// green
			echo "#00BB00\"><b>".( $row[0] ? date("Y-m-d H:i:s", $row[0]) : "never" )."</b></font></td> <td></td></tr>\n";
		} else if (time() > ($row[0] + $delay) && $row[0] > $last_restart) {
			// yellow
			echo "#FF9900\"><b>".date("Y-m-d H:i:s", $row[0])."</b></font></td> <td>".date("g:i a", ($row[0] + $delay))." <font color=\"#777777\">+".($mvp[$i][3]/1000/60)." min</font>"."</td></tr>\n";
		} else if (time() > $row[0] && $row[0] > $last_restart) {
			// red
			echo "#FF0000\">".date("Y-m-d H:i:s", $row[0])."</font></td> <td>".date("g:i a", ($row[0] + $delay)).($mvp[$i][3] ? (" <font color=\"#777777\">+".($mvp[$i][3]/1000/60)." min</font>") : "")."</td></tr>\n";
		} else {
			// green
			echo "#00BB00\"><b>".( $row[0] ? date("Y-m-d H:i:s", $row[0]) : "never" )."</b></font></td> <td></td></tr>\n";
		}
		$result->close(); // free result set
	}
}
$cxn->close();
?>
</table>

</body>
</html>
