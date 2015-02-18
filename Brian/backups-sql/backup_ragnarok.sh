#!/bin/bash

dbhost='HOST_OR_IP'
dbuser='MYSQL_USERNAME'
dbpass='MYSQL_PASSWORD'
dbname='DATABASE'
savepath=$(dirname $0) # OR something like '/path/to/backup/folder'

date=`date +%Y-%m-%d_%H%M`
month=`date +%Y-%m`
filename="$savepath/$month/"$dbname"_"$date".sql"

if [[ ! -d "$savepath/$month" ]]; then
	mkdir -p "$savepath/$month"
	chmod 700 "$savepath/$month"
fi

mysqldump --opt --host=$dbhost --user=$dbuser --password=$dbpass $dbname > $filename
chmod 400 $filename
