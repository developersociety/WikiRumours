#!/bin/bash
apt-get update -y
apt-get install -y cron

echo "* * * * * /usr/local/bin/php /home/site/wwwroot/cron/cron.php" | crontab -

service cron start
