<?php

class timesheet_user
{
    private $id = false;
    private $username;
    private $password;
    private $admin = false;

    /**
     * Load a timesheet user.
     * force_id is used by admin to login without password
     */
    public function __construct($username = "", $password = "", $force_id = false)
    {
        if($force_id !== false)
        {
            $this->username = $username;
            $this->id       = $force_id;
            $this->admin    = true;
            unset($_SESSION["timesheet_login"]);
        }
        else if(!isset($_SESSION["timesheet_login"]))
        {
            $this->username = $this->escape($username);
            $this->password = $this->escape($password);

            $this->login();
        }
        else
        {
            list($this->id, $this->username, $this->password) = 
                $_SESSION["timesheet_login"];
        }
    }

    public function escape($str)
    {
        if(get_magic_quotes_gpc())
            $str = stripslashes($str);
        
        return mysql_escape_string($str);
    }

    private function login()
    {
        global $_lib;

        $sql = sprintf( 
            "SELECT a.AccountPlanID, a.AccountName
                  FROM accountplan a, timesheetpasswords p 
                  WHERE a.AccountPlanID = p.AccountPlanID
                        AND (a.BirthDate IS NOT NULL)
                        AND (a.AccountName = '%s' OR a.AccountPlanID = '%s')
                        AND p.Password = '%s'",
            $this->username, $this->username, $this->password);

        $r = $_lib['db']->db_query($sql);
        if($_lib['db']->db_numrows($r) == 0)
            throw new Exception("Timesheets: Login Failed");

        $row = $_lib['db']->db_fetch_assoc($r);
        $this->id = $row['AccountPlanID'];
        $this->username = $row['AccountName'];

        $_SESSION['timesheet_login'] = array($this->id, $this->username, $this->password);
    }

    public function logout()
    {
        unset($_SESSION['timesheet_login']);
	session_destroy();
    }

    public function get_id() 
    {
        return $this->id;
    }

    public function get_username() 
    {
        return $this->username;
    }

    public function is_admin()
    {
        return $this->admin;
    }


    public function list_customers()
    {
        global $_lib;

        $sql = "SELECT AccountPlanID, AccountName FROM accountplan WHERE AccountPlanType = 'customer'";
        $r = $_lib['db']->db_query($sql);

        $customers = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $customers[ $row['AccountPlanID'] ] = $row['AccountName'];
        }

        return $customers;
    }

    /**
     * Return an array of periods i.e. array('YYYY-MM')
     */
    public function list_periods()
    {
        global $_lib;

        $sql = sprintf(
            "SELECT CONCAT(YEAR(date), '-', MONTH(date)) as Period 
               FROM timesheets WHERE AccountPlanID = %d GROUP BY Period ORDER BY date",
            $this->id);

        $r = $_lib['db']->db_query($sql);

        $periods = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $periods[] = $row['Period'];
        }

        list($now_year, $now_month) = explode('-', date("Y-n"));

        for($i = 0; $i < 3; $i++)
        {
            $m = $now_month - $i;
            $y = $now_year;
            if($m <= 0)
            {
                $y -= 1;
                $m = 12 + $m;
            }

            if(!in_array("$y-$m", $periods))
            {
                $periods[] = "$y-$m";
            }
        }

        return $periods;
    }

    /**
     * Return an array of dates in a period
     * @param period
     *    period in format 'YYYY-MM'
     */
    public function list_period($period)
    {
        global $_lib;

        $period = $this->escape($period);
        if($period[0] == '_')
        {
            $period = substr($period, 1);
        }

        $sql = sprintf(
            "SELECT *
               FROM timesheets 
               WHERE AccountPlanID = %d 
                     AND CONCAT(YEAR(date), '-', MONTH(date)) = '%s'
               ORDER BY Date, BeginTime, EntryID",
            $this->id, $period);

        $r = $_lib['db']->db_query($sql);
            
        $entries = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $entries[] = $row;
        }

        return $entries;
    }

    public function list_diets() 
    {
        global $_lib;

        $sql = sprintf("SELECT * FROM timesheetdiet");
        
        $r = $_lib['db']->db_query($sql);
        
        $diets = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $diets[ $row['DietID'] ] = $row['Name'];
        }

        return $diets;
    }

    public function list_accommodations() 
    {
        global $_lib;

        $sql = sprintf("SELECT * FROM timesheetaccommodation");

        $r = $_lib['db']->db_query($sql);
        
        $accoms = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $accoms[ $row['AccommodationID'] ] = $row['Name'];
        }

        return $accoms;
    }

    public function get_stats($period) 
    {
        global $_lib;

        $period = $this->escape($period);

        $sql = sprintf(
            "SELECT 
               Sum(HOUR(SumTime) * 60 + MINUTE(SumTime) ) as sum, Project
             From timesheets
             WHERE AccountPlanID = %d
                   AND CONCAT(YEAR(date), '-', MONTH(date)) = '%s'
             GROUP BY Project
            ", $this->id, $period);

        $r = $_lib['db']->db_query($sql);
            
        $projects = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $projects[] = $row;
        }

        return $projects;  
    }

    public function is_period_locked($period)
    {
        global $_lib;

        $period = $this->escape($period);

        $sql = sprintf(
            "SELECT Locked FROM timesheetperiods WHERE AccountPlanID = %d AND Period = '%s'",
            $this->id, $period);
        $r = $_lib['db']->db_query($sql);

        $locked = false;
        while($row = $_lib['db']->db_fetch_assoc($r))
        {
            if($row['Locked'] == 1)
                $locked = true;
        }

        return $locked;
    }

    public function get_period_info($period)
    {
        global $_lib;
        $period = $this->escape($period);

        $sql = sprintf(
            "SELECT * FROM timesheetperiods WHERE AccountPlanID = %d AND Period = '%s'",
            $this->id, $period);

        $r = $_lib['db']->db_query($sql);

        $row = $_lib['db']->db_fetch_assoc($r);
        if(!$row)
        {
            $row = array('Locked' => false, 
                         'Period' => $period,
                         'AccountPlanID' => $this->id,
                         'Comment' => "");
        }

        return $row;
    }

    public function list_projects()
    {
        global $_lib;

        $r = $_lib['db']->db_query("SELECT ProjectID, Heading FROM project WHERE active = 1 ORDER BY Heading");

        $list = array();

        while($row = $_lib['db']->db_fetch_assoc($r))
        {
            $list[ $row['ProjectID'] ] = $row['Heading'];
        }

        return $list;
    }

    public function list_departments()
    {
        global $_lib;

        $r = $_lib['db']->db_query("SELECT CompanyDepartmentID, DepartmentName FROM companydepartment WHERE active = 1 ORDER BY DepartmentName");

        $list = array();

        while($row = $_lib['db']->db_fetch_assoc($r))
        {
            $list[ $row['CompanyDepartmentID'] ] = $row['DepartmentName'];
        }

        return $list;
    }

    public function new_worktype($name)
    {
        global $_lib;

        $name = $this->escape($name);
        $sql = sprintf("INSERT INTO timesheetsworktype (`Name`) VALUES ('%s')", $name);
        $_lib['db']->db_query($sql);

        return true;
    }

    public function del_worktype($id)
    {
        global $_lib;

        $sql = sprintf("DELETE FROM timesheetsworktype WHERE WorkTypeID = %d LIMIT 1", $id);
        $_lib['db']->db_query($sql);

        return true;
    }

    public function new_diet($name) 
    {
        global $_lib;

        $name = $this->escape($name);
        $sql = sprintf("INSERT INTO timesheetdiet (`Name`) VALUES ('%s')", $name);
        $_lib['db']->db_query($sql);

        return true;
    }

    public function del_diet($id)
    {
        global $_lib;

        $sql = sprintf("DELETE FROM timesheetdiet WHERE DietID = %d LIMIT 1", $id);
        $_lib['db']->db_query($sql);

        return true;
    }

    public function del_accommodation($id) 
    {
        global $_lib;

        $sql = sprintf("DELETE FROM timesheetaccommodation WHERE AccommodationID = %d", $id);
        $_lib['db']->db_query($sql);

        return true;
    }

    public function new_accommodation($name) 
    {
        global $_lib;

        $name = $this->escape($name);
        $sql = sprintf("INSERT INTO timesheetaccommodation (`Name`) VALUES ('%s')", $name);
        $_lib['db']->db_query($sql);

        return true;
    }

    public function list_worktypes()
    {
        global $_lib;

        $r = $_lib['db']->db_query("SELECT * FROM timesheetsworktype ORDER BY Name");
        $list = array();

        while($row = $_lib['db']->db_fetch_assoc($r))
        {
            $list[ $row['WorkTypeID'] ] = $row['Name'];
        }

        return $list;
    }

    public function update_entry($entryid, $begin, $end, $date, $comment)
    {
        global $_lib;

        $begin   = $this->escape($begin);
        $end     = $this->escape($end);
        $comment = $this->escape($comment);
        $entryid = (int)$entryid;

        $sql = sprintf(
            "UPDATE timesheets 
               SET BeginTime = '%s', EndTime = '%s', Date = '%s'
               WHERE AccountPlanID = %d AND EntryID = %d",
            $begin, $end, $this->id, $date, $entryid);
                 
        $_lib['db']->db_query($sql);

        return true;
    }
};

class timesheet_user_page
{
    private $user;
    private $root = "/?";

    private $months = array("N/A", "Januar", "Februar", "Mars", "April", "Mai",
                            "Juni", "Juli", "August", "September", "Oktober", "November", "Desember");


    public function __construct($user)
    {
        $this->user = $user;
    }

    public function set_root($root)
    {
        $this->root = $root;
    }

    private function create_timeselectbox($name, $class, $value, $options) 
    {
        $el = sprintf('<select name="%s" id="%s" class="%s">', $name, $name, $class);
        
        foreach($options as $v) 
        {
            $v2 = "$v";
            if(strlen($v2) < 2) 
                $v2 = "0$v2";
            
            if($v == $value) 
                $el .= sprintf('<option value="%d" selected="selected">%s</option>', $v, $v2);
            else
                $el .= sprintf('<option value="%d">%s</option>', $v, $v2);
            
        }

        $el .= '</select>';
        
        return $el;
    }
    
    private function print_head($title = '')
    {
        if($title != '')
            $title = ' - ' . $title;

        if(!$this->user->is_admin())
        {
            printf("<html>\n" .
                   "  <head>\n" .
                   "    <title>Timesheet registration for %s%s</title>\n" .
                   "    <script src='/lib/js/jquery.js'></script>\n" .
                   "    <style> * { font-size: 11px; } </style>\n" .
                   "  </head>\n" .
                   "  <body>\n" .
                   "    <p>Logget inn som %s (%d) %s<p>\n" .
                   "    <p><a href='%s'>Tilbake</a></p>\n",
                   $this->user->get_username(), $title,
                   $this->user->get_username(), $this->user->get_id(), $title,
                   $this->root
            );

            printf("    <p><a href='?logout'>log ut</a></p>");
        }
        else
        {
            printf("<h1>Timelister for %s%s</h1>" .
                   "<p><a href='%s'>Tilbake til oversikt</a></p>", 
                   $this->user->get_username(), $title, $this->root);

        }

        printf(
            "<style>".
            "a.hilight { color: black; }\n" .
            "table { border-collapse: collapse; }\n" .
            "td { padding: 2px; padding-right: 7px; }\n".
            "tr:hover { background-color: #eee; }\n" .
            ".BeginTime_h, .EndTime_h, .SumTime_h { background-color: green; color: white; }\n".
            ".day_0 { background-color: #f66; }\n".
            "</style>".

            "<style media='print'>".
            "#layout_top { display: none; }\n".
            "html { font-size: 10px; margin: 0px; }\n".
            "* { margin:0; padding 0; }\n".
            ".noprint,hr { display: none; }\n".
            "textarea { border: 0px; }\n".
            "input { border: 0px; }\n".
            "tr { border: 1px solid black; }\n".
            "</style>"

            );
    }

    private function print_bottom()
    {
        printf("  </body>\n" .
               "</html>\n");
    }

    public function pageswitch($page)
    {
        switch($page)
        {
        case 'view':
            $this->view(); break;

        case 'listprojects':
            $this->listprojects(); break;
        case 'listprojectperiods':
            $this->listprojectperiods(); break;
        case 'listprojectperiod':
            $this->listprojectperiod(); break;

        case '':
        case 'index':
        default:
            $this->index(); break;
        }
    }

    private function index() 
    {
        global $_lib;

        if($this->user->is_admin())
        {
            if(isset($_POST['new_worktype']))
            {
                $this->user->new_worktype($_POST['new_worktype_name']);
            }
            else if(isset($_POST['new_diet'])) 
            {
                $this->user->new_diet($_POST['new_diet_name']);
            }
            else if(isset($_POST['new_accom'])) 
            {
                $this->user->new_accommodation($_POST['new_accom_name']);
            }

            else if(isset($_GET['delete_worktype']))
            {
                $this->user->del_worktype($_GET['delete_worktype']);
            }
            else if(isset($_GET['delete_diet'])) 
            {
                $this->user->del_diet($_GET['delete_diet']);
            }
            else if(isset($_GET['delete_accom']))
            {
                $this->user->del_accommodation($_GET['delete_accom']);
            }
            else if(isset($_POST['update_diet']))
            {
                foreach($_POST as $k => $v)
                {
                    if("diet" == substr($k, 0, 4))
                    {
                        list($dummy, $id) = explode('_', $k);
                        $sql = sprintf("UPDATE timesheetdiet SET Name = '%s' WHERE DietID = %d",
                                       $this->user->escape($v), $id);
                        $_lib['db']->db_query($sql);
                    }
                }
            }
            else if(isset($_POST['update_accom']))
            {
                foreach($_POST as $k => $v)
                {
                    if("accom" == substr($k, 0, 5))
                    {
                        list($dummy, $id) = explode('_', $k);
                        $sql = sprintf("UPDATE timesheetaccommodation SET Name = '%s' WHERE AccommodationID = %d",
                                       $this->user->escape($v), $id);
                        $_lib['db']->db_query($sql);
                    }
                }
            }
            else if(isset($_POST['update_worktype']))
            {
                foreach($_POST as $k => $v)
                {
                    if("worktype" == substr($k, 0, 8))
                    {
                        list($dummy, $id) = explode('_', $k);
                        $sql = sprintf("UPDATE timesheetsworktype SET Name = '%s' WHERE WorkTypeID = %d",
                                       $this->user->escape($v), $id);
                        $_lib['db']->db_query($sql);
                    }
                }
            }
        }

        $periods = $this->user->list_periods();
        rsort($periods);

        $this->print_head();

        printf("<table>\n" .
               "  <tr>\n" .
               "    <th style='width: 200px;'>&Aring;r</th>\n" .
               "    <th style='width: 200px;'>Periode</th>\n"  .
               "    <th></th>\n" .
               "  </tr>\n");

        $last_year = "0";
        $i = 0;
        foreach($periods as $period)
        {
            list($year, $month) = explode('-', $period);

            printf("<tr>");
            
            if($year != $last_year) 
            {
                if($i != 0)
                {
                    printf("</tr><tr>");
                }

                $last_year = $year;
                printf("<td style='text-align: center;'>%s</td>", $year);
            }
            else
            {
                printf("<td></td>");
            }

            printf("<td style='text-align: left;'>%s</td><td><a href='%s&tp=view&period=%s'>velg</a></td>", $this->months[$month], $this->root, $period);
            
            printf("</tr>\n");
            $i ++;
        }
        echo '</table>';
        
        if(!$this->user->is_admin())
        {
            printf("<table>" .
                   "<tr><td>Annen m&aring;ned:</td>" .
                   "  <td><form action='%s' method='get'>" .
                   "    <input type='hidden' name='tp' value='view' />" .
                   "    <input type='text' value='YYYY-MM' name='period' />" .
                   "    <input type='submit' value='G&aring; til' />" .
                   "  </form></td>" .
                   "</tr>" .
                   "</table>",
                   $this->root);
        }

        if($this->user->is_admin())
        {
            $worktypes = $this->user->list_worktypes();
            $diets     = $this->user->list_diets();
            $accoms    = $this->user->list_accommodations();

            $width = "450px";
            
            echo '<h2>Arbeidsarter</h2>';
            echo '<table>';
            printf('<form action="%s" method="post">', $this->root);
            foreach($worktypes as $k => $v)
            {
                printf("<tr><td><input style='width: $width' value='%s' name='worktype_%d' /></td>", htmlspecialchars($v, ENT_QUOTES), $k);
                if( $_lib['sess']->get_person('AccessLevel') >= 4 )
                    printf("<td><a href='%s&delete_worktype=%d'>Slett</td>\n", $this->root, $k);
                printf("</tr>");
            }

            printf('<tr><td><input type="submit" value="Oppdater alle" name="update_worktype" onclick="return confirm(\'Er du sikkert?\')"></td></tr>');
            printf('</form>');

            printf('<tr>
                  <form action="%s" method="post">
                  <td>
                    <input style="width: '.$width.'" type="text" value="" name="new_worktype_name" />
                  </td>
                  <td>
                    <input type="submit" name="new_worktype" value="Opprett">
                  </td>
                </tr>
                </form>', $this->root);
            echo '</table>';


            echo '<h2>Dietter</h2>';
            echo '<table>';
            printf('<form action="%s" method="post">', $this->root);
            foreach($diets as $k => $v) 
            {
                printf("<tr><td><input style='width: $width' value='%s' name='diet_%d' /></td>", htmlspecialchars($v, ENT_QUOTES), $k);

                if( $_lib['sess']->get_person('AccessLevel') >= 4 )
                    printf("<td><a href='%s&delete_diet=%d'>Slett</a></td>\n", $this->root, $k);
                printf("</tr>");
            }

            printf('<tr><td><input type="submit" value="Oppdater alle" name="update_diet" onclick="return confirm(\'Er du sikkert?\')"></td></tr>');
            printf('</form>');

            printf('<tr>
                  <form action="%s" method="post">
                  <td>
                    <input style="width: '.$width.'" type="text" value="" name="new_diet_name" />
                  </td>
                  <td>
                    <input type="submit" name="new_diet" value="Opprett">
                  </td>
                </tr>
                </form>', $this->root);
            echo '</table>';

            echo '<h2>Overnattinger</h2>';
            echo '<table>';
            printf('<form action="%s" method="post">', $this->root);
            foreach($accoms as $k => $v) 
            {
                printf("<tr><td><input style='width: $width' value='%s' name='accom_%d' /></td>", htmlspecialchars($v, ENT_QUOTES), $k);
                if( $_lib['sess']->get_person('AccessLevel') >= 4 )
                    printf("<td><a href='%s&delete_accom=%d'>Slett</a></td>\n", $this->root, $k);
                printf("</tr>");
            }

            printf('<tr><td><input type="submit" value="Oppdater alle" name="update_accom" onclick="return confirm(\'Er du sikkert?\')"></td></tr>');
            printf('</form>');

            printf('<tr>
                  <form action="%s" method="post">
                  <td>
                    <input style="width: '.$width.'" type="text" value="" name="new_accom_name" />
                  </td>
                  <td>
                    <input type="submit" name="new_accom" value="Opprett">
                  </td>
                </tr>
                </form>', $this->root);
            echo '</table>';
        }

        $this->print_bottom();
    }

    /**
     * Print period table
     */
    private function print_table($period, $fields, $array, $dest, $show_unlock = true)
    {
        if($period[0] == "_")
        {
            $period = substr($period, 1);
            $locked = true;
        }
        else
        {
            $locked = $period_info['Locked'];
        }

        $period_info = $this->user->get_period_info($period);
        
        
        /*
         * Save button javascript-callback. Concats time-fields to one field before submit.
         */
        printf("
<script type='text/javascript'>
  $(document).ready(function(){
    $('input[type=\"submit\"]').click(function(){
      var read_and_remove = function() {
        var field_name = this.name.substring(0, this.name.length - 2);

        $('#' + field_name).val( $('#' + field_name + '_h').val() + ':' + $('#' + field_name + '_m').val() );
        $('#' + field_name + '_h').remove();
        $('#' + field_name + '_m').remove();
      };

      $.each($('.BeginTime_h'), read_and_remove);
      $.each($('.EndTime_h'), read_and_remove);
      $.each($('.SumTime_h'), read_and_remove);

      return true;
    });
  });
</script> 

");

        $width = 170 * count($fields);
        if(isset($_POST['report']))
        {
            $width = round($width * 0.60);
        }

        printf(
            "<form action='%s' method='post' id='tabel_form'>" .
            "<input type='hidden' name='save_table' value='save' />" .
            "<table style='width: %dpx;'>\n" .
            "<tr>\n",
            $dest, $width
            );

        /* Headers */
        foreach($fields as $field => $field_data)
        {
            printf(' <th style="text-align: left;">%s</th> ', $field_data['translation']);
        }

        printf(
            "</tr>\n"
            );

        $sum_fields = array();
        $i = 0;
        $sum_h  = 0;
        $sum_m  = 0;
        $sum_km = 0;
        $sum_parking = 0;
        $sum_drivingexpenses = 0;
        $sum_toll = 0;
        $last_day = -1;

        /* Table Body */
        foreach($array as $entries)
        {
            //foreach($entries as $entry)
            $entries_count = count($entries);
            for($entries_i = 0; $entries_i < $entries_count; $entries_i++)
            {
                $entry = $entries[ $entries_i ];

                foreach($fields as $name_ => $dummy) /* fetch first field name */
                    break;

                if($entry['Locked'] == '1')
                    $lockedLine = true;
                else
                    $lockedLine = false;

                $day_no = $entry[$name_];
                if($day_no != $last_day) 
                {
                    if($last_day != -1) 
                    {
                        printf("<tr><td colspan='%d' style='background-color: #aaa;'></td></tr>", count($fields));
                    }

                    $line_date = $entry['Date'];
                    $sum_time = 0.0;
                    $control = 0;
                    for($i = 0; $entries[$entries_i + $i]['Date'] == $line_date; $i++)
                    {
                        if ($control++ > 2000) {
                            break;
                        }
                        $t = explode(':', $entries[$entries_i + $i]['SumTime']);

                        $sum_time += ((int)$t[0] * 60) + (int)$t[1];
                    }

                    $w = date('w', strtotime($period . '-' . $day_no));
                    $wd = array('S&oslash;ndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'L&oslash;rdag');
                    $w2 = $wd[$w];
                    printf("<tr style='height: 25px;', class='day_%d'><td><b>%s</b></td><td colspan='2'><b>%s</b></td><td>%02d:%02d</td>".
                           "<td colspan='%d'><input type='submit' value='L&aring;s dag' name='lock_day_%s' />".
                           "<input type='submit' value='Lagre' name='save' /></td></tr>", 
                           $w, $day_no, $w2, 
                           (int)$sum_time / 60, $sum_time % 60,
                           count($fields)-2, $day_no);

                }

                printf("<tr style='%s' id='rowno_%d' class='row'><td></td>\n", "color:black;", $i);
                $last_day = $day_no;
                
                $buttons = "";
                $buttons .= sprintf("<td width=230>");

                if(!$locked && !$lockedLine)
                {
                    $buttons .= sprintf("<input type='submit' name='lock_line_%d' class='lock_line' value='L&aring;s linje' /> ", 
                                        $entry['EntryID']);

                    $buttons .= sprintf("<input type='button' id='new_line_%s_%d' value='Ny linje' class='new_line' /> ".
                                        "<input type='button' id='del_line_%s' class='del_line' value='Slett linje' />",
                                        $entry['Day'], $i, $i);
                }
                else if(!$locked)
                {
                    if($lockedLine && $this->user->is_admin())
                    {
                        $buttons .= sprintf("<input type='submit' name='unlock_line_%d' value='L&aring;s opp' class='unlock_line' /> ",
                                           $entry['EntryID']);
                    }

                    if(!$lockedLine)
                    {
                        $buttons .= sprintf("<input type='button' id='del_line_%s' class='del_line' value='Slett linje' /> ", $i);
                    }

                    $buttons .= sprintf("<input type='button' id='new_line_%s_%d' value='Ny linje' class='new_line' /> ",
                           $entry['Day'], $i);

                }

                if($lockedLine && $entry['LockedBy'] != '') 
                {
                    $buttons .= sprintf('<p>L&aring;st av %s %s</p>', $entry['LockedBy'], $entry['LockedTime']);
                }
                
                $buttons .= sprintf('</td>');
                    
                
                foreach($fields as $field => $field_data)
                {
                    $value = $field_data['default'];
                    if(isset($entry[$field])) 
                    {
                        $value = $entry[$field];
                    }

                    $name = 'field_' . $entry[$name_] . '_' . $field . '_' . $i;

                    if($field == 'Day') 
                    {
                        printf('<input type="hidden" name="id_%d" value="%s_%s" />', $entry['EntryID'], $entry[$name_], $i);
                        continue;
                    }
                    else if($field == 'Buttons')
                    {
                        echo $buttons;
                        continue;
                    }
                    else if($field == 'SumTime')
                    {
                        list($h, $m) = explode(':', $value);
                        $sum_h += $h;
                        $sum_m += $m;
                    }
                    else if($field == 'TravelDistance')
                    {
                        $sum_km += $value;
                    }
                    else if($field == 'Toll')
                    {
                        $sum_toll += $value;
                    }
                    else if($field == 'Parking') 
                    {
                        $sum_parking += $value;
                    }
                    else if($field == 'DrivingExpenses')
                    {
                        $sum_drivingexpenses += $value;
                    }

                    $data = "";
                    if($field_data['type'] == "caption")
                    {
                        $data = htmlspecialchars($value);
                    }
                    else if($field_data['type'] == "text")
                    {
                        $value = htmlspecialchars($value, ENT_QUOTES);

                        if($locked || $lockedLine)
                        {
                            $data = wordwrap($value, 30, '<br />');
                        }
                        else 
                        {
                            if($field_data['size'] < 5)
                                $text_align = 'right';
                            else
                                $text_align = 'left';

                            $data = sprintf("<input name='%s' type='text' value='%s' size='%d' class='$field' style='text-align: %s'/>", $name, 
                                            $value, $field_data['size'], $text_align);
                        }
                    }
                    else if($field_data['type'] == "time")
                    {
                        $value = substr($value, 0, 5);
                        list($h, $m) = explode(':', $value);
                        if($locked || $lockedLine)
                        {
                            $data = $value;
                        }
                        else 
                        {
                            if(strstr($name, "SumTime"))
                            {
                                $data = sprintf(
                                    "%s %s <input name='%s' type='hidden' value='' id='%s' />",
                                    $this->create_timeselectbox($name."_h", $field."_h", $h, range(0,23)),
                                    $this->create_timeselectbox($name."_m", $field."_m", $m, range(0,59,5)),
                                    $name, $name);
                            }
                            else if(strstr($name, "EndTime")) 
                            {
                                $data = sprintf(
                                    "%s %s <input name='%s' type='hidden' value='' id='%s' />",
                                    $this->create_timeselectbox($name."_h", $field."_h", $h, range(0,24)),
                                    $this->create_timeselectbox($name."_m", $field."_m", $m, range(0,59,5)),
                                    $name, $name);
                            }
                            else
                            {
                                $data = sprintf(
                                    "%s %s <input name='%s' type='hidden' value='' id='%s' />",
                                    $this->create_timeselectbox($name."_h", $field."_h", $h, range(0,23)),
                                    $this->create_timeselectbox($name."_m", $field."_m", $m, range(0,59,5)),
                                    $name, $name);
                            }
                        }
                                            
                    }
                    else if($field_data['type'] == "select")
                    {
                        /* make field sum */
                        if($field == "Diet" || $field == "Accommodation" || $field == "Project") 
                        {
                            if(!isset($sum_fields[$field])) 
                            {
                                $sum_fields[$field] = array();
                                $sum_fields[$field][$value] = 1;
                            }
                            else if(!isset($sum_fields[$field][$value]))
                            {
                                $sum_fields[$field][$value] = 1;
                            }
                            else 
                            {
                                $sum_fields[$field][$value] += 1;
                            }
                        }
                        else
                        {
                            if(!isset($sum_fields[$field])) 
                            {
                                $sum_fields[$field] = array();
                                //$sum_fields[$field][$value] = 1;
                                list($h, $m) = explode(':', $entry['SumTime']);
                                $sum_fields[$field][$value] = (float)$h + $m / 60.0;
                            }
                            else if(!isset($sum_fields[$field][$value]))
                            {
                                list($h, $m) = explode(':', $entry['SumTime']);
                                $sum_fields[$field][$value] = (float)$h + $m / 60.0;
                            }
                            else 
                            {
                                list($h, $m) = explode(':', $entry['SumTime']);
                                $sum_fields[$field][$value] += (float)$h + $m / 60.0;
                            }
                        }

                        if($locked || $lockedLine)
                        {
                            $data = $field_data['options'][$value];
                        }
                        else
                        {
                            $data  = sprintf("<select name='%s' style='width: 200px;'>\n", $name);

                            if($value == 0)
                                $nullselected = 'selected';
                            else
                                $nullselected = '';

                            $data .= sprintf("  <option value='0' %s> - </option>\n", $nullselected);
                                                        
                            foreach($field_data['options'] as $option => $option_value)
                            {
                                if($option == $value)
                                    $data .= sprintf("  <option value='%s' selected>%s</option>\n", $option, 
                                                     $option_value);
                                else
                                    $data .= sprintf("  <option value='%s'>%s</option>\n", $option, 
                                                     $option_value);
                            }
                       
                            $data .= sprintf("</select>\n");
                        }
                    }

                    printf(" <td class='field_%s'>%s</td> ", $field, $data);
                }

                
                printf("</tr>\n");

                $i++;
            }

        }

        echo "</table>";
        echo "<br />";
        echo "<table style='page-break-before: always;'>";

        $sum_h += (int)($sum_m / 60);
        $sum_m =  $sum_m % 60;
        printf("<tr><td><b>Sum timer</b></td><td></td><td></td><td>%s:%s</td>",
               (strlen("$sum_h") < 2 ? "0$sum_h" : $sum_h),
               (strlen("$sum_m") < 2 ? "0$sum_m" : $sum_m));

        $stats = $this->user->get_stats($period);
        $projects  = $this->user->list_projects();

        echo 
            "<td rowspan='10'><label style='display: block'>Kommentar</label><textarea cols='100' rows='18' name='comment'>" .
            htmlspecialchars( $period_info['Comment'], ENT_NOQUOTES ) .
            "</textarea></td></tr>";

        echo "<tr></tr>";


        $hilight_id = 0;
        foreach($stats as $stat)
        {
            $hilight_id ++;
            $sum_h = (int)($stat['sum'] / 60);
            $sum_m =  $stat['sum'] % 60;
            $p = $projects[ $stat['Project'] ];
            printf("<tr><td colspan=3><b>Sum <a href='#' class='hilight' id='hilight_%d'>%s</a</b></td><td>%s:%s</td></tr>",
                   $hilight_id, $p, 
                   (strlen("$sum_h") < 2 ? "0$sum_h" : $sum_h),
                   (strlen("$sum_m") < 2 ? "0$sum_m" : $sum_m));
                   
            ?>
<script>
     $('#hilight_<?= $hilight_id ?>').click(function(){
             $.each($('tr'),
                    function(){
                        var t = $(this);
                        //this.css({'backgroundColor': 'white'});
                        if(t.attr('id').substring(0, 5) != 'rowno')
                            return;

                        t.css({'backgroundColor': 'white'});
                        var is_correct_project = false;
                        var has_sum = false;

                        if(t.children('.field_Project').html() == '<?= $p ?>' &&
                           t.children('.field_SumTime').html() != '00:00') {
                            is_correct_project = true;
                            has_sum = true;
                        }

                        $.each( $('#' + t.attr('id') + ' select'), function(){
                                if($(this).attr('name').indexOf('Project') != -1 &&
                                   $(this).val() == '<?= $stat['Project'] ?>') {
                                    is_correct_project = true;
                                }
                                else if($(this).attr('name').indexOf('SumTime') != -1 &&
                                        $(this).val() > 0) {
                                    has_sum = true;
                                }
                            });

                        if(is_correct_project && has_sum) {
                            t.css({'backgroundColor': '#bbb'});
                        }
                    }
             );
             
         });
</script>
            <?
        }

        if($sum_km > 0) 
        {
            printf('<tr><td colspan=3><b>Sum reiselengde</b></td><td>%d km</td></tr>', $sum_km);
        }

        if($sum_toll > 0) 
        {
            printf('<tr><td colspan=3><b>Sum bompenger</b></td><td>%10.2f kr</td></tr>', $sum_toll);
        }

        if($sum_parking > 0) 
        {
            printf('<tr><td colspan=3><b>Sum parkering</b></td><td>%10.2f kr</td></tr>', $sum_parking);
        }

        if($sum_drivingexpenses > 0) 
        {
            printf('<tr><td colspan=3><b>Sum parkering</b></td><td>%10.2f kr</td></tr>', $sum_drivingexpenses);
        }


        printf('<tr style="height: 10px;"></tr><tr><td colspan=3><b>Antall oppf&oslash;ringer</b></td><tr>');
        foreach($sum_fields as $f => $a) 
        {
            $o = $fields[$f]['options'];

            printf('<tr><td colspan=3><b>%s</b></td></tr>', $fields[$f]['translation']);

            foreach($a as $n => $v) 
            {
                if($n == 0)
                    continue;

                if(!is_float($v)) 
                {
                    printf("<tr><td></td><td colspan=2>%s</td><td>%d</td></tr>", $o[$n], $v);
                }
                else
                {
                    $h = floor($v);
                    $m = ($v - $h) * 60.0;
                    printf("<tr><td></td><td colspan=2>%s</td><td>%02d:%02d</td></tr>", $o[$n], $h, $m);
                }
            }
        }

        printf("</table>");

        
        if(!$locked)
            printf("<p><input type='submit' name='save' value='Lagre' id='save_button' /></p>");
        
        if($show_unlock && $this->user->is_admin())
        {
            if($locked)
            {
                printf("<p><input type='submit' name='unlock' value='L&aring;s opp' class='noprint' /></p>");
            }
            else
            {
                printf("<p><input type='submit' name='lock' value='L&aring;s' class='noprint' /></p>");
            }
        }

        printf("</tabel>\n" .
               "</form>\n");

        ?>
        
<script>
$(document).ready(function() {
    var nextI = <?= $i ?>;

    var del_function = function() {
        var arr = this.id.split('_');
        var i = arr[2];
        $('#rowno_' + i).remove();
    };

    $('.del_line').click(del_function);


    $('.new_line').click(function() {
        var arr = this.id.split('_');
        var day = arr[2], i = arr[3];

        var color = (day % 2 != 0 ? "background-color: #000; color: white;" : "background-color:#FFF; color:black;");
<?
                $row = sprintf("<tr id='rowno_%%I%%' class='row'>\n");

                $buttons = sprintf("<td><input type='button' id='new_line_%s_%s' value='Ny linje' class='new_line' /> ".
                                   "<input type='button' id='del_line_%s' class='del_line' value='Slett linje' /></td>",
                                   "%DAY%", "%I%", "%I%");
                
                /*
                 * Folgende kode er identisk med noe som ligger lengre opp.
                 * Det burde faktoriseres. 
                 */
                foreach($fields as $field => $field_data)
                {
                    $value = $field_data['default'];

                    if($field == 'SumTime')
                    {
                        list($h, $m) = explode(':', $value);
                        $sum_h += $h;
                        $sum_m += $m;
                    }
                    else if($field == 'Day') 
                    {
                        $value ="";
                    }
                    else if($field == 'Buttons')
                    {
                        $row .= $buttons;
                        continue;
                    }

                    $name = 'field_%DAY%_' . $field . '_' . '%I%';

                    $data = "";
                    if($field_data['type'] == "caption")
                    {
                        $data = htmlspecialchars($value);
                    }
                    else if($field_data['type'] == "text")
                    {
                        if($field_data['size'] < 5)
                            $text_align = 'right';
                        else
                            $text_align = 'left';
                        
                        $data = sprintf("<input name='%s' type='text' value='%s' size='%d' class='$field' style='text-align: %s'/>", $name, 
                                        $value, $field_data['size'], $text_align);
                    }
                    else if($field_data['type'] == "time")
                    {
                        $value = substr($value, 0, 5);
                        list($h, $m) = explode(':', $value);

                        if(strstr($name, "SumTime"))
                        {
                            $data = sprintf(
                                "%s %s <input name='%s' type='hidden' value='' id='%s' />",
                                $this->create_timeselectbox($name."_h", $field."_h", $h, range(0,23)),
                                $this->create_timeselectbox($name."_m", $field."_m", $m, range(0,59,5)),
                                $name, $name);
                        }
                        else if(strstr($name, "EndTime")) 
                        {
                            $data = sprintf(
                                "%s %s <input name='%s' type='hidden' value='' id='%s' />",
                                $this->create_timeselectbox($name."_h", $field."_h", $h, range(0,24)),
                                $this->create_timeselectbox($name."_m", $field."_m", $m, range(0,59,5)),
                                $name, $name);
                        }
                        else
                        {
                            $data = sprintf(
                                "%s %s <input name='%s' type='hidden' value='' id='%s' />",
                                $this->create_timeselectbox($name."_h", $field."_h", $h, range(0,23)),
                                $this->create_timeselectbox($name."_m", $field."_m", $m, range(0,59,5)),
                                $name, $name);
                        }
                    }
                    else if($field_data['type'] == "select")
                    {
                        $data  = sprintf("<select name='%s' style='width: 200px;'>\n", $name);
                        
                        if($value == 0)
                            $nullselected = 'selected';
                        else
                            $nullselected = '';
                        
                        $data .= sprintf("  <option value='0' %s> - </option>\n", $nullselected);
                        
                        foreach($field_data['options'] as $option => $option_value)
                        {
                            if($option == $value)
                                $data .= sprintf("  <option value='%s' selected>%s</option>\n", $option, 
                                                 $option_value);
                            else
                                $data .= sprintf("  <option value='%s'>%s</option>\n", $option, 
                                                 $option_value);
                        }
                        
                        $data .= sprintf("</select>\n");
                    }

                    $row .= sprintf(" <td>%s</td> ", $data);
                }

                $row .= sprintf("</tr>\n");

                printf('
        var line = "%s";', str_replace("\n", "", addslashes($row)));

?>
        line = line.replace(/\%I\%/g, nextI)
                   .replace(/\%COLOR\%/g, color)
                   .replace(/\%DAY\%/g, day);
        var last_line = $(this).closest('tr');
        last_line.after(line);
        $('#new_line_' + day + '_' + nextI).click(arguments.callee);
        $('#del_line_' + nextI).click(del_function);
        nextI ++;

    });

	
});
</script>
        

        <?

    }

    private function save($period_name, $period, $cols) 
    {
        global $_lib;

        $project_info = $this->user->get_period_info($period_name);
        if(!isset($project_info['TimesheetPeriodID']))
        {
            $sql = sprintf(
                "INSERT INTO timesheetperiods (`Period`, `AccountPlanID`, `Locked`, `Comment`)
                      VALUES('%s', '%d', '0', '%s');",
                $period_name, $this->user->get_id(), $this->user->escape($_POST['comment']));
        }
        else
        {
            $sql = sprintf(
                "UPDATE timesheetperiods SET Comment = '%s' WHERE Period = '%s' AND AccountPlanID = %d",
                $this->user->escape($_POST['comment']), $period_name, $this->user->get_id());
        }
        $_lib['db']->db_query($sql);
        
        $num_days = date("t", strtotime($period_name . '-01'));
        
        // $period == $period_name?
        $locked = $this->user->is_period_locked($period);
        if($locked) 
        {
            die("Can't do that");
        }
        
        $sql = sprintf(
            "DELETE FROM timesheets 
                   WHERE Date >= '%s-01' 
                         AND Date <= '%s-%d' 
                         AND AccountPlanID = %d
                         AND Locked = 0",
                $period_name, $period_name, $num_days, $this->user->get_id());
        $_lib['db']->db_query($sql);
        
        $matches = array();
        foreach($_POST as $k => $v)
        {
            preg_match('/field_(?P<Day>\d+)_BeginTime_(?P<No>\d+)/', $k, $matches);
            if(sizeof($matches) <= 0)
                continue;
            
            /* should be a better way to do this */
            $sql = "INSERT INTO timesheets (`AccountPlanID`, `Date`";
            foreach($cols as $col)
                $sql .= ", `$col`";
            $sql .= sprintf(") VALUES ('%d', '%s-%d'", 
                            $this->user->get_id(), $period_name, $matches['Day']);
            foreach($cols as $col) 
            {
                $sql .= sprintf(", '%s'", $this->user->escape( 
                                    $_POST[sprintf('field_%s_%s_%d', 
                                                   $matches['Day'], $col, 
                                                   $matches['No'])]));
            }
            
            $sql .= ");";
            $_lib['db']->db_query($sql);
        }        
    }

    private function view()
    {
        global $_lib;
        $period_name = $this->user->escape($_REQUEST['period']);

        $add_line = false;
        foreach($_POST as $k => $v) 
        {
            if(substr($k, 0, 8) == "new_line") 
            {
                $add_line = true;
                break;
            }
        }

        /*
         *
         * UPDATE TABLE
         *
         */
        if($this->user->is_admin() && isset($_POST['lock']))
        {
            $project_info = $this->user->get_period_info($period_name);
            if(!isset($project_info['TimesheetPeriodID']))
            {
                $sql = sprintf(
                    "INSERT INTO timesheetperiods (`Period`, `AccountPlanID`, `Locked`)
                      VALUES('%s', '%d', '1');",
                    $period_name, $this->user->get_id());
            }
            else
            {
                $sql = sprintf(
                    "UPDATE timesheetperiods SET Locked = 1 WHERE Period = '%s' AND AccountPlanID = %d",
                    $period_name, $this->user->get_id());
            }

            $_lib['db']->db_query($sql);
        }
        else if($this->user->is_admin() && isset($_POST['unlock']))
        {
            $sql = sprintf(
                "UPDATE timesheetperiods SET Locked = 0 WHERE Period = '%s' AND AccountPlanID = %d",
                $period_name, $this->user->get_id());

            $_lib['db']->db_query($sql);
        }

        $cols = array('BeginTime', 'EndTime', 'SumTime',
                      'Project', 'CompanyDepartment', 'WorkType', 'Comment', 
                      'Diet', 'Accommodation', 'TravelRoute', 
                      'TravelDesc', 'TravelDistance', 'Toll', 
                      'Parking', 'Customer', 'DrivingExpenses');


        if(isset($_POST['save']))
        {
            $this->save($period_name, $period, $cols);
        }
        else
        {
            if($this->user->is_admin()) 
            {
                $locked_by = $this->user->escape(
                    $_lib['sess']->get_person('FirstName') . ' ' . $_lib['sess']->get_person('LastName')
                    );
            }
            else
            {
                $locked_by = $this->user->escape($this->user->get_username());
            }

            foreach($_POST as $k => $v) 
            {
                if(substr($k, 0, 8) == "lock_day")
                {
                    $this->save($period_name, $period, $cols);
                    $day = substr($k, 9);
                    
                    $sql = sprintf("UPDATE timesheets SET Locked = 1, LockedBy = '%s', LockedTime = NOW() WHERE AccountPlanID = %d AND Date = '%s-%s'",
                                   $locked_by, $this->user->get_id(), $period_name, $day);
                    $_lib['db']->db_query($sql);
                }
                else if(substr($k, 0, 9) == "lock_line") 
                {
                    $id = substr($k, 10);

                    list($lock_day, $lock_no) = explode('_', $_POST['id_' . $id]);

                    $update_rest = "";
                    foreach($cols as $col)
                    {
                        $update_rest .= sprintf(", %s = '%s'", $col,
                                                $this->user->escape(
                                                    $_POST[sprintf('field_%s_%s_%d',
                                                                   $lock_day, $col, $lock_no)]));
                    }

                    $sql = sprintf("UPDATE timesheets SET Locked = 1, LockedBy = '%s', LockedTime = NOW() %s WHERE AccountPlanID = %d AND EntryID = %d", 
                                   $locked_by, 
                                   $update_rest,
                                   $this->user->get_id(), 
                                   $id);

                    $_lib['db']->db_query($sql);
                    break;
                }
                else if(substr($k, 0, 11) == "unlock_line" && $this->user->is_admin()) 
                {                    
                    $id = substr($k, 12);
                    $sql = sprintf("UPDATE timesheets SET Locked = 0 WHERE AccountPlanID = %d AND EntryID = %d", 
                                   $this->user->get_id(), $id);

                    $_lib['db']->db_query($sql);

                    break;
                }
            }
        }


        /*
         *
         * Draw table
         *
         */

        $period = $this->user->list_period($_REQUEST['period']);

        $month_days = date("t", strtotime($_REQUEST['period'] . "-01"));
        $entries = array();

        // Ingen lokalisering?.. Føler jeg har gjort denne jobben før. Må lage noe system for det
        list($date_y, $date_m) = explode('-', $_REQUEST['period']);
        if($date_y[0] == '_')
        {
            $date_y = substr($date_y, 1);
        }

        $this->print_head(sprintf('%s %d', $this->months[$date_m], $date_y));

        foreach($period as $entry)
        {
            list($year, $month, $day) = explode('-', $entry['Date']);

            if(isset($_POST['del_line_' . $entry['EntryID']]))
            {
                $sql = sprintf(
                    "DELETE FROM timesheets
                       WHERE EntryID = %d LIMIT 1", $entry['EntryID']);

                $_lib['db']->db_query($sql);

            }
            else
            {
                if(!isset($entries[$day]))
                    $entries[$day] = array();
                
                $entry['Day'] = $day;

                $entries[$day][] = $entry;
            }
        }

        for($i = 1; $i <= $month_days; $i++)
        {
            $d = $i;
            if(strlen("$i") < 2)
                $d = "0$i";
            
            if(!isset($entries[$d]))
            {
                $entries[$i] = array();
                $entries[$i][] = array('Day' => $d, 'Sort' => 'behind');
            }

            if(isset($_POST['new_line_' . $d]))
            {
                if (empty($entries[$d])) 
                {
                    $entries[$d] = array();
                }
                $entries[$d][] = array('Day' => $d);
            }
        }

        $customers = $this->user->list_customers();
        $worktypes = $this->user->list_worktypes();
        $projects  = $this->user->list_projects();
        $departments = $this->user->list_departments();
        $diets     = $this->user->list_diets();
        $accommodations = $this->user->list_accommodations();

        $fields = array(
            'Day'        => array('type' => 'caption', 'size' => '3', 'translation' => 'Dag', 'checked' => true),
            
            'BeginTime'  => array('type' => 'time', 'size' => '10', 'default' => '00:00', 'translation' => 'Start', 'checked' => true),
            'EndTime'    => array('type' => 'time', 'size' => '10', 'default' => '00:00', 'translation' => 'Slutt', 'checked' => true),
            'SumTime'    => array('type' => 'time', 'size' => '10', 'default' => '00:00', 'translation' => 'Sum', 'checked' => true),
            'Buttons'    => array(''),
            'Comment'    => array('type' => 'text', 'size' => '30', 'default' => "", 'translation' => 'Kommentar', 'checked' => true),


            'Customer'   => array('type' => 'select', 'options' => $customers, 'default' => 0, 'translation' => 'Kunde', 'checked' => true),
            'Project'    => array('type' => 'select', 'options' => $projects, 'default' => 0, 'translation' => 'Prosjekt', 'checked' => true),
            'CompanyDepartment'    => array('type' => 'select', 'options' => $departments, 'default' => 0, 'translation' => 'Avdeling', 'checked' => true),
            'WorkType'   => array('type' => 'select', 'options' => $worktypes, 'default' => 0, 'translation' => 'Arbeidsart', 'checked' => true),


            'Diet'       => array('type' => 'select', 'options' => $diets, 'default' => 0, 'translation' => 'Diett', 'checked' => false),
            'Accommodation' => array('type' => 'select', 'options' => $accommodations, 'default' => 0, 'translation' => 'Overnatting', 'checked' => false),

            'TravelRoute' => array('type' => 'text', 'size' => '30', 'default' => "", 'translation' => 'Reiserute', 'checked' => false),
            'TravelDesc' => array('type' => 'text', 'size' => '30', 'default' => "", 'translation' => 'Reiseform&aring;l', 'checked' => false),
            'TravelDistance' => array('type' => 'text', 'size' => '3', 'default' => "0", 'translation' => 'Reiselengde (km)', 'checked' => false),

            'Toll' => array('type' => 'text', 'size' => 3, 'default' => '0.00', 'translation' => 'Bompenger', 'checked' => false),
            'Parking' => array('type' => 'text', 'size' => 3, 'default' => '0.00', 'translation' => 'Parkering', 'checked' => false),
            'DrivingExpenses' => array('type' => 'text', 'size' => 3, 'default' => '0', 'translation' => 'Reise', 'checked' => false)
            );
        
        $full_fields = $fields;

        if(isset($_POST['report']))
        {
            foreach($full_fields as $k => $v)
            {
                if(!isset($_POST[$k]))
                    unset($fields[$k]);
            }
        }

        ksort($entries);
        $this->print_table($period_name, $fields, $entries, $this->root . '&tp=view&period=' . $_REQUEST['period']);


        /*
         * Report generating interface
         */
        $report_period = $_REQUEST['period'];
        if($report_period[0] != '_')
            $report_period = '_' . $report_period;

        print '<div class="noprint">';
        print '<h2>Lag rapport</h2>';
        printf('<form action="%s" method="post">', $this->root . '&tp=view&period=' . $report_period);
        print '<input type="hidden" name="report" value="1">';
        foreach($full_fields as $field => $data)
        {
            if($field == 'Buttons')
                continue;
            
            if(isset($_POST['report']))
            {
                if(isset($_POST[$field]))
                    $checked = "checked";
                else
                    $checked = "";
            }
            else
            {
                if($data['checked'])
                    $checked = "checked";
                else
                    $checked = "";
            }

            printf('<input type="checkbox" name="%s" %s /> %s<br />',
                   $field, $checked, $data['translation']);
        }

        print '<br /><input type="submit" value="Lag rapport" /></form>';
        print '</div>';

        $this->print_bottom();
    }

    function listprojects()
    {
        global $_lib;

        $projects  = $this->user->list_projects();        
        
        printf("<h2>Velg Prosjekt</h2>");
        printf("<a href='javascript:history.go(-1)' class='noprint'>Tilbake</a><br />");
        printf("<form action='%s' method='post'>\n", $this->root . "&tp=listprojectperiods");

        printf("<select name='project'>\n");
        foreach($projects as $k => $v)
        {
            printf("<option value='%s'>%s</option>\n", $k, $v);
        }
        printf("</select>");

        printf("<input type='submit' value='Velg'>\n");
        printf("</form>");
    }

    function listprojectperiods()
    {
        $project = $_POST["project"];
        global $_lib;

        if(!$this->user->is_admin())
            return;

        $sql = sprintf(
            "SELECT CONCAT(YEAR(`date`), '-', MONTH(`date`)) as Period 
               FROM timesheets 
               WHERE SumTime > '00:00:00'
               GROUP BY Period ORDER BY `date`"
            );

        $r = $_lib['db']->db_query($sql);

        $periods = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $periods[] = $row['Period'];
        }

        printf("<h2>Velge periode</h2>");
        printf("<a href='javascript:history.go(-1)'>Tilbake</a>");

        printf("<table>");
        printf("<tr><td style='width: 100px;'><b>&Aring;r</b></td><td><b>M&aring;ned</b></td></tr>\n");

        $lastyear = "nil";
        foreach($periods as $k => $period)
        {
            list($year, $month) = explode("-", $period);

            if($year != $lastyear)
            {
                $lastyear = $year;
                $yeardata = $year;
            }
            else
            {
                $yeardata = "";
            }

            if($month < 10)
                $month = "0$month";

            printf("<tr><td>%s</td><td><a href='%s&tp=listprojectperiod&period=%s-%s&project=%d'>%s</a></td></tr>\n",
                   $yeardata, $this->root, $year, $month, $project, $month);
        }
        printf("</table>");
    }

    function listprojectperiod()
    {
        global $_lib;
        $period = $this->user->escape($_GET["period"]);
        $project = $_GET["project"];

        if(!$this->user->is_admin())
            return;

        printf("<h2>Oversikt over %s</h2>", $period);
        printf("<a href='javascript:history.go(-1)'>Tilbake</a>");

        if(strlen($period) == 7)
        {        
            $sql = sprintf(
                   "SELECT t.*, a.AccountName
                      FROM timesheets t, accountplan a
                    WHERE
                      t.Project = %d
                      AND t.AccountPlanID = a.AccountPlanID
                      AND Date >= '%s-01' AND Date <= '%s-01' + INTERVAL 1 MONTH
                      AND SumTime > '00:00:00'
                    ORDER BY t.Date, a.AccountName",
                   $project, $period, $period);
        }
        else
        {
            $sql = sprintf(
                   "SELECT t.*, a.AccountName
                      FROM timesheets t, accountplan a
                    WHERE
                      t.Project = %d
                      AND t.AccountPlanID = a.AccountPlanID
                      AND Date = '%s'
                      AND SumTime > '00:00:00'
                    ORDER BY t.Date, a.AccountName",
                   $project, $period);
        }

        $period = array();
        $entries = array();

        $r = $_lib['db']->db_query($sql);
        while($row = $_lib['db']->db_fetch_assoc($r))
        {
            $period[] = $row; 
        }

        foreach($period as $entry)
        {
            list($year, $month, $day) = explode('-', $entry['Date']);

            if(isset($_POST['del_line_' . $entry['EntryID']]))
            {
                $sql = sprintf(
                    "DELETE FROM timesheets
                       WHERE EntryID = %d LIMIT 1", $entry['EntryID']);

                $_lib['db']->db_query($sql);

            }
            else
            {
                if(!isset($entries[$day]))
                    $entries[$day] = array();
                
                $entry['Day'] = $day;
                $entries[$day][] = $entry;
            }
        }

        $worktypes = $this->user->list_worktypes();
        $projects  = $this->user->list_projects();

        $fields = array(
            'Day'        => array('type' => 'caption', 'size' => '3'),
            'BeginTime'  => array('type' => 'text', 'size' => '10', 'default' => '00:00:00'),
            'EndTime'    => array('type' => 'text', 'size' => '10', 'default' => '00:00:00'),
            'SumTime'    => array('type' => 'text', 'size' => '10', 'default' => '00:00:00'),
            'Project'    => array('type' => 'select', 'options' => $projects, 'default' => 0),
            //'CompanyDepartment'    => array('type' => 'select', 'options' => $projects, 'default' => 0),
            'WorkType'   => array('type' => 'select', 'options' => $worktypes, 'default' => 0),
            'Comment'    => array('type' => 'text', 'size' => '255', 'default' => ""),
            'AccountName' => array('type' => 'text', 'size'=> '255', 'default' => "Unknown"),
            );

        $this->print_table("_" . $period, $fields, $entries, "", $false);
    }
};
