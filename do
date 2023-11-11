#!/bin/bash

cd /root/.xmltv

date > log
tv_grab_zz_sdjson_sqlite --days 13 --output xmltv.xml >> log 2>> log

php upload.php >> log 2>> log

date >> log
