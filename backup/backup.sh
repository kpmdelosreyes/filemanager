##!bin/bash
date=`date +backup%Y%m%d%H%M%S`
tar -cvf /home/chabs/public_html/filemanager/backup/$date.tar /home/chabs/public_html/filemanager/external/*
gzip /home/chabs/public_html/filemanager/backup/$date.tar
ftp -n 192.168.0.98 <<END_SCRIPT
quote USER alex
quote PASS 1111
lcd /home/chabs/public_html/filemanager/backup/
cd /home/alex/public_html/files/chabs/
put $date.tar.gz
ls
quit
END_SCRIPT
rm -rf /home/chabs/public_html/filemanager/backup/*.tar.gz