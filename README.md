```
### MariaDB
sudo apt-get install mariadb-server mariadb-client
sudo apt-get install libdbd-mariadb-perl libjson-perl libfile-slurp-perl

sudo mysql_secure_installation

mysql_tzinfo_to_sql /usr/share/zoneinfo | sudo mysql -u root mysql
sudo systemctl restart mysqld

sudo mysql
CREATE DATABASE `swaps`;

CREATE USER 'swaps' IDENTIFIED BY 'xxxx';
GRANT USAGE ON *.* TO 'swaps'@'%' IDENTIFIED BY 'xxxx';
GRANT ALL privileges ON `swaps`.* TO 'swaps'@'%';
FLUSH PRIVILEGES;

CREATE USER 'swaps_ro' IDENTIFIED BY 'xxxx';
GRANT USAGE ON swaps.* TO 'swaps_ro'@'%' IDENTIFIED BY 'xxxx';
GRANT SELECT ON swaps.* TO 'swaps_ro'@'%' IDENTIFIED BY 'xxxx';
FLUSH PRIVILEGES;

CREATE TABLE swaps (
uuid CHAR(36) PRIMARY KEY,
started_at DATETIME NOT NULL,
taker_coin VARCHAR(8) NOT NULL,
taker_amount DOUBLE(20,8) UNSIGNED NOT NULL,
taker_gui VARCHAR(64),
taker_version VARCHAR(64),
taker_pubkey VARCHAR(64),
maker_coin VARCHAR(8) NOT NULL,
maker_amount DOUBLE(20,8) UNSIGNED NOT NULL,
maker_gui VARCHAR(64),
maker_version VARCHAR(64),
maker_pubkey VARCHAR(64)
);

CREATE TABLE swaps_failed (
uuid CHAR(36) PRIMARY KEY,
started_at DATETIME NOT NULL,
taker_coin VARCHAR(8) NOT NULL,
taker_amount DOUBLE(20,8) UNSIGNED NOT NULL,
taker_error_type VARCHAR(32),
taker_error_msg TEXT,
taker_gui VARCHAR(64),
taker_version VARCHAR(64),
taker_pubkey VARCHAR(64),
maker_coin VARCHAR(8) NOT NULL,
maker_amount DOUBLE(20,8) UNSIGNED NOT NULL,
maker_error_type VARCHAR(32),
maker_error_msg TEXT,
maker_gui VARCHAR(64),
maker_version VARCHAR(64),
maker_pubkey VARCHAR(64)
);

CREATE INDEX started_at ON swaps (started_at);
CREATE INDEX maker_coin ON swaps (maker_coin);
CREATE INDEX taker_coin ON swaps (taker_coin);
CREATE INDEX started_at ON swaps_failed (started_at);
CREATE INDEX maker_coin ON swaps_failed (maker_coin);
CREATE INDEX taker_coin ON swaps_failed (taker_coin);


### cron
*/4 * * * * find /home/username/atomicDEX-API/etomic_build/seed/DB/74205554f954fbf57d2a516a28531489928d7d80/SWAPS/STATS/MAKER -name "*.json" -mmin -5 -exec /home/username/parser {} \;
*/5 * * * * find /home/username/atomicDEX-API/etomic_build/seed/DB/74205554f954fbf57d2a516a28531489928d7d80/SWAPS/STATS/TAKER -name "*.json" -mmin -6 -exec /home/username/parser {} \;
```
