#!/bin/bash
apt-get update -y
apt-get install -y cron

echo "* * * * * . /etc/profile ; /usr/local/bin/php /home/site/wwwroot/cron/cron.php" | crontab -

service cron start

# password file is required so create if does not exists to prevent errors
[ -f /home/site/wwwroot/.htpasswd ] || touch /home/site/wwwroot/.htpasswd