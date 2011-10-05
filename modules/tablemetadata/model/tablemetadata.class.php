<?
# $Id: record.inc,v 1.55 2005/10/14 13:15:43 thomasek Exp $ ConfDBFields_record.php,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no/

class model_tablemetadata_tablemetadata {

    function __construct() {
    }

    function updateselected($args) {

        $totalcount = $args['TotalCount'];
        for($i = 0; $i < $totalcount; $i++)
        {
            if($args['check_'.$i] == 1)
            {
                $params = array();
                $params['db_name'] = $args[$i];
                $this->update_db($params);
            }
        }
    }

    function updateall() {
        global $_lib;

        $query_show = "show databases";
        $result     = $_lib['db']->db_query($query_show);
        $i = 0;
        while ($row = $_lib['db']->db_fetch_object($result)) {
            print "Oppdaterer: $row->Database\n";
            $params['db_name'] = $row->Database;
            $this->update_db($params);
        }
    }

    function updateallskipsystemdbs($args) {
        global $_lib;

        if (empty($args['tablefilter'])) {
            $tablefilter = null;
        } else {
            $tablefilter = $args['tablefilter'];
        }

        $system_dbs = array('lodo', 'mysql', 'test', 'information_schema');

        $query_show = "show databases";
        $result     = $_lib['db']->db_query($query_show);
        $i = 0;
        while ($row = $_lib['db']->db_fetch_object($result)) {
            if (in_array($row->Database, $system_dbs)) {
                continue;
            }

            print "Oppdaterer: $row->Database\n";
            $params['db_name'] = $row->Database;
            $params['tablefilter'] = $tablefilter;
            $this->update_db($params);
        }
   }

    function runscriptall($args) {
        if (empty($args['scriptpath'])) {
            print "Missing scriptpath argument.\n";
            return false;
        }

        $scriptpath = $args['scriptpath'];

        global $_lib;

        $system_dbs = array('lodo', 'mysql', 'test', 'information_schema');

        if (!is_file($scriptpath)) {
            print "File $scriptpath not found.\n";
            return false;
        }

        global $_SETUP;

        $script = file_get_contents($scriptpath);

        
        $params['commands'] = explode(';', $script);
        # use default login values, assuming all dbs have same login values
        # in the future we might load setup files instead
        $params['db_server'] = $_SETUP['DB_SERVER_DEFAULT'];
        $params['db_user'] = $_SETUP['DB_USER_DEFAULT']; 
        $params['db_password'] = $_SETUP['DB_PASSWORD_DEFAULT'];

        $query_show = "show databases";
        $result     = $_lib['db']->db_query($query_show);
        $i = 0;
        while ($row = $_lib['db']->db_fetch_object($result)) {
            if (in_array($row->Database, $system_dbs)) {
                continue;
            }

            print "Kjører script på: $row->Database\n";
            $params['db_name'] = $row->Database;
            $this->runscriptondb($params);
        }
    }

    function verifylododb($db_link) {
        # verify that db has a table called mvaavstemming, if not assume not lodo db
        $query = "SHOW TABLES LIKE 'mvaavstemming';";
        $result = mysqli_query($db_link, $query);
        if (!$result) {
            return false;
        }

        return (mysqli_num_rows($result) == 1);
    }

    function runscriptondb($params) {

        if (empty($params['db_name'])) {
            print "DB name missing.\n";
            return false;
        }

        $db_name = $params['db_name'];

        try {
            $db_link = @mysqli_connect($params['db_server'], $params['db_user'], $params['db_password'], $db_name);
        } catch (Exception $e) {
            echo 'Caught exception when trying to login to $db_name: ',  $e->getMessage(), "\n";
            return false;
        }

        if (!$db_link) {
            print "You are not authorized to login to this database: $db_name.\n";
            return false;
        }

        if (!$this->verifylododb($db_link)) {
            print "Not a lodo database: $db_name.\n";
            return false;
        }

        foreach ($params['commands'] as $command) {
            if ($command == "" || trim($command) == "") {
                continue;
            }
            print "Kjører kommando: " . substr($command, 0, 40) . "...\n";

                $query = $command;
    
                $result = mysqli_query($db_link, $query);
                if (!$result) {
                    print "Dbname: $db_name, db_query: $query. <br>\nBad query: " . mysqli_error($db_link) . "<br />\n.\n";
                    return false;
                } else {
                    print "Query successful.\n";
                }
        }

        print "Dbname: $db_name, db script successfully executed.\n";

        return true;
    }

    function newlang($args) {
        global $_lib;

        $query = "select * from confdbfields where TableName='" . $args['TableName'] . "' order by TableField";
        $result = $_lib['db']->db_query($query);
        while($row = $_lib['db']->db_fetch_object($result))
        {
            $query = "INSERT INTO confdbfieldlanguage SET ConfDBFieldID='$row->ConfDBFieldID', TableName='" . $args['TableName'] . "', TableField='$row->TableField', LanguageID='" . $args['lang_name'] . "'";
            $query_result = $_lib['db']->db_query3(array('query'=>$query, 'do_not_die'=>'1'));
            //print $query;
        }
    }

    function delete($args) {
        global $_lib;
    
        $query = "delete from confdbfieldlanguage where ConfDbFieldLanguageID=" . $args['ConfDbFieldLanguageID'];
        $query_result = $_lib['db']->db_query3(array('query'=>$query, 'do_not_die'=>'1'));
        //print $query;
    }

    function dbupdate($args) {
        global $_SETUP;        
        $this->update_db($args);
    }

    function recorddelete($args) {
        global $_lib;
        
        $query = "update $db_table set Active='0' WHERE ConfDBFieldID='" . $args['ConfDBFIeldID'] . "'";
        $result = $_lib['db']->db_query($query);
    }
    
    function fieldlanguageupdate($args) {
        global $_lib;
        
        $tables['confdbfields']           = 'ConfDBFieldID';
        $tables['confdbfieldlanguage']    = 'ConfDbFieldLanguageID';
        $_lib['storage']->db_update_multi_table($args, $tables);
    }
    
    function fieldlanguagenew($args) {
        global $_lib;
        
        $query = "INSERT INTO confdbfieldlanguage SET ConfDBFieldID='".$args['ConfDBFieldID']."', TableName='".$args['TableName']."', TableField='".$args['TableField']."'";
        print $query;
        $_lib['db']->db_insert($query);
    }

    function confdbfield_delete($args) {
        global $_lib;
        
        $query = "update $db_table set Active='0' WHERE ConfDBFieldID='" . $args['ConfDBFIeldID'] . "'";
        $result = $_lib['db']->db_query($query);
    }


    private function update_db($args) {
        global $_SETUP, $_lib;
        
        #print_r($args);        

        $databaseName = $args['db_name'];
        $tableFilter = $args['tablefilter'];
        $dsn = $_SETUP['DB_SERVER_DEFAULT'] . $databaseName . $_SETUP['DB_TYPE_DEFAULT'];
        $dbh[$dsn] = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'], 'database' => $databaseName, 'username' => $_SETUP['DB_USER_DEFAULT'], 'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

        $query_is_lodo = "SHOW TABLES LIKE 'confdbfields'";
        $result_is_lodo = $dbh[$dsn]->db_query($query_is_lodo);
        if (!$result_is_lodo ||  mysqli_num_rows($result_is_lodo) != 1) {
            $_lib['message']->add("<b>Database: $databaseName er ikke en Lodo database\n");
            return false;
        }
    
        if (!empty($tableFilter)) {
            $query_update = "update confdbfields SET Active=0 where TableName='$tableFilter'";
        } else {
            $query_update = "update confdbfields SET Active=0";
        }
        $dbh[$dsn]->db_update($query_update);

        if (!empty($tableFilter)) {
            $query_table  = "show tables LIKE '$tableFilter'";
        } else {
            $query_table  = "show tables";
        }
        $result_table = $dbh[$dsn]->db_query($query_table);
    
        $typemap = array(
          10 => 'date',
          12 => 'datetime',
          #252 => 'string',
          #253 => 'string',
          #254 => 'string',
          4 => 'decimal',
          5 => 'decimal',
          6 => 'decimal',
          0 => 'decimal',
          1 => 'int',
          3 => 'int',
          2 => 'int',
          7 => 'datetime',
        );
    

        $total = 0;
        $updated = 0;
        $new = 0;
        $new_fields = 0;

        while ($table_obj = $dbh[$dsn]->db_fetch_object($result_table))
        {
            if (!empty($tableFilter)) {
                $table_choice = "Tables_in_$databaseName ($tableFilter)";
            } else {
                $table_choice = "Tables_in_$databaseName";

            }
            $table = $table_obj->{$table_choice};
            #print_r($table);
    
            #print "tabell: $table<br>";
            if(!$table) { continue; }
            $query_tableinfo = "SHOW COLUMNS FROM $table"; #Obs
            #print  "$query_tableinfo<br>\n";
            $result_tableinfo = $dbh[$dsn]->db_query($query_tableinfo);
            #print_r($row);
            #$tablefields = mysql_list_fields($databaseName, $table);
            #$tablefields = mysqli_fetch_fields($row);
            $maxwidth = 80; #Not used yet
    
            if(preg_match("/_/", $table))
            {
                #$_lib['message']->add("Tables can not contain underscore (reserved by framework_lib_inline): $table");
            }
            #print_r($tablefields);
            while($row = $dbh[$dsn]->db_fetch_object($result_tableinfo))
            {
                #print_r($row);
    
                    $FieldExtra = "";
                    $FieldExtraEdit = "";
                    $inputType  = "";
                    $outputType = "";
                    $FormType   = "";
                    $Required   = "";
                    $FormHeight     = 0;
                    $FormWidth      = 0;
                    $FormTypeEdit = "";
                    $DefaultValue = "";
                    $type_num = "";

                    #print_r($tablefields[$i]);
                    #exit;
                    $type       = $row->Type;
                    $field      = $row->Field;
                    #$len       = $tablefields[$i]->max_length;
                    $flags      = $row->Extra;
                    $def        = $row->Default; #Default value
                    $key        = $row->Key;
                    $null       = $row->Null;
    
                    $fieldType = $type;

                    $len = null;
    
                    if(preg_match('{(.*)\((.*)\)}', $type, $m)) {
                        if(is_int($m['2'])) {
                          $len          = $m['2'];
                          $FormWidth    = $m['2'];
                        }
                        $type = $m['1'];
                    } else {
                          $len = 255;
                    }
                    #print "<br>\ntype: $type, $len<br>\n";
    
                    if(preg_match("/_/", $field))
                    {
                        #$_lib['message']->add("Fields can not contain underscore (reserved by framework_lib_inline): $table.$field");
                    }
    
                    #echo $type . " " . $field . " " . $len . " " . $flags . "<br>\n";
    
                    #These if tests should be replaced by a hash.
    
                    if($type == 'date')
                    {
                        $inputType = 'Date';
                        $FormType  = 'text';
                        $FormWidth     = 25;
                    }
                    elseif($type == 'datetime')
                    {
                        $inputType = 'Datetime';
                        $FormType  = 'text';
                        $FormHeight    = 1;
                        $FormWidth     = 25;
                    }
                    /* // Not tested yet
                    elseif($type == 'time')
                    {
                        $inputType = 'Time';
                        $FormType  = 'text';
                        $FormHeight    = 1;
                        $FormWidth     = 10;
                        } */
                    elseif($type == 'timestamp')
                    {
                        $inputType = 'Datetime';
                        $FormType  = 'show';
                        $FormHeight    = 1;
                        $FormWidth     = 25;
                    }
                    elseif($type == 'string')
                    {
                        $inputType = 'String';
                        $FormType  = 'text';
                        $FormHeight    = 1;
                        $FormWidth     = $len;
                    }
                    elseif($type == 'varchar')
                    {
                        $inputType = 'String';
                        $FormType  = 'text';
                        $FormHeight    = 1;
                        $FormWidth     = $len;
                    }
                    elseif($type == 'char')
                    {
                        $inputType = 'String';
                        $FormType  = 'text';
                        $FormHeight    = 1;
                        $FormWidth     = $len;
                    }
                    elseif($type == 'real')
                    {
                        $inputType = 'Amount';
                        $FormType = 'text';
                        $FormHeight = 1;
                        $FormWidth = 16;
                    }
                    elseif($type == 'decimal')
                    {
                        $inputType = 'Amount';
                        $FormType = 'text';
                        $FormHeight = 1;
                        $FormWidth = 16;
                    }
                    elseif($type == 'float')
                    {
                        $inputType = 'Amount';
                        $FormType = 'text';
                        $FormHeight = 1;
                        $FormWidth = 16;
                    }
                    elseif($type == 'double')
                    {
                        $inputType = 'Amount';
                        $FormType = 'text';
                        $FormHeight = 1;
                        $FormWidth = 16;
                    }
                    elseif($type == 'int')
                    {
                        $inputType = 'Int';
                        $FormType = 'text';
                        $FormHeight = 1;
                        $FormWidth = 12;
                    }
                    elseif($type == 'bigint')
                    {
                        $inputType = 'Int';
                        $FormType = 'text';
                        $FormHeight = 1;
                        $FormWidth = 20;
                    }
                    elseif($type == 'set')
                    {
                        $inputType = 'String';
                        $FormType = 'text';
                        $FormHeight = 1;
                        $FormWidth = 12;
                    }
                    elseif($type == 'tinyint')
                    {
                        $inputType = 'Int';
                        $FormType = "checkbox";
                        $FormWidth = 10;
                        $FormHeight = 1;
                    }
                    elseif($type == 'smallint')
                    {
                        $inputType = 'Int';
                        $FormType = "checkbox";
                        $FormWidth = 10;
                        $FormHeight = 1;
                    }
                    elseif($type == 'blob')
                    {
                        $inputType = 'String';
                        $FormType = 'wysiwyg';
                        #$FormType = 'textarea'; #For tollpost
                        $FormHeight = 0; #Does not work well for override. We let height be dynamic by setting 0
                        $FormWidth  = 80;
                    }
                    elseif($type == 'mediumtext')
                    {
                        $inputType = 'String';
                        $FormType = 'wysiwyg';
                        #$FormType = 'textarea'; #For tollpost
                        $FormHeight = 0; #Does not work well for override. We let height be dynamic by setting 0
                        $FormWidth  = 80;
                    }
                    elseif($type == 'text')
                    {
                        $inputType = 'String';
                        $FormType = 'wysiwyg';
                        #$FormType = 'textarea'; #For tollpost
                        $FormHeight = 0; #Does not work well for override. We let height be dynamic by setting 0
                        $FormWidth  = 80;
                    }
                    else
                    {
                        $_lib['message']->add("Finnes ikke type: $type");
                        $_lib['message']->add("table:" .  $table ." type:".$type." type_num:".$type_num." field:".$field." length: ".$len." flags:".$flags);
                    }
    
                    if($key == 'PRI')
                    {
                        $pk = 1;
                        #print "$field, $flags<br>";
                    }
                    else
                    {
                        $pk = 0;
                    }
    
                    #More advanced funstionality added later:
                    #All PersonID references - generate person menu automatically
                    #All timestamps: type show (not edit)
                    #All companyID - company meny automatically
                    #All createdby and changed by: sho (not editable)
                    #All smallint - checkbox
    
                    if(preg_match("/PersonID/", $field))
                    {
                        $FormType = "select";
                        $FieldExtra = "person.menu";
                    }
                    if(preg_match("/ProductID/", $field))
                    {
                        $FormType = "select";
                        $FieldExtra = "form.productmenu";
                    }
                    if(preg_match("/IAddressID/", $field))
                    {
                        $FormType = "select";
                        $FieldExtra = "iaddress.menu";
                    }
                    if(preg_match("/DAddressID/", $field))
                    {
                        $FormType = "select";
                        $FieldExtra = "daddress.menu";
                    }
                    if(preg_match("/VAddressID/", $field))
                    {
                        $FormType = "select";
                        $FieldExtra = "vaddress.menu";
                    }
                    if(preg_match("/CompanyID/", $field))
                    {
                        $FormType = "select";
                        $FieldExtra = "company.menu";
                    }
                    if(preg_match("/Changed/", $field))
                    {
                        $FormType = "show";
                    }
                    if(preg_match("/Created/", $field))
                    {
                        $FormType = "show";
                    }
                    if(preg_match("/Updated/", $field))
                    {
                        $FormType = "show";
                    }
                    if(preg_match("/Email/", $field)){
                        $inputType = 'Email';
                    }
                    if(preg_match("/WWW/", $field))
                    {
                        $inputType = 'URL';
                    }
                    if(preg_match("/URL/", $field))
                    {
                        $inputType = 'URL';
                    }
                    if(preg_match("/ZipCode/", $field))
                    {
                        $inputType = 'ZipCode';
                    }
                    if(preg_match("/PostalCode/", $field))
                    {
                        $inputType = 'ZipCode';
                    }
                    if(preg_match("/Phone/", $field))
                    {
                        $inputType = 'Phone';
                    }
                    if(preg_match("/Mob/", $field))
                    {
                        $inputType = 'Phone';
                    }
                    if(preg_match("/Country/", $field))
                    {
                        $inputType = 'Country';
                    }
                    if(preg_match("/CountryCode/", $field))
                    {
                        $inputType = 'CountryCode';
                    }
                    if(preg_match("/Active/", $field))
                    {
                        $FormType = "checkbox";
                    }
                    if(preg_match("/Password/", $field))
                    {
                        $FormType = "password";
                    }
                    if(preg_match("/HoursPrDay/", $field))
                    {
                        $FormType  = "text";
                        $inputType = 'Amount';
                    }
                    if(preg_match("/InvoicePrDay/", $field))
                    {
                        $FormType  = "text";
                        $inputType = 'Amount';
                    }
                    if(preg_match("/CompanyDepartmentID/", $field))
                    {
                        $FieldExtra = 'form.avdmenu';
                        $FormType   = "select";
                    }
                    if(preg_match("/VatID/", $field))
                    {
                        $FieldExtra = 'form.vat';
                        $FormType   = "select";
                    }
                    if(preg_match("/InvoiceStatus/", $field))
                    {
                        $FieldExtra = 'form.invoicestatus';
                        $FormType   = "select";
                    }
    
                    if(preg_match("/ReportsToID/", $field))
                    {
                        $FieldExtra = 'form.companycontact';
                        $FormType   = "select";
                    }
                    if(preg_match("/ClassificationID/", $field))
                    {
                        $FieldExtra = 'form.typeclassificationmenu';
                        $FormType   = "select";
                    }
                    if(preg_match("/PriorityID/", $field))
                    {
                        $FieldExtra = 'form.typeprioritymenu';
                        $FormType   = "select";
                    }
                    if(preg_match("/AccessLevel/", $field))
                    {
                        $FieldExtra = 'form.typeaccesslevelmenu';
                        $FormType   = "select";
                    }
    
                    ################################ new fields
                    if(preg_match("/FileID/", $field))
                    {
                        $FormType  = "file2";
                        $inputType = "file2";
                    }
                    if(preg_match("/MediaID/", $field))
                    {
                        $FormType  = "media2";
                        $inputType = "media2";
                    }
                    if(preg_match("/ImageID/", $field))
                    {
                        $FormType  = "image2";
                        $inputType = "image2";
                        $FieldExtra = "100*100*png";
                        $FieldExtraEdit = '100*100*png';
                    }
                    if(preg_match("/AddressType/", $field))
                    {
                            $FormType  = "select";
                            $inputType = 'Int';
                            $DefaultValue = 0;
                            $FieldExtra = "form.typeaddresstypemenu";
                    }
                    if(preg_match("/ContentType/", $field))
                    {
                            $FormType  = "select";
                            $inputType = 'Int';
                            $DefaultValue = '';
                            $FieldExtra = "form.typecontenttype";
                    }
                    if(preg_match("/LanguageID/", $field))
                    {
                            $FormType  = "select";
                            $inputType = 'Int';
                            $DefaultValue = '';
                            $FieldExtra = "language.all";
                    }
                    if(preg_match("/Template/", $field))
                    {
                            $FormType  = "select";
                            $inputType = 'Int';
                            $DefaultValue = '';
                            $FieldExtra = "publish.template";
                    }
    
                    ################################
                    if($table == 'media')
                    {
                        if(preg_match("/MediaID/", $field))
                        {
                            $FormType  = "image2";
                            $inputType = 'media2';
                            $outputType = 'media2';
                            $FieldExtra = '100*100*png';
                            $FieldExtraEdit = '100*100*png';
                        }
                    }
                    if($table == 'mediastorage')
                    {
                        if(preg_match("/FileName/", $field))
                        {
                            $FormType  = "media3";
                            $inputType = 'media3';
                        }
                        if(preg_match("/Align/", $field))
                        {
                            $FormType  = "select";
                            $inputType = 'string';
                            $FieldExtra = 'form.ImageAlignMenu';
                        }
                        if(preg_match("/URL/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                        }
                        if(preg_match("/Heading/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                        }
                    }
                    if($table == 'mediacategory')
                    {
                        if(preg_match("/MediaCategoryID/", $field))
                        {
                            $FormType = "select";
                            $FieldExtra = "mediacategory.menu";
                        }
                    }
                    if($table == 'sla')
                    {
                        if(preg_match("/CompanyID/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                            $Required  = 1;
                        }
                        if(preg_match("/Frequency/", $field))
                        {
                            $FormType  = "select";
                            $inputType = 'int';
                            $FieldExtra = 'form.typeslafrequencymenu';
                            $Required  = 1;
                        }
    
                        if(preg_match("/Region/", $field))
                        {
                            $FormType  = "select";
                            $inputType = 'string';
                            $FieldExtra = 'form.typeregionmenu';
                            $Required  = 1;
                        }
    
                        if(preg_match("/SalesPersonID/", $field))
                        {
                            $FormType  = "select";
                            $inputType = 'string';
                            $FieldExtra = 'form.typesalespersonmenu';
                            $Required  = 1;
                        }
    
                        if(preg_match("/SignedDate/", $field))
                        {
                            $Required  = 1;
                        }
                        if(preg_match("/StartDate/", $field))
                        {
                            $Required  = 1;
                        }
                        if(preg_match("/StopDate/", $field))
                        {
                            $Required  = 1;
                        }
                    }
                    if($table == 'slagoal')
                    {
                        if(preg_match("/Goal/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'Amount';
                            $FormWidth     = 6;
                        }
                        if(preg_match("/GoalAchievement/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'Amount';
                            $FormWidth     = 6;
                        }
                    }
                    if($table == 'slaimprovement')
                    {
                        if(preg_match("/Sort/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'Int';
                            $FormWidth     = 2;
                        }
                        if(preg_match("/Goal/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'Amount';
                            $FormWidth     = 6;
                        }
                        if(preg_match("/GoalAchievement/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'Amount';
                            $FormWidth     = 6;
                        }
                        if(preg_match("/Code/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                            $FormWidth     = 4;
                            $Required  = 1;
                        }
                        if(preg_match("/Service/", $field))
                        {
                            $FormType  = "textarea";
                            $inputType = 'Int';
                            $FormWidth     = 60;
                            $FormHeight    = 4;
                            $Required  = 1;
                        }
                        if(preg_match("/WhereIs/", $field))
                        {
                            $FormType = "select";
                            $FieldExtra = "slaimprovement.avdmenu";
                        }
                        if(preg_match("/Who/", $field))
                        {
                            $FormType = "select";
                            $FieldExtra = "form.typesalespersonmenu";
                        }
                    }
                    if($table == 'slaeffort')
                    {
                        if(preg_match("/Priority/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'Int';
                            $DefaultValue = 0;
                            $FormWidth     = 2;
                        }
                        if(preg_match("/Status/", $field))
                        {
                            $FormType  = "select";
                            $inputType = 'Int';
                            $DefaultValue = 0;
                            $FieldExtra = "form.typeslastatusmenu";
                        }
                        if(preg_match("/Code/", $field))
                        {
                            $FormType     = "text";
                            $inputType    = 'Int';
                            $DefaultValue = 0;
                            $Required     = 1;
                            $FormWidth        = 4;
                        }
                    }
    
                    if($table == 'process')
                    {
                        if(preg_match("/ApprovedByPersonID/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                            $Required  = 0;
                        }
                    }
    
                    if($table == 'faq')
                    {
                        if(preg_match("/Category/", $field))
                        {
                            $FormType  = "select";
                            $FieldExtra = "form.faqCategoryMenu";
                        }
                    }
    
                    if($table == 'processprocedure')
                    {
                        if(preg_match("/MadeByPersonID/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                            $Required  = 0;
                        }
                    }
                    if($table == 'passwords')
                    {
                        if(preg_match("/Password/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                            $Required  = 0;
                        }
                    }
                    if($table == 'objectusage')
                    {
                        if(preg_match("/NumberOfFlights/", $field))
                        {
                            $FormType  = "text";
                            $inputType = 'string';
                            $Required  = 1;
                        }
                    }
                    if($table == 'person')
                    {
                        if(preg_match("/DefaultInterface/", $field))
                        {
                            $FormType = "select";
                            $inputType = 'string';
                            $FieldExtra = "form.interfacemenu";
                        }
                        if(preg_match("/DefaultModule/", $field))
                        {
                            $FormType = "select";
                            $inputType = 'string';
                            $FieldExtra = "form.modulemenu";
                        }
                        if(preg_match("/DefaultTemplate/", $field))
                        {
                            $FormType = "select";
                            $inputType = 'string';
                            $FieldExtra = "form.templatemenu";
                        }
                    }
    
                    if(!strlen($outputType) > 0)
                    {
                        $outputType = $inputType;
                    }
    
                    #Check if field exists
                    $query = "select * from  confdbfields where TableName='$table' and TableField='$field'";
                    $exists = $dbh[$dsn]->get_row(array('query' => $query));
    
                    $fields    = "Active=1, ";
                    $fieldType = $dbh[$dsn]->db_escape($fieldType);


                    if(!$exists)
                    {
                        $query = "INSERT INTO confdbfields SET TableName='$table', TableField='$field', ValidFrom=NOW(), FormType='$FormType', FormTypeEdit='$FormTypeEdit', FieldType='$fieldType', FormHeight='$FormHeight', FormWidth='$FormWidth', PrimaryKey=$pk, Active=1, InputValidation='$inputType', OutputValidation='$outputType', FieldExtra='$FieldExtra', FieldExtraEdit='$FieldExtraEdit',Required='$Required', DefaultValue = '$DefaultValue'";
                        #print "$query<br>";
                        $result = $dbh[$dsn]->db_insert($query);
                        $new++;
                        $new_fields .= "$table.$field<br>";
                    }
                    else
                    {
                        #print "FW: $FormWidth = LEN: $len<br>";
                        if($exists->FormWidth > $len)
                        {
                            #$FormWidth = $len;
    
                        }
                        $query = "update confdbfields SET TableField='$field', FormWidth='$FormWidth', FormHeight='$FormHeight', FieldType='$fieldType', PrimaryKey=$pk, Active=1, FormType='$FormType', FormTypeEdit='$FormTypeEdit', InputValidation='$inputType',  OutputValidation='$outputType', FormWidth='$FormWidth', FieldExtra='$FieldExtra', FieldExtraEdit='$FieldExtraEdit', Required='$Required', DefaultValue = '$DefaultValue' where TableName='$table' and TableField='$field'";
                        if($table == 'xxx') {
                          #print_r($row);
                          #print $table." ".$type." ".$field." ".$len." ".$flags."<br>\n";
                          #print "FW: $FormWidth = LEN: $len<br>";
                          #print "$query<br>";
                        }
                        $result = $dbh[$dsn]->db_update($query);
                        $updated++;
                    }
                    $total++;
            } # end if columns
        } #end while table
        $query = "delete from confdbfields where Active=0";
        #print "$query<br>";
    
        $dbh[$dsn]->db_delete($query);
        $_lib['message']->add("<b>Database: $databaseName,  Antall felt: $total, oppdaterte. $updated, nye: $new<br>$new_fields</b>\n");
    
        #mysql_free_result($result);
    }
}
?>