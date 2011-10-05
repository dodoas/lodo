<?
#Functions to format the data properly for the users

class format {
  var $_DF = array();
  var $_NF = array();

  function format($args) {
    #Init
    $this->_DF = $args['_DF'];
    $this->_NF = $args['_NF'];
    $this->_dsn = $args['_dsn'];
    $this->_dbh = $args['_dbh'];
  }

  function Period($args)
  {
    global $_SETUP;
    $args = $this->CheckInput($args);
    $periodinput = $args['value'];

    if( (!isset($_SETUP['period_separator'])) or ($_SETUP['period_separator'] == "") or (strlen($_SETUP['period_separator']) == 0) )
        $separator = "-";
    else
        $separator = $_SETUP['period_separator'];

    if( (!isset($_SETUP['period_format'])) or ($_SETUP['period_format'] == "") or (strlen($_SETUP['period_format']) == 0) )
        $formating = "yyyymm";
    else
        $formating = $_SETUP['period_format'];

    if((isset($periodinput)) and ($periodinput != "") and ($periodinput != '0000-00'))
    {
        #deler opp formaterings string
        $j = 0;
        $periodformat = array();
        $tmp = substr($formating, 0, 1);
        for($i=0; $i<strlen($formating); $i++)
        {
            if(substr($formating, $i, 1) == $tmp)
            {
                $periodformat[$j] .= $tmp;
            }
            else
            {
                $j++;
                $tmp = substr($formating, $i, 1);
                $periodformat[$j] .= $tmp;
            }
        }

        #deler opp input data
        $periodparts = array();
        if( (preg_match("{(\d{4})(\d{2})}", $periodinput, $m)) )
        {
            $periodparts['yyyy'] = substr($periodinput,0,4);
            $periodparts['mm'] = substr($periodinput,4,2);
        }
        elseif( (preg_match("{(\d{4})\-(\d{2})}", $periodinput, $m)) )
        {
            $periodparts['yyyy'] = substr($periodinput,0,4);
            $periodparts['mm'] = substr($periodinput,5,2);
        }

        #matcher input mot formatering og setter sammen
        $periodoutput = "";
        $format == "";
        foreach($periodformat as $format)
        {
            if($format == 'yyyy')
            {
                $periodoutput .= $periodparts['yyyy'].$separator;
            }
            elseif($format == 'mm')
            {
                $periodoutput .= $periodparts['mm'].$separator;
            }
        }
        $periodoutput = substr($periodoutput, 0, strlen($periodoutput)-strlen($separator));

        $hash = array('value'=>$periodoutput);
        if(strlen($args['return']) > 0)
            return $hash[$args['return']];
        else
            return $hash;
    }
    else
    {
        #print("error in period string: $date");

        $hash = array('value'=>'', 'error' => "error in period string: $periodinput");
        if(strlen($args['return']) > 0)
            return $hash[$args['return']];
        else
            return $hash;
    }
  }

  #gyldig input er:
  #'yyyymmdd', 'yyyymmddhhmmss', 'yyyy-mm-dd hh:mm:ss', 'yyyy-mm-dd'
  function Date($args)
  {
    global $_SETUP;
    $args = $this->CheckInput($args);
    $dateinput = $args['value'];
    #print "inndato: $dateinput<br>";

    if( (!isset($_SETUP['date_separator'])) or ($_SETUP['date_separator'] == "") or (strlen($_SETUP['date_separator']) == 0) )
        $separator = " ";
    else
        $separator = $_SETUP['date_separator'];

    if( (!isset($_SETUP['date_format'])) or ($_SETUP['date_format'] == "") or (strlen($_SETUP['date_format']) == 0) )
        $formating = "ddMMyyyy";
    else
        $formating = $_SETUP['date_format'];

    if((isset($dateinput)) and ($dateinput != "") and ($dateinput != '0000-00-00'))
    {
        #deler opp formaterings string
        $j = 0;
        $dateformat = array();
        $tmp = substr($formating, 0, 1);
        for($i=0; $i<strlen($formating); $i++)
        {
            if(substr($formating, $i, 1) == $tmp)
            {
                $dateformat[$j] .= $tmp;
            }
            else
            {
                $j++;
                $tmp = substr($formating, $i, 1);
                $dateformat[$j] .= $tmp;
            }
        }

        #deler opp input data
        $dateparts = array();
        if( (preg_match("{(\d{4})(\d{2})(\d{2})(.*)}", $dateinput, $m)) || (preg_match("{(\d{4})(\d{2})(\d{2})}", $dateinput, $m)) )
        {
            $dateparts['yyyy'] = substr($dateinput,0,4);
            $dateparts['mm'] = substr($dateinput,4,2);
            $dateparts['dd'] = substr($dateinput,6,2);
        }
        elseif( (preg_match("{(\d{4})\-(\d{2})\-(\d{2})}", $dateinput, $m)) || (preg_match("{(\d{4})\-(\d{2})\-(\d{2}) (.*)}", $dateinput, $m)) )
        {
            $dateparts['yyyy'] = substr($dateinput,0,4);
            $dateparts['mm'] = substr($dateinput,5,2);
            $dateparts['dd'] = substr($dateinput,8,2);
        }

        #matcher input mot formatering og setter sammen
        $dateoutput = "";
        $format == "";
        foreach($dateformat as $format)
        {
            if($format == 'yyyy')
            {
                $dateoutput .= $dateparts['yyyy'].$separator;
            }
            elseif($format == 'mm' or $format == 'MM')
            {
                if($format == 'mm')
                {
                    $dateoutput .= $dateparts['mm'].$separator;
                }
                elseif($format == 'MM')
                {
                    $dateoutput .= $this->MonthToText(array('value'=>$dateparts['mm'], 'return'=>'value')).$separator;
                }
            }
            elseif($format == 'dd' or $format == 'DD')
            {
                if($format == 'dd')
                {
                    $dateoutput .= $dateparts['dd'].$separator;
                }
                elseif($format == 'DD')
                {
                    //$dateoutput .= $this->DayToText($dateparts['dd']); #har ikke laget enda
                }
            }
        }
        $dateoutput = substr($dateoutput, 0, strlen($dateoutput)-strlen($separator));

        $hash = array('value'=>$dateoutput);
        if(strlen($args['return']) > 0)
            return $hash[$args['return']];
        else
            return $hash;
    }
    else
    {
        #print("error in date string: $date");

        $hash = array('value'=>'', 'error' => "error in date string: $dateinput");
        if(strlen($args['return']) > 0)
            return $hash[$args['return']];
        else
            return $hash;
    }
  }

  function MonthToText($args)
  {
     $args = $this->CheckInput($args);
     $mndNr = $args['value'];

     $mnd = array();
     $mnd['01'] = 'Januar';
     $mnd['02'] = 'Februar';
     $mnd['03'] = 'Mars';
     $mnd['04'] = 'April';
     $mnd['05'] = 'Mai';
     $mnd['06'] = 'Juni';
     $mnd['07'] = 'Juli';
     $mnd['08'] = 'August';
     $mnd['09'] = 'September';
     $mnd['10'] = 'Oktober';
     $mnd['11'] = 'November';
     $mnd['12'] = 'Desember';

     if(strlen($mndNr) == 1)
        $mndNr = sprintf("%02d", $mndNr);

     $hash = array('value'=>$mnd[$mndNr]);
     if(strlen($args['return'])>0)
         return $hash[$args['return']];
     else
         return $hash;
  }

    function Amount($args)
    {
        $args = $this->CheckInput($args);
        $amount = $args['value'];

        if($args['roundoff'] == 'down')
        {
            $amount = floor($amount);
        }
        elseif($args['roundoff'] == 'up')
        {
            $amount = ceil($amount);
        }

        if(strlen($args['decimals'])>0)
        {
            $decimals = $args['decimals'];
        }
        else
        {
            $decimals = $this->_NF['decimals'];
        }

        $value = number_format($amount,  $decimals, $this->_NF['dec_point'], $this->_NF['thousands_sep']);

        if($args['nonzero'] == 1)
        {
            if($value == 0)
            {
                $value = '';
            }
        }

        $hash = array('value'=>$value);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function Percent($args)
    {
        $args = $this->CheckInput($args);
        $value = $args['value'];

        $hash = array('value'=>($value)."%");
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function ReversePercent($args)
    {
        $args = $this->CheckInput($args);
        $value = $args['value'];

        $hash = array('value'=>(substr($value,0,(strlen($value)-1))/100));
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function AccountNumber($args)
    {
        $value = $args['value'];
        if(strlen($value)==11)
        {
            $hash = array('value'=>(substr($value,0,4).".".substr($value,4,2).".".substr($value,6,5)));
        }
        else
            $hash = array('error'=>"error in accountnumber string", 'value'=>'');

        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function Int($args)
    {
        $hash = array('value'=>$args['value']);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function string($args)
    {
        $hash = array('value'=>$args['value']); #Could have a safestring, removing htmlcharacters and other bad things
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function StringPlain($args)
    {
        $hash = array('value'=>$args['value']); #Could have a safestring, removing htmlcharacters and other bad things
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function Email($args)
    {
        $hash = array('value'=>$args['value']);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function ZipCode($args)
    {
        $hash = array('value'=>$args['value']);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function Phone($args)
    {
        $hash = array('value'=>$args['value']);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function URL($args)
    {
        $hash = array('value'=>$args['value']);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function Country($args)
    {
        $hash = array('value'=>$args['value']);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function codeToCountry($code) 
    {
        global $_lib;

        if (strlen($code) != 2) {
            return "";
        }

        $query = "select LocalName from country where `Code` = '$code'";
        $row = $_lib['storage']->get_row(array('query' => $query));
        return $row->LocalName;
    }

    function countryToCode($country) {
        global $_lib;

        if (strlen($country) == 2) {
            return strtoupper($country);
        }

        $query = "select Code from country where LocalName = '" . $_lib['storage']->db_escape($country) . "'";
        $row = $_lib['storage']->get_row(array('query' => $query));
        return $row->Code;
    }

    #Must have a valid db record row in to show height/width/etc
    function file($args)
    {
        #$element  = "<a href=\"/auto/" . $args['table']. "/" . $args['value']. "\" title=\"Size: " . $row->{$args['field'] . 'Size'} . " kb, Type: " . $row->{$args['field'] . 'Type'}  . "\">" . $args['value']. "</a><br />";
        $hash = array('value'=>$args['value']);
        if(strlen($args['return'])>0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function Media($args)
    {
    }

    #Must have a valid db record row in to show height/width/etc
    function Image($args)
    {
       #print "Bilde<br>\n";
       #print_r($args);
        $element = "";
        $row = $args['row'];
        if($row->{$args['field'].'URL'})
        {
          $element .= "<a href=\"".$row->{$args['field'].'URL'}." title=\"".$row->{$args['field'].'Heading'}."\">";
        }

        if($args['useThumbnail'] == 1)
        {
            $dotpos = strpos($args['value'], '.');
            $args['value'] = substr($args['value'], 0, $dotpos)."_thumbnail".substr($args['value'], $dotpos, strlen($args['value']));

            $element .= "<img src=\"/auto/".$args['table']."/".$args['value']."\" align=\"".$row->{$args['field'].'Align'}."\" alt=\"".$row->{$args['field'].'Heading'}."\">";
        }
        elseif($args['value'])
        {
            $element .= "<img src=\"/auto/".$args['table']."/".$args['value']."\" height=\"".$row->{$args['field'].'Height'}."\" width=\"".$row->{$args['field'].'Width'}."\" align=\"".$row->{$args['field'].'Align'}."\" alt=\"".$row->{$args['field'].'Heading'}."\">";
        }

        if($row->{$args['field'].'URL'})
        {
            $element .= "</a><br>\n";
        }
        else
        {
            $element .= "<br>\n";
        }

        $hash = array('value'=> $element);

        if(strlen($args['return']) > 0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function image2($args)
    {
        $mediaRow = $args['mediaRow'];

        $element = "";
        $row = $args['row'];

        if($mediaRow->URL)
        {
          $element .= "<a href=\"$mediaRow->URL title=\"$mediaRow->Heading\">";
        }

        $element .= "<img src=\"auto/mediastorage/$mediaRow->FileName\" height=\"$mediaRow->Height\" width=\"$mediaRow->Width\" align=\"$mediaRow->Align\" alt=\"$mediaRow->Heading\" />";

        if($mediaRow->URL)
        {
            $element .= "</a><br>\n";
        }
        else
        {
            $element .= "<br>\n";
        }

        $hash = array('value'=> $element);

        if(strlen($args['return']) > 0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function media2($args)
    {
        global $_image;

        $mediaRow = $args['mediaRow'];

        $isImage = $_image->getImageOutputFunction(array('fileType'=>$mediaRow->Type));
        if(strlen($isImage) > 0)
        {
            if($args['table'] == 'media' and $args['field'] == 'MediaID')
            {
                $hash = $this->image2($args);
            }
        }
        else
        {
            return array('value'=>"<img src=\"img/empatixlogo.png\" height=\"16\" width=\"16\" align=\"center\" />");
        }

        if(strlen($args['return']) > 0)
            return $hash[$args['return']];
        else
            return $hash;
    }

    function Datetime($args)
    {
       $datetime = $args['value'];
       //print $datetime."...";
       if($datetime == '0000-00-00 00:00:00')
       {
            $hash = array('value'=>'');
            if(strlen($args['return'])>0)
                return $hash[$args['return']];
            else
                return $hash;
       }
       elseif(!$datetime)
       {
            $hash = array('value'=>'');
            if(strlen($args['return'])>0)
                return $hash[$args['return']];
            else
                return $hash;
       }

       if(preg_match('{(.*) (\d\d:\d\d:\d\d)$}', $datetime, $m))
       {
         //print "m1: $m[1], m2: $m[2]";
         $m['1'] = $this->Date(array('value'=>$m['1'], 'return'=>'value'));
       }
       elseif(preg_match('{(.*)(\d\d)(\d\d)(\d\d)$}', $datetime, $m))
       {
         #print "Datetime: #$datetime#, m1: $m[1], m2: $m[2]";
         $m['1'] = $this->Date(array('value'=>$m['1'], 'return'=>'value'));
         $m['2'] = $m['2'] . ":" . $m['3'] . ":" . $m['4'];
       }
       else
       {
         print "WARNING: Could not format this datetime: #$datetime#, year: ".$m['1'].", time: " . $m['2'] . $m['3'] . $m['4'];
       }
       if($m[2] == '00:00:00')
       {
            $hash = array('value'=>$m['1']);
            if(strlen($args['return'])>0)
                return $hash[$args['return']];
            else
                return $hash;
       }
       else
       {
            $hash = array('value'=>$m['1']." ".$m['2']);
            //print_r($hash);
            if(strlen($args['return'])>0)
                return $hash[$args['value']];
            else
                return $hash;
       }
    }

  function Active($args)
  {
    $args = $this->CheckInput($args);

    if($args['value'])
    {
      $hash = array('value'=>'P&aring;');
    }
    else
    {
      $hash = array('value'=>'Av');
    }
    if(strlen($args['return'])>0)
        return $hash[$args['return']];
    else
        return $hash;
  }

  function PersonIDToName($args)
  {
    $args = $this->CheckInput($args);

    $personid        = $args['value'];
    $query_person    = "select * from person where PersonID='$personid'";
    #print "$query_person";
    $row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query_person));
    $hash = array('value' => "$row->FirstName $row->LastName", 'error' => $error);
    if(strlen($args['return']) > 0) {
        return $hash[$args['return']];
    } else {
        return $hash;
    }
  }

  function PersonIDToEmail($args)
  {
    $args = $this->CheckInput($args);

    $personid       = $args['value'];
    $query_person   = "select * from person where PersonID='$personid'";
    $row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query_person));
    $hash = array('value' => $row->Email, 'error' => $error);

    if(strlen($args['return'])>0) {
        //print "ikke hash<br>";
        return $hash[$args['value']];
    } else {
        //print "hash<br>";
        return $hash;
    }
  }

  # args: type, value
  function TypeMenu($args)
  {
    global $_lib;
    $args = $this->CheckInput($args);

    if(!$args[value]) {
      $_lib['sess']->error("Missing value to typemenu convert: $args[type]");
    }
    $query_type    = "select MenuChoice from confmenues where MenuName='$args[type]' and MenuValue='$args[value]'";
    #print "$query_type<br />";
    $row = $_lib['db']->get_row(array('query' => $query_type));
    $hash = array('value' => $row->MenuChoice, 'error' => $error);
    if(strlen($args['return'])>0)
        return $hash[$args['return']];
    else
        return $hash;
  }

   # args: type, value
  function CompanyIDToName($args)
  {
    $args = $this->CheckInput($args);

    $companyid = $args['value'];
    $query_company    = "select VName from company where CompanyID='$companyid'";
    $row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query_company));
    $hash = array('value' => $row->VName, 'error' => $error);
    if(strlen($args['return'])>0)
        return $hash[$args['return']];
    else
        return $hash;
  }

  # args: type, value
  function AccountPlanIDToName($args)
  {
    $args = $this->CheckInput($args);
  
    $accountplanid  = $args['value'];
    $query_accountplan    = "select AccountName from accountplan where AccountPlanID='$accountplanid'";
    $row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query_accountplan));
    $hash = array('value' => $row->AccountName, 'error' => $error);
    if(strlen($args['return'])>0)
        return $hash[$args['return']];
    else
        return $hash;
  }

    function CheckInput($args)
    {
        if(!is_array($args))
        {
            $args = array('value'=>$args, 'return'=>'value');
        }
        return $args;
    }

}

?>
