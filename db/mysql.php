<?
print "Start\n";

if($argc <= 2) {
  print "\nusage: mysql.php db_username db_password sql_filename\n";
  exit;
}

$username = $argv[1];
$password = $argv[2];
$filename = $argv[3];
$host     = 'localhost';

$noRunHash = array();
$noRunHash['mysql'] = 1;
$noRunHash['test'] = 1;

require_once('../code/lib/db/db_mysqli.class.php');

$dbh[$dsn] = new db_mysql(array('host' => $localhost, 'username' => $username, 'password' => $password));

$query_table  = "show databases";
$result_table = $dbh[$dsn]->db_query($query_table);

while ($database_obj = $dbh[$dsn]->db_fetch_object($result_table))
{
    #Importer innholdet i tabellene
    if(isset($noRunHash[$database_obj->Database]) and $noRunHash[$database_obj->Database] == 1)
    {
    }
    else
    {
        global $_SETUP;
        $command1 = "/usr/bin/mysql -u" . $username . " -p" . $password . " --force $database_obj->Database < $filename";
        print "$command1\n";
        system($command1);
    }
}
print "Stop\n";
?>
