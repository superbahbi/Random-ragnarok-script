<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Bandwidth</title>
<style type="text/css">
.style1 {
	color: #00CC00;
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>

<body>
<table style="width: 570px">
	<tr>
		<td>Bandwidth Utilization:</td>
		<td>Predicted Bandwidth Utilization:</td>
	</tr>
<?php
error_reporting(0); // Turn off all error reporting

$apiKey = "SET_API_KEY_HERE";

$apiData = "https://one.limestonenetworks.com/webservices/clientapi.php?key=".$apiKey."&mod=servers&action=list";
$serverList = simplexml_load_file($apiData);

foreach ($serverList->server as $serverItem) {
	// echo "<ul>\n";
	// echo "<li>Server ID = {$serverItem->attributes()->id}</li>\n";
	// echo "<li>Public IP = {$serverItem->publicip}</li>\n";
	// echo "</ul>\n";
echo "<tr>";
echo "<td class=\"style1\">{$serverItem->bandwidth->actual->friendly} / {$serverItem->bandwidth->actual->allocated} = {$serverItem->bandwidth->actual->percentage}%</td>\n";
echo "<td class=\"style1\">{$serverItem->bandwidth->predicted->friendly} / {$serverItem->bandwidth->actual->allocated} = {$serverItem->bandwidth->predicted->percentage}%</td>\n";
echo "</tr>";
}
?>
</table>
<br />
<b>Last Hour</b><br />
<img src="graph.php?range=hour" alt="Last Hour Bandwidth Graph" /><br />
<br />
<b>Last Day</b><br />
<img src="graph.php?range=day" alt="Last Day Bandwidth Graph" /><br />
<br />
<b>Last Week</b><br />
<img src="graph.php?range=week" alt="Last Week Bandwidth Graph" /><br />

</body>
</html>
