<?php
// http://www.eathena.ws/board/index.php?showtopic=268702
$cxn = new mysqli("host_OR_IP", "USER", "PASS", "DATABASE");
$limit = 10; // max players to show
error_reporting(0);


if (mysqli_connect_errno()) {
	// printf("Connect failed: %s\n", mysqli_connect_error());
	die("Connection failed\n");
}

header("Content-Type: text/plain");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$item_id = intval($_GET['id']);
	
	// check if item is stackable
	if ($result = $cxn->query(sprintf("SELECT name_english,name_japanese,`type` FROM item_db WHERE id=%d LIMIT 1", $item_id))) {
		// TODO: also check item_db2
		($obj = $result->fetch_object())
			or die("Invalid item_id");
		$result->close();
	}
	switch ($obj->type) {
	case 4: // IT_WEAPON
	case 5: // IT_ARMOR
	case 7: // IT_PETEGG
	case 8: // IT_PETARMOR
		$itemdb_isstackable = false;
		break;
	default:
		$itemdb_isstackable = true;
		break;
	}
	
	// Inventory
	if ($itemdb_isstackable)
		$query = sprintf("SELECT `char`.`name`, inventory.amount FROM inventory " . 
		                 "LEFT JOIN `char` ON inventory.char_id=`char`.char_id " . 
		                 "WHERE inventory.nameid=%d ORDER BY amount DESC LIMIT %d", $item_id,$limit);
	else
		$query = sprintf("SELECT `char`.`name`,COUNT(inventory.id) AS 'amount' FROM `inventory` " . 
		                 "LEFT JOIN `char` ON inventory.char_id=`char`.char_id " . 
		                 "WHERE nameid=%d GROUP BY inventory.char_id " . 
		                 "ORDER BY COUNT(inventory.id) DESC LIMIT %d", $item_id,$limit);
	echo "item_id = $item_id = {$obj->name_japanese}\n" . 
	     "\n" . 
	     "Amount\tName (inventory)\n" . 
	     "--------------------------\n";
	if ($result = $cxn->query($query)) {
		while ($obj = $result->fetch_object())
			echo sprintf("%d\t%s\n", $obj->amount, $obj->name);
		$result->close();
	}
	
	// Storage
	if ($itemdb_isstackable)
		$query = sprintf("SELECT `char`.`name`, `storage`.amount FROM `storage` " . 
		                 "LEFT JOIN `char` ON `storage`.account_id=`char`.account_id " . 
		                 "WHERE `storage`.nameid=%d GROUP BY `storage`.account_id " . 
		                 "ORDER BY amount DESC LIMIT %d", $item_id,$limit);
	else
		$query = sprintf("SELECT `char`.`name`, COUNT(DISTINCT `storage`.id) AS 'amount' FROM `storage` " . 
		                 "LEFT JOIN `char` ON `storage`.account_id=`char`.account_id " . 
		                 "WHERE nameid=%d GROUP BY `storage`.account_id " . 
		                 "ORDER BY COUNT(DISTINCT `storage`.id) DESC LIMIT %d", $item_id,$limit);
	echo "\n" . 
	     "Amount\tName (storage)\n" . 
	     "--------------------------\n";
	if ($result = $cxn->query($query)) {
		while ($obj = $result->fetch_object())
			echo sprintf("%d\t%s\n", $obj->amount, $obj->name);
		$result->close();
	}
	
} else {
	echo "Usage:\n" . 
	     "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?id=<item_id>\n" . 
	     "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?id=607\n";
}

$cxn->close();
exit;
?>
