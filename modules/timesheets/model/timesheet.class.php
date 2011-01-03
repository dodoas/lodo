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
            "SELECT a.AccountPlanID 
                  FROM accountplan a, timesheetpasswords p 
                  WHERE a.AccountPlanID = p.AccountPlanID
                        AND (a.AccountName = '%s' OR a.AccountPlanID = '%s')
                        AND p.Password = '%s'",
            $this->username, $this->username, $this->password);

        $r = $_lib['db']->db_query($sql);
        if($_lib['db']->db_numrows($r) == 0)
            throw new Exception("Timesheets: Login Failed");

        $row = $_lib['db']->db_fetch_assoc($r);
        $this->id = $row['AccountPlanID'];

        $_SESSION['timesheet_login'] = array($this->id, $this->username, $this->password);
    }

    public function logout()
    {
        unset($_SESSION['timesheet_login']);
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

        $sql = sprintf(
            "SELECT *
               FROM timesheets 
               WHERE AccountPlanID = %d 
                     AND CONCAT(YEAR(date), '-', MONTH(date)) = '%s'
               ORDER BY Date, EntryID",
            $this->id, $period);

        $r = $_lib['db']->db_query($sql);
            
        $entries = array();
        while( $row = $_lib['db']->db_fetch_assoc($r) )
        {
            $entries[] = $row;
        }

        return $entries;
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

    public function list_projects()
    {
        global $_lib;

        $r = $_lib['db']->db_query("SELECT ProjectID, Heading FROM project WHERE active = 1");

        $list = array();

        while($row = $_lib['db']->db_fetch_assoc($r))
        {
            $list[ $row['ProjectID'] ] = $row['Heading'];
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

        $sql = sprintf("DELETE FROM timesheetsworktype WHERE WorkTypeID = %d", $id);
        $_lib['db']->db_query($sql);

        return true;
    }

    public function list_worktypes()
    {
        global $_lib;

        $r = $_lib['db']->db_query("SELECT * FROM timesheetsworktype");
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

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function set_root($root)
    {
        $this->root = $root;
    }

    private function print_head()
    {
        if(!$this->user->is_admin())
        {
            printf("<html>\n" .
               "  <head>\n" .
               "    <title>Timesheet registration for %s</title>\n" .
               "  </head>\n" .
               "  <body>\n" .
               "    <p>Logget inn som %s (%d)<p>\n" .
               "    <p><a href='%s'>Tilbake</a></p>\n",
               $this->user->get_username(), $this->user->get_username(), $this->user->get_id(),
               $this->root
            );

            printf("    <p><a href='?logout'>log ut</a></p>");
        }
        else
        {
            printf("<h1>Timelister for %s</h1>" .
                   "<p><a href='%s'>Tilbake til oversikt</a></p>", 
                   $this->user->get_username(), $this->root);
        }
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
        if($this->user->is_admin())
        {
            if(isset($_POST['new_worktype']))
            {
                $this->user->new_worktype($_POST['new_worktype_name']);
            }
            else if(isset($_GET['delete_worktype']))
            {
                $this->user->del_worktype($_GET['delete_worktype']);
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

            printf("<td style='text-align: center;'>%s</td><td><a href='%s&tp=view&period=%s'>velg</a></td>", $month, $this->root, $period);
            
            printf("</tr>\n");
            $i ++;
        }
        echo '</table>';

        if($this->user->is_admin())
        {
            echo '<h2>Arbeidsarter</h2>';
            $worktypes = $this->user->list_worktypes();
            
            echo '<table>';
            foreach($worktypes as $k => $v)
            {
                printf("<tr><td>%s</td><td><a href='%s&delete_worktype=%d'>Slett</td></tr>\n", $v, $this->root, $k);
            }
            printf('<tr>
                  <form action="%s" method="post">
                  <td>
                    <input type="text" value="" name="new_worktype_name" />
                  </td>
                  <td>
                    <input type="submit" name="new_worktype" value="Lag">
                  </td>
                </tr>', $this->root);
            echo '</table>';
        }


        $this->print_bottom();
    }

    private function print_table($period, $fields, $array, $dest, $show_unlock = true)
    {
        if($period[0] == "_")
            $locked = true;
        else
            $locked = $this->user->is_period_locked($period);

        printf(
            "<form action='%s' method='post'>" .
            "<input type='hidden' name='save_table' value='save' />" .
            "<table>\n" .
            "<tr>\n",
            $dest
            );

        foreach($fields as $field => $field_data)
        {
            printf(' <th>%s</th> ', $field);
        }

        printf(
            "</tr>\n"
            );

        $i = 0;
        $sum_h = 0;
        $sum_m = 0;

        foreach($array as $entries)
        {
            foreach($entries as $entry)
            {
                printf("<tr style='background-color: %s'>\n", ( ($i + 1) % 2 != 0 ? "#DDD" : "#FFF" ));

                foreach($fields as $field => $field_data)
                {
                    $value = $field_data['default'];
                    if(isset($entry[$field])) 
                    {
                        $value = $entry[$field];
                    }

                    if($field == 'SumTime')
                    {
                        list($h, $m) = explode(':', $value);
                        $sum_h += $h;
                        $sum_m += $m;
                    }

                    foreach($fields as $name => $dummy) /* fetch first field name */
                        break;
                    $name = 'field_' . $entry[$name] . '_' . $field . '_' . $i;

                    $data = "";
                    if($field_data['type'] == "caption")
                    {
                        $data = htmlspecialchars($value);
                    }
                    else if($field_data['type'] == "text")
                    {
                        if($locked)
                            $data = $value;
                        else
                            $data = sprintf("<input name='%s' type='text' value='%s' id='$field' />", $name, 
                                            $value);
                    }
                    else if($field_data['type'] == "select")
                    {
                        if($locked)
                        {
                            $data = $field_data['options'][$value];
                        }
                        else
                        {
                            $data  = sprintf("<select name='%s'>\n", $name);
                            
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

                    printf(" <td>%s</td> ", $data);
                }

                if(!$locked)
                {
                    printf("<td><input type='submit' name='new_line_%s' value='Ny linje' /><td>".
                           "<td><input type='submit' name='del_line_%s' value='Slett linje' /></td>",
                           $entry['Day'], $entry['EntryID']);
                }
                
                printf("</tr>\n");

                $i++;
            }

        }

        $sum_h += (int)($sum_m / 60);
        $sum_m =  $sum_m % 60;
        printf("<tr><td></td><td>Sum</td><td></td><td>%s:%s</td></tr>",
               (strlen("$sum_h") < 2 ? "0$sum_h" : $sum_h),
               (strlen("$sum_m") < 2 ? "0$sum_m" : $sum_m));

        printf("</table>");

        if(!$locked)
            printf("<p><input type='submit' name='save' value='Lagre' /></p>");
        
        if($show_unlock && $this->user->is_admin())
        {
            if($locked)
            {
                printf("<p><input type='submit' name='unlock' value='L&aring;s opp' /></p>");
            }
            else
            {
                printf("<p><input type='submit' name='lock' value='L&aring;s' /></p>");
            }
        }

    }

    private function view()
    {
        global $_lib;
        $period_name = $this->user->escape($_REQUEST['period']);

        /*
         *
         * UPDATE TABLE
         *
         */
        if($this->user->is_admin() && isset($_POST['lock']))
        {
            $sql = sprintf(
                "INSERT INTO timesheetperiods (`Period`, `AccountPlanID`, `Locked`)
                      VALUES('%s', '%d', '1');",
                $period_name, $this->user->get_id());

            $_lib['db']->db_query($sql);
        }
        else if($this->user->is_admin() && isset($_POST['unlock']))
        {
            $sql = sprintf(
                "DELETE FROM timesheetperiods WHERE Period = '%s' AND AccountPlanID = %d",
                $period_name, $this->user->get_id());

            $_lib['db']->db_query($sql);
        }
        if(isset($_POST['save']))
        {
            $num_days = date("t", strtotime($period_name . '-01'));

            $sql = sprintf(
                "DELETE FROM timesheets 
                   WHERE Date >= '%s-01' 
                         AND Date <= '%s-%d' 
                         AND AccountPlanID = %d",
                $period_name, $period_name, $num_days, $this->user->get_id());

            $_lib['db']->db_query($sql);

            $matches = array();
            foreach($_POST as $k => $v)
            {
                preg_match('/field_(?P<Day>\d+)_BeginTime_(?P<No>\d+)/', $k, $matches);
                if(sizeof($matches) <= 0)
                    continue;

                $cols = array('BeginTime', 'EndTime', 'SumTime',
                              'Project', 'WorkType', 'Comment');

                /* should be a better way to do this */
                $sql = "INSERT INTO timesheets (`AccountPlanID`, `Date`";
                foreach($cols as $col)
                    $sql .= ", `$col`";
                $sql .= sprintf(") VALUES ('%d', '%s-%d'", 
                                $this->user->get_id(), $period_name, $matches['Day']);
                foreach($cols as $col)
                    $sql .= sprintf(", '%s'", $this->user->escape( 
                                        $_POST[sprintf('field_%s_%s_%d', 
                                                       $matches['Day'], $col, 
                                                       $matches['No'])]));
                $sql .= ");";

                $_lib['db']->db_query($sql);
            }
        }


        /*
         *
         * Draw table
         *
         */

        $period = $this->user->list_period($_REQUEST['period']);

        $this->print_head();

        $month_days = date("t", strtotime($_REQUEST['period'] . "-01"));
        $entries = array();

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
                $entries[$d] = array();
                $entries[$d][] = array('Day' => $d);
            }

            if(isset($_POST['new_line_' . $d]))
            {

                if (empty($entries[$d])) {
                    $entries[$d] = array();
                }
                $entries[$d][] = array('Day' => $d);
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
            'WorkType'   => array('type' => 'select', 'options' => $worktypes, 'default' => 0),
            'Comment'    => array('type' => 'text', 'size' => '255', 'default' => "")
            );

        ksort($entries);
        $this->print_table($period_name, $fields, $entries, $this->root . '&tp=view&period=' . $_REQUEST['period']);

        $this->print_bottom();
    }

    function listprojects()
    {
        global $_lib;

        $projects  = $this->user->list_projects();        
        
        printf("<h2>Velg Prosjekt</h2>");
        printf("<a href='javascript:history.go(-1)'>Tilbake</a><br />");
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
            'WorkType'   => array('type' => 'select', 'options' => $worktypes, 'default' => 0),
            'Comment'    => array('type' => 'text', 'size' => '255', 'default' => ""),
            'AccountName' => array('type' => 'text', 'size'=> '255', 'default' => "Unknown"),
            );

        $this->print_table("_" . $period, $fields, $entries, "", $false);
    }
};
