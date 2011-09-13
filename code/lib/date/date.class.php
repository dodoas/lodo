<?
#FUnctions to get data correctly into the database

class Date
{
  var $weekday = array();
  var $db;

  function Date()
  {
    $this->weekday['1'] = 'mandag';
    $this->weekday['2'] = 'tirsdag';
    $this->weekday['3'] = 'onsdag';
    $this->weekday['4'] = 'torsdag';
    $this->weekday['5'] = 'fredag';
    $this->weekday['6'] = 'l&oslash;rdag';
    $this->weekday['7'] = 's&oslash;ndag';
  }

  function get_this_period($date)
  {
    global $_lib;

    #Input date on format yyyy-mm-dd
    if(preg_match('{(\d\d\d\d)-(\d\d)(.*)}', $date, $m))
    {
      return $m['1']."-".$m['2'];
    }
    else
    {
      $_lib['sess']->warning("Unable to get period for this date: $date");
    }
  }

  function get_this_month($date)
  {
    global $_lib;

    #print "gtm: $date<br>";

    #Input date on format yyyy-mm-dd
    if(preg_match('{\d\d\d\d-(\d\d)}', $date, $m))
    {
      #print_r($m);
      return $m['1'];
    }
    else
    {
      $_lib['sess']->warning("Unable to get period for this date: $date");
    }
  }

  function get_last_day_in_month($period)
  {
    global $_lib;
    $next_period        = $this->get_next_period(array('value'=>$period, 'realPeriod' => 1));

    $sql_get_last_day   = "SELECT DATE_SUB('$next_period-01', INTERVAL 1 DAY) as LastDayInMonth";
    $row                = $_lib['db']->get_row(array('query' => $sql_get_last_day));

    #$_log->file("$sql_get_last_day : $row->LastDayInMonth");
    return $row->LastDayInMonth;
  }

  function period_left_less_than($periodleft, $periodright)
  {
    #print "$periodleft >= $periodright\n";
    list($lyear, $lmonth) = split('-', $periodleft);
    list($ryear, $rmonth) = split('-', $periodright);

    $status = false;
    if($lyear < $ryear)
    {
        $status = true;
    }
    elseif(($lyear <= $ryear) and ($lmonth < $rmonth))
    {
        $status = true;
    }
    else
    {
        $status = false;
    }
    //print "status: $status. $lyear-$lmonth < $ryear-$rmonth\n";
    return $status;
  }

  function get_months_between_periods($fromperiod, $toperiod)
  {
    list ($FromYear, $FromMonth)    = split('-', $fromperiod);
    list ($ToYear,   $ToMonth)      = split('_', $toperiod);

    $months = ($ToYear - $FromYear) * 12;
    if($FromMonth < $ToMonth)
    {
        $months -= $ToMonth - $FromMonth;
    }
    elseif($FromMonth > $ToMonth)
    {
        $months += $ToMonth - $FromMonth;
    }
    return $months;
  }

    function get_this_period_last_year($date)
    {
        global $_lib;

        if(preg_match('{(\d\d\d\d)-(\d\d)(.*)}', $date, $m))
        {
            $m[1]--;
            return $m['1']."-".$m['2'];
        }
        else
        {
            $_lib['sess']->warning("Unable to get period for this date: $date");
        }
    }

    function get_this_period_next_year($date)
    {
        global $_lib;

        if(preg_match('{(\d\d\d\d)-(\d\d)(.*)}', $date, $m))
        {
            $m[1]++;
            return $m['1']."-".$m['2'];
        }
        else
        {
            $_lib['sess']->warning("Unable to get period for this date: $date");
        }
    }

  function get_this_year($date)
  {
    #Input date on format yyyy-mm-dd
    preg_match('{(\d\d\d\d)-\d\d-\d\d}', $date, $m);
    if(isset($m['1']))
    {
        return $m['1'];
    }
    else
    {
        preg_match('{(\d\d\d\d)-\d\d}', $date, $m);
        return $m['1'];
    }
  }

    function get_next_period($args)
    {
        global $_lib;
        if(is_array($args)){
          $period = $args['value'];
        } else {
          $period = $args;
        }
        if($args['realPeriod'] == 1)
        {
            #we want real periods ex: not period 13
            $maxPeriod = 12;
        }
        else
        {
            $maxPeriod = 13;
        }

        #Input date on format yyyy-mm
        if(preg_match('{(\d\d\d\d)-(\d\d)}', $period, $m))
        {
            $m[2] += 1;
            if($m[2] > $maxPeriod)
            {
                $m[1] += 1;
                $m[2] = 1;
            }
            return sprintf("%04d-%02d", $m['1'], $m['2']);
        }
        else
        {
            $_lib['sess']->warning("Unable to get next period for this date: $date");
        }
    }


    function get_prev_period($args)
    {
        global $_lib;

        $period = $args['value'];

        if($args['realPeriod'] == 1)
        {
            #we want real periods ex: not period 13
            $maxPeriod = 12;
        }
        else
        {
            $maxPeriod = 13;
        }

        #Input date on format yyyy-mm
        if(preg_match('{(\d\d\d\d)-(\d\d)}', $period, $m))
        {
            $m[2] -= 1;
            if($m[2] <= 0)
            {
                $m[1] -= 1;
                $m[2] = $maxPeriod;
            }
            return sprintf("%04d-%02d", $m['1'], $m['2']);
        }
        else
        {
            $_lib['sess']->warning("Unable to get prev period for this date: $date");
        }
    }

  function get_WeekDayName($day)
  {
    return $this->weekday[$day];
  }

  function get_WeekDayNameHash()
  {
    return $this->weekday;
  }

    function add_Years($date, $add)
    {
        if(isset($date))
        {
            if(preg_match('{(\d\d\d\d)-(\d\d)-(\d\d)}', $date, $m))
            {
                return sprintf("%04d-%02d-%02d", ($m['1'] + $add), $m['2'], $m['3']);
            }
        }
    }

  function add_Days($date, $add)
  {
    global $_lib;
    $query = "SELECT ADDDATE('$date', INTERVAL $add DAY) as duedate";
    $row = $_lib['db']->get_row(array('query' => $query));
    return $row->duedate;
  }

  #Return the difference in days between to dates
  function dateDiff($date1, $date2)
  {

    #########
    # needs mysql 4.1.1
    #$row = $_lib['db']->get_row(array('query'=>"select datediff('$date1', '$date2') as datediff"));
    #return $row->datediff;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $diff = $date1 - $date2;

    return floor($diff / 86400);
  }
  
  # for legal format strings, see: http://php.net/manual/en/function.strftime.php
  public function mysql_format($informat, $string) {
	  ## only supported in php 5.3
	  # for legal format strings, see: http://www.php.net/manual/en/function.date.php
	  # $dt = DateTime::createFromFormat($format, $string);
	  
	  # return $dt->format( 'Y-m-d H:i:s' );

  	  $outformat =  '%Y-%m-%d %H:%M:%S'; 
	  $ftime = strptime($string, $informat); 
	  $unxTimestamp = mktime( 
							 $ftime['tm_hour'], 
							 $ftime['tm_min'], 
							 $ftime['tm_sec'], 
							 1 , 
							 $ftime['tm_yday'] + 1, 
							 $ftime['tm_year'] + 1900 
                 ); 

	  return strftime($outformat, $unxTimestamp);
  }

  public function t_to_mysql_format($string) {
	  return self::mysql_format('%Y-%m-%dT%H:%M:%S%z', $string);
  }
}
?>
