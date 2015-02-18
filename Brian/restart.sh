#!/bin/sh

EA_FOLDER=`pwd`

echo "Stopping Servers... "
	ps -efu `whoami` | grep -server_sql | grep $EA_FOLDER | grep -v grep | awk '{print $2}' | xargs --no-run-if-empty kill &> /dev/null
echo -n "Starting Servers... "
	screen -amdS login "$EA_FOLDER/login-server_sql"
	screen -amdS char "$EA_FOLDER/char-server_sql"
	screen -amdS map "$EA_FOLDER/map-server_sql"
echo "Servers Started!";
