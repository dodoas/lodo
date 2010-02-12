database=empatix
echo "Db password"
stty -echo
read password
stty echo

/usr/bin/mysqldump -uroot -p$password $database -d --skip-add-drop-table > sql/intern_datastructure.sql

for table in role roleperson roletemplate roletemplateaccess setup menu language \
confdbfields confmenues confsql conftemplates templaterestrictions accountplan vat
do
  mysqldump -uroot -p$password $database --no-create-info $table | tee sql/$table.sql
done > sql/content.sql
