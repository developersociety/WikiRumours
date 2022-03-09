drop-db:
	mysqladmin -f -u root -p drop wikirumours 2>/dev/null || true

create-db:
	mysqladmin -u root -p create wikirumours

add-local-dev-user:
	mysql -u root wikirumours < db_setup/dev-setup.sql

reset: drop-db create-db # add-local-dev-user
	mysql -u root wikirumours < db_setup/wikirumours_msf.sql
	mysql -u root wikirumours < db_setup/add-dev-user.sql

blank-reset: drop-db create-db # add-local-dev-user
	mysql -u root wikirumours < db_setup/import_me.sql
	mysql -u root wikirumours < db_setup/add-dev-user.sql

# Doesn't work, as needs full .htaccess router thing...
# run:
# 	cd source/web_root && php -S localhost:8000 initialize.php
