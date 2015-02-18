<?php

$dbhost = 'HOST_OR_IP';
$dbuser = 'MYSQL_USERNAME';
$dbpass = 'MYSQL_PASSWORD';
$dbname = 'DATABASE';
$savepath = "/path/to/backup/folder";

$send_email = "yes";
$to  = "root@localhost";
$from = "backup@localhost";
$senddate = date("j F Y");
$subject = "Full backup of $dbname completed - $senddate";
$message = "See attached zip file for mysqldump of $dbname";

$use_gzip = "yes";
$remove_sql_file = "yes";
$remove_gzip_file = "no";

ini_set("memory_limit","256M");
ini_set("max_execution_time","180");

// Do not Modify below this line! It will void your warranty!  Nah, go crazy go nuts

$month = date("Y-m");
$date = date("Y-m-d");
$filename = "$savepath/$month/".$dbname."_".$date.".sql";
if (!is_dir("$savepath/$month"))
	mkdir("$savepath/$month", 0700, true);
shell_exec("mysqldump --opt -h$dbhost -u$dbuser -p$dbpass $dbname > $filename");
	
if($use_gzip=="yes"){
	$zipline = "tar -czf $savepath/$month/".$dbname."_".$date."_sql.tar.gz -C $savepath/$month/ ".$dbname."_".$date.".sql";
	shell_exec($zipline);
}
if($remove_sql_file=="yes"){
	exec("rm -r -f $filename");
}

if($use_gzip=="yes"){
	$filename2 = "$savepath/$month/".$dbname."_".$date."_sql.tar.gz";
} else {
	$filename2 = $filename;
}


if($send_email == "yes" ){

	$fileatt_type = filetype($filename2);
	if($use_gzip=="yes"){
		$fileatt_name = "".$dbname."_".$date."_sql.tar.gz";
	} else {
		$fileatt_name = "".$dbname."_".$date.".sql";
	}

	$headers = "From: $from";

	// Read the file to be attached ('rb' = read binary)
	$file = fopen($filename2,'rb');
	fclose($file);

	// Generate a boundary string
	$semi_rand = md5(time());
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

	// Add the headers for a file attachment
	$headers .= "\nMIME-Version: 1.0\n" ."Content-Type: multipart/mixed;\n" ." boundary=\"{$mime_boundary}\"";

	// Add a multipart boundary above the plain message
	$message = "This is a multi-part message in MIME format.\n\n" ."--{$mime_boundary}\n" ."Content-Type: text/plain; charset=\"iso-8859-1\"\n" ."Content-Transfer-Encoding: 7bit\n\n" .
	$message . "\n\n";

	// Base64 encode the file data
	$data = chunk_split(base64_encode(file_get_contents($filename2)));

	// Add file attachment to the message
	$message .= "--{$mime_boundary}\n" ."Content-Type: {$fileatt_type};\n" ." name=\"{$fileatt_name}\"\n" ."Content-Disposition: attachment;\n" ." filename=\"{$fileatt_name}\"\n" ."Content-Transfer-Encoding: base64\n\n" .
		$data . "\n\n" ."--{$mime_boundary}--\n";

	// Send the message
	$ok = @mail($to, $subject, $message, $headers);
	if ($ok) {
		echo "Database backup created and sent! $date\n";
	} else {
		echo "Mail could not be sent. Sorry!\n";
	}
} else {
	echo "Database backup created! $date\n";
}


if($remove_gzip_file=="yes"){
	exec("rm -r -f $filename2");
}

?>
