<?php
error_reporting(0);
$cxn = new mysqli("host_OR_IP", "USER", "PASS", "DATABASE");

// check connection
if (mysqli_connect_errno()) {
	// printf("Connect failed: %s\n", mysqli_connect_error());
	die("Connect failed\n");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>More than 2 Chars-per-IP</title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>

<body>

<table style="width: 500px">
<tr><td>IP</td><td>Char Name</td><td>Last Map</td></tr>
<tr><td></td></tr>
<?php
// get list of IPs with more than 2 chars
if ($result = $cxn->query("SELECT last_ip FROM `char` LEFT JOIN login ON `char`.account_id=login.account_id WHERE `char`.online GROUP BY last_ip HAVING COUNT(char_id) > 2 ORDER BY COUNT(char_id) DESC, last_ip ASC")) {
	// for each IP
	while ($obj = $result->fetch_object()) {
		// get list of online chars from that IP
		if ($result2 = $cxn->query("SELECT `char`.`name`,`char`.last_map FROM `char` LEFT JOIN login ON `char`.account_id=login.account_id WHERE login.last_ip='".$obj->last_ip."' AND `char`.online ORDER BY last_map ASC")) {
			while ($obj2 = $result2->fetch_object()) {
				printf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>\n", $obj->last_ip, $obj2->name, $obj2->last_map);
			}
			$result2->close();
		}
		echo "<tr><td></td></tr>\n";
	}
	$result->close();
}
$cxn->close();
?>
</table>

</body>
</html>
