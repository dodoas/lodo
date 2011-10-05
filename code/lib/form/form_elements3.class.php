<?
#$args: table, field, value, pk, max_height (defualt etter innhold), min_height (default 4 linjer), width, class (antall tegn i select, bredde text og textarea),  tabindex, accesskey
#Height should be caalculated on textarea fields based on number of characters stored in database - not nessecarry to spesify.
#Goal: Most similar input to all form elements
#Should be bulid so that all input elements only have the fields wich actually have values
#Should return form element instead of printing it (makes it easier to cache and reuse the elements

class form3
{
    public $_SETUP;
    public $_QUERY;
    public $tabindex    = 0;
    public $Locked      = false;

    function Form3($args)
    {
        #Init
        $this->_SETUP   = $args['_SETUP'];
        $this->_QUERY   = $args['_QUERY'];

        #print "$this->_dbh, $this->_dsn<br>";
        #print_r($this->_dbh);
    }

###########################################################

    function submit($args)
    {
        if($args['historyLink'] == 1)
        {
            if(!isset($args['historyValue']))
            {
                $args['historyValue'] = -1;
            }
            $args['onClick'] = "onClick=\"history.go(".$args['historyValue'].")\"";
        }
        $args['type'] = "submit";
        $name         = $args['name'];
        return $this->input($args);
        #Check if this role can have this button, if not disable it
    }

###########################################################

    function radiobutton($args)
    {
        $name = $this->MakeName($args);

        # Radiobuttons has always 1/0 result
        $element = "<input type=\"radio\" name=\"$name\" id=\"$name\" value=\"".$args['value']."\"";
        if($args['choice'] == $args['value'])
            $element = " $element checked=\"checked\" ";
        $element = " $element />";

        return $element;
    }

###########################################################

    function checkbox($args)
    {
      $name     = $this->MakeName($args);
      $tabindex = $this->MakeTabindex($args);

      if(isset($args['disabled']) && $args['disabled'] == true)
        $disabled="disabled";

      $element = "<input type=\"hidden\" name=\"$name\" value=\"0\" tabindex=\"$tabindex\" $disabled/>\n";
      $element = " $element <input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"1\" $disabled tabindex=\"$args[tabindex]\"";
      if($args['autosubmit'])
      {
        $element .= " onchange='submit()'";
      }
      if($args[value]) $element = " $element checked ";
      $element = " $element />\n";
      return $element;
    }

###########################################################

    function CompanyContactMenu3($args) {
      $args['query'] = $this->_QUERY['form']['companycontact'];
      return $this->_MakeSelect($args);
    }

###########################################################

    function Type_menu3($args) {
      $args['query'] = $this->_QUERY['form'][$args['type']];
      #print "test #" . $args['type'] . "# " . $args['query'] . "<br>";
      return $this->_MakeSelect($args);
    }

###########################################################

    function Avd_menu3($args)
    {
        $args['query'] = $this->_QUERY['form']['avdmenu'];
        return $this->_MakeSelect($args);
    }

    function Country_menu3($args)
    {
        $args['query'] = 'select Code, LocalName from country order by LocalName asc';
        $args['num_letters'] = 60;
        $args['width'] = 60;
        return $this->_MakeSelect($args);
    }

###########################################################

    function sone_menu($args)
    {
        $args['query'] = $this->_QUERY['form']['sonemenu'];
        return $this->_MakeSelect($args);
    }

###########################################################

    function kommune_menu($args)
    {
        $args['query'] = $this->_QUERY['form']['kommunemenu'];
        return $this->_MakeSelect($args);
    }

###########################################################
    function wysiwyg($args)
    {
        //print_r($args);
        $width  = $this->_width($args);
        $min_wysiwig_width = 35;
        //print "($width > $min_wysiwig_width)"."HER";
        if($width > $min_wysiwig_width and $args['notWysiwyg'] != 1)
        {
            if(!preg_match("/Safari/", $_SERVER['HTTP_USER_AGENT']))
            {
                $args['wysiwyg']    = true;
                $args['min_height'] = 14;
            }
        }
        //print_r($args);
        return $this->textarea($args);
    }

###########################################################
    #$args: table, field, value, pk, height, width, tabindex, acceskey
    function textarea($args)
    {
        #print "height: ".$args['height'].", min_height:".$args['min_height']."<br>\n";
        //unset($args['height']); #For now to unflexible
        if(!isset($args['min_height']))
        {
          $args['min_height'] = 5; #At least 4 lines height unless otherwise spesified, grows as large as needed
        }
        $tabindex           = $this->MakeTabindex($args);
        $name               = $this->MakeName($args);
        $height             = $this->_height($args);
        $width              = $this->_width($args);
        #print "width: $width, innwidth:" . $args['width'] . "<br>\n";
        #print "bruker: height: $height, min_height:" . $args['min_height'] . "<br>\n";

        #Added \n here after textarea. Firefox eats beginning \ns. Could introduce bugs in other browsers.
        $element = "<textarea name=\"$name\" id=\"$name\" cols=\"$width\" rows=\"$height\" tabindex=\"".$tabindex."\" accesskey=\"".$args['accesskey']."\">\n".$args['value']."</textarea>";
        if($args['wysiwyg'] and preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT']))
        {
            $element .= "<script type=\"text/javascript\" defer=\"1\">HTMLArea.replace(\"$name\")</script>";
        }
        #print_r($_SERVER);
        return $element;
    }

    ###########################################################
    #Must have a valid db record row in to show height/width/etc
    function file($args)
    {
        $args['type']  = 'file';
        $args['value'] = $args['value_unformatted'];
        $row          = $args['row'];

        if($args['onlyUpload'] == 1)
        {
            $element .= $this->input($args);
        }
        else
        {
            $element  = "<a href=\"/auto/" . $args['table']. "/" . $args['value_unformatted']. "\" title=\"" . $row->{$args['field'] . 'Heading'} . "\">" . $row->{$args['field'] . 'Heading'} . "</a><br />";
            $element .= $this->input($args);
            $element .= "<br>Size: " . $row->{$args['field'] . 'Size'} . " kb, Type: " . $row->{$args['field'] . 'Type'};
        }

        return $element;
    }

    ###########################################################
    #Must have a valid db record row in to show height/width/etc
    function Image($args)
    {
        $args['type']         = 'file';
        $args['width_sub']    = 10; #Subtrack the size of the upload button
        $row                  = $args['row'];
        $field                = $args['field'];

        $element = "";

        if(!isset($args['value']))
        {
            #$element  = "<a href=\"/auto/" . $args['table']. "/" . $args['value_unformatted']. "\" target=\"_top\">";
            $element .= "<img src=\"/auto/".$args['table']."/".$args['value_unformatted']."\" height=\"".$row->{$args['field'].'Height'}."\" width=\"".$row->{$args['field'].'Width'}."\" /><br />\n";
            #$element .= "</a>";
        }
        else
        {
            $element .= $args['value'];
        }

        $args['value']        = $args['value_unformatted'];

        $element .= $this->input($args);

        if($args['value'])
        {
            $element .= "<br />\n".$row->{$args['field'].'Heading'}." Size: ".$row->{$args['field'].'Size'}." kb, Type: ".$row->{$args['field'].'Type'}." Height: ".$row->{$args['field'].'Height'}." Width: ".$row->{$args['field'].'Width'}."\n";

            $args['value'] = $row->{$field . 'Align'};
            $args['field'] = $field . 'Align';
            $args['extra'] = 'form.typerequestimgalignmenu';
            $args['type']  = 'select';
            $element .= "<br>Align: " . $this->select($args) . "<br />\n";

            $args['value'] = $row->{$field . 'URL'};
            $args['field'] = $field . 'URL';
            $args['type']  = 'text';
            $element .= "URL: " . $this->input($args) . "<br />\n";

            $args['value'] = $row->{$field . 'Heading'};
            $args['field'] = $field . 'Heading';
            $args['type']  = 'text';
            $element .= "Heading: " . $this->input($args) . "<br />\n";
        }

        return $element;
    }

    function Image2($args)
    {
        $mediaRow = $args['mediaRow'];

        $args['type']         = 'file2';

        $row                  = $args['row'];
        $field                = $args['field'];

        $element = "";

        $element .= $args['value']; #insert formated value (the picture)

        $args['value'] = ''; //$args['value_unformatted'];

        $args['name'] = 'mediastorage_FileName_'.$mediaRow->OrgMediaStorageID;

        $element .= $this->input($args);

        unset($args['name']);

        if($args['showOnlyImage'] != 1)
        {
            $element .= "<br />\nHeading: $mediaRow->Heading Size: $mediaRow->Size kb, Type: $mediaRow->Type Height: $mediaRow->Height Width: $mediaRow->Width\n";

            $args['value'] = $mediaRow->Align;
            $args['name']  = 'mediastorage_Align_'.$mediaRow->MediaStorageID;
            $args['extra'] = 'form.typerequestimgalignmenu';
            $args['type']  = 'select';
            $element .= "<br>Align: ".$this->select($args)."<br />\n";

            $args['value'] = $mediaRow->URL;
            $args['name'] = 'mediastorage_URL_'.$mediaRow->MediaStorageID;
            $args['type']  = 'text';
            $element .= "URL: ".$this->input($args)."<br />\n";

            $args['value'] = $mediaRow->Heading;
            $args['name'] = 'mediastorage_Heading_'.$mediaRow->MediaStorageID;
            $args['type']  = 'text';
            $element .= "Heading: ".$this->input($args)."<br />\n";
        }

        return $element;
    }

    function text($args) {
      $args['type'] = 'text';
      return $this->input($args);
    }

    function password($args) {
      $args['type'] = 'password';
      return $this->input($args);
    }

    function hidden($args) {
      $args['type'] = 'hidden';
      if($args['type'] == 'hidden' and isset($args['name']))
        $name = $args['name'];
      return $this->input($args);
    }

   function date($args) {
     $args['validation'] = 'Date';
     return $this->input($args);
   }

###########################################################
#$args: type=text/password/hidden/submit, height, width, table, field, value, pk, accesskey, class
#type=submit or hidden allows use of the name parameter

    function input($args)
    {
        #print_r($args);
        
        if($this->Locked) {
            return $args['value'];
        }

        if(strlen($args['name'])>0)
        {
            $name = $args['name'];
        }
        else
        {
            $name = $this->MakeName($args);
        }

        $tabindex = $this->MakeTabindex($args);

        $width = $this->_width($args);

        $element = '';

        if($args['type'] == 'file2')
        {
            $element .= "<input type=\"hidden\" name=\"table\" id=\"table\" value=".$args['table']." />\n";
            $element .= "<input type=\"hidden\" name=\"field\" id=\"field\" value=".$args['field']." />\n";

            $args['type'] = 'file';
        }

        $element  .= "<input type=\"".$args['type']."\" name=\"$name\" id=\"$name\" value=\"".$args['value']."\" ";
        if($args['type'] != 'hidden')
        {
          $element .= " size=\"$width\" ";
          $element .= " tabindex=\"$tabindex\" ";
        }
        if($args['maxlength'])
        {
          $element .= " maxlength=\"".$args['maxlength']."\" ";
        } elseif($args['width'])
        {
          $element .= " maxlength=\"".$args['width']."\" ";
        }

        if($args['accesskey'])
        {
          $element .= " accesskey=\"".$args['accesskey']."\" ";
        }
        if($args['OnChange'])
        {
          $element .= " OnChange=\"".$args['OnChange']."\" ";
        }
        if($args['OnClick'])
        {
          $element .= " OnClick=\"".$args['OnClick']."\" ";
        }
        if($args['class'])
        {
            $element .= " class=\"".$args['class']."\" ";
        }
        if(isset($args['title']))
        {
            $element .= " title=\"".$args['title']."\" ";
        }
        if(isset($args['OnKeyUp'])) {
            $element .= " OnKeyUp=\"".$args['OnKeyUp']."\" ";
        }
        if(isset($args['confirm'])) {
            $element .= " onClick=\"return confirm('" . $args['confirm'] . "')\"";
        }

        $element .= " />\n";
        //print_r($args);
        if(isset($args['validation']) && $args['validation'] == 'Date' or $args['validation'] == 'Datetime')
        {
            $obj =  $args['field'].$args['pk'];
            $element .= "<script type=\"text/javascript\">\n";
            $element .= "var $obj = new calendar1(document.forms['".$args['form_name']."'].elements['".$name."']);\n";
            $element .= "$obj.year_scroll = true;\n";
            if($args['validation'] == 'Date')
            {
                $element .= "$obj.time_comp = false;\n";
            }
            else
            {
                $element .= "$obj.time_comp = true;\n";
            }
            $element .= "</script>\n";
            $element .= "<a href=\"javascript:$obj.popup();\"><img src=\"/lib/tigra_calendar/img/cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Choose date\" /></a>\n";
        }
        return $element;
    }

###########################################################

    function img($args)
    {

        if(isset($args['path']) and isset($args['filename']))
        {
            $filepath = $args['path'].$args['filename'];
        }
        else
        {
            $filepath = "/auto/".$args['table']."/".$args['value'];
            //print $filepath;
        }

        if(isset($args['width']) or isset($args['height']))
        {
            $width ="width=\"".$args['width']."\"";
            $height ="height=\"".$args['height']."\"";
        }
        elseif(($args['thumbnail']) == 1)
        {
            $width ="width=\"215\"";
            $height ="height=\"215\"";
        }

        if(isset($args['alt']))
        {
            $alt = "alt=\"".$args['alt']."\"";
        }

        $element = "<img src=\"$filepath\" $width $height $alt />";
        return $element;
    }

###########################################################

    #returnerer navn ut i fra tabelnavn, feltnavn, prim�rn�kkel
    function MakeName($args)
    {
        if(isset($args['name']) )
            return $args['name'];
        elseif($args['pk'])
        {
            return $args['table'].".".$args['field'].".".$args['pk'];
        }
        elseif($args['field'])
        {
            return $args['table'].".".$args['field'];
        }
        else
        {
            return $args['table'];
        }
    }

###########################################################

    function MakeTabindex($args)
    {
        if(isset($args['tabindex']))
        {
            return $args['tabindex'];
        }
        else
        {
            return $this->tabindex++;
        }
    }

###########################################################
     #query = queryname, sql=complete sql query
    function select($args)
    {
        global $_lib;
        if(isset($args['sql']))
        {
            $args['query'] = $args['sql'];
        }
        else
        {
            #args query in is module.query. Splkit and use
            if(isset($args['query']))
            {
                $args['extra'] = $args['query'];
            }
            $param = split('\.', $args['extra']);
            $args['query'] = $this->_QUERY[$param['0']][$param['1']];
        }

        if($args['query'] || $args['data'])
        {
           return $this->_MakeSelect($args);
        } else {
            $_lib['sess']->error("Query empty or missing data to select: ".$args['extra']);
            print "Problems with query: ".$args['extra'].$param['0'].$param['1'];
        }
    }

###########################################################
    #input: required, name, tabindex, query, value, table, field, combinedmenu, width
    #combinedmenu #Make a menu with the ids listed and the names listed in front combined
    function _MakeSelect($args)
    {
        global $_lib;

        $name       = $this->MakeName($args);
        $tabindex   = $this->MakeTabindex($args);

        if(isset($args['disabled'])) {
            $disabled = true;
        }
        else {
            $disabled = false;
        }

        if(isset($args['class']))
        {
            $class = $args['class'];
        }
        else 
        {
            $class = "";
        }

        if(isset($args['width']))
        {
            $width = $args['width'];
        }
        elseif(isset($args['num_letters']))
        {
            $width = $args['num_letters'];
        }
        else
        {
            $width = 20;
        }

        if($args['notShowKey'] == 1)
        {
            $notShowKey = 1;
        }
        else
        {
            $notShowKey = 0;
        }

        if(strlen($args['notChoosenText']) > 0)
        {
            $notChoosenText = $args['notChoosenText'];
        }
        else
        {
            if(true || $_sess->language == 'no') # hardcode to norwegian since language not working
                $notChoosenText = 'Ikke valgt';
            else
                $notChoosenText = 'Not chosen'; 
        }

        if(isset($args['debug']))
        {
            print "query: ".$args['query']."<br />name: ".$name."<br />";
        }

        if(isset($args['id'])) 
        {
            $id = $args['id'];
        }
        else
        {
            $id = $name;
        }   

        // Disabled fields doesn't get submited 
        // a quick fix for this is to make the selectbox with a different name than 
        // requested and make a second hidden field with the original name and the value.
        if($disabled) {
            $element    = '<input type="hidden" name="'.$name.'" value="'.$args['value'].'" />';
            $element    .= "<select name=\"".$name."_disabled\" id=\"$id\" class=\"$class\" disabled=\"disabled\" ";
        }
        else {
            $element    = "<select name=\"$name\" id=\"$id\" class=\"$class\" "; 
        }
        if($tabindex)  { $element .= " tabindex=\"$tabindex\""; }
        if($accesskey) { $element .= " accesskey=\"$args[accesskey]\""; }
        if($args['autosubmit']) { $element .= " onchange=submit()";  }
        $element .= ">\n";

        if($args['required'] == false) #not choosen option
        {
            $element .= "<option value=\"\">".substr($notChoosenText,0, $width)."</option>\n";
        }

        $combinedmenudata = array(); #Make a menu with the ids listed and the names listed in front combined
        
        if($args['query']) {
            $result     = $_lib['db']->db_query($args['query']);
            $fieldCount = $_lib['db']->db_NumFields($result);
    
            while($_row = $_lib['db']->fetch_row($result)) {
                if($fieldCount > 1) {
                    $tmp = '';
                    for($i=1; $i<$fieldCount; $i++)
                    {
                        $tmp .= strip_tags($_row[$i]) ." ";
                    }
                    $tmp = trim($tmp);
    
                    if($notShowKey == 0) 
                    {
                        if(strlen(trim($tmp)) == 0)
                        {
                            $data[$_row['0']] .= $_row['0'];
                        }
                        else
                        {
                            #$option .= $tmp."-".$_row['0'];
                            $data[$_row['0']] .= $tmp;
                        }
                    }
                    else
                    {
                        $data[$_row['0']] .= $tmp;
                    }
                }
                else
                {
                    $data[$_row['0']] .= $_row['0'];
                }
            }
        } else {
            $data = $args['data'];
        }

        if(is_array($data)) {
            foreach($data as $key => $value)
            {
                $option = "";

                if($key == $args['value'] and strlen($args['value']) > 0)
                {
                  $element .= '<option value="' . $key. '" selected="selected">';
                  $found = true;
                }
                else
                {
                  $element .= '<option value="' . $key . '">';
                }

                $element .= substr($value, 0, $width) . "</option>\n";
            }

            if($args['combinedmenu'])
            {
                ksort($data);

                foreach($data as $key => $value)
                {
                    $name = strip_tags($name);
                    $element.= "<option value=\"$key\">" . substr("$key - $value", 0, $width) . "</option>\n";
                }
            }

            if(!$found and isset($args['value']) and $args['value'])
            {
                $element .= "<option value=\"\" selected>".substr('Verdi ikke funnet' . ': '.$args['value'],0, $width)."</option>\n";
            }
            $element .= "</select>\n";
        } else {
            $element = '';
        }

        return $element;
    }

    function PaymentMeansMenu() {
        global $_lib;

        $query_card             = "select AccountNumber, AccountNumber, AccountDescription from account where Active=1 order by Sort";
        $bankaccountcardA       = $_lib['db']->get_arrayrow(array('query' => $query_card));

        $query_card             = "select CardNumber, CardNumber, CardType from bankaccountcard where Active=1 order by Sort";
        $bankaccountcardA       = $_lib['db']->get_arrayrow(array('query' => $query_card));

        $query_card             = "select * from confmenues where Active=1 and MenuName='PaymentMeans' order by Sort";
        $bankaccountcardA       = $_lib['db']->get_arrayrow(array('query' => $query_card));
        
        return $this->select(array('data' => $bankaccountcardA));
    }

###########################################################

    function PeriodeYear_menu3($args)
    {
        $args['query'] = $this->_QUERY['form']['periodmenu'];
        return $this->_MakeSelect($args);
    }

###########################################################
  function AccountPeriod_menu3($args) {
      global $_QUERY;
      if(isset($args['noaccess'])) {
        $args['query'] = $_QUERY['form']['periodallmenu'];
      }
      elseif($args['access'] > 2) {
        $args['query'] = $_QUERY['form']['periodaccess2menu'];
      } else {
        $args['query'] = $_QUERY['form']['periodaccessmenu'];
      }
    return $this->_MakeSelect($args);
    }
###########################################################

    function Product_menu3($args)
    {
        $args['query']          = $this->_QUERY['form']['productmenu'];
        $args['combinedmenu'] = true;
        return $this->_MakeSelect($args);
    }

###########################################################

    function vat_menu3($args)
    {
        global $_lib;
        $name = $this->MakeName($args);

        if(isset($args['BuyMeny']) && isset($args['SaleMenu']))
            $query = $this->_QUERY['form']['vatmenu'];
        elseif(isset($args['BuyMenu']))
            $query = $this->_QUERY['form']['vatBuymenu'];
        elseif(isset($args['SaleMenu']))
            $query = $this->_QUERY['form']['vatSalesMenu'];
        else
            $query = $this->_QUERY['form']['vatmenu'];

        if($args['date']) {
            $date = $args['date'];
        } else {
            $date = $_lib['sess']->get_session('LoginFormDate');
        }

        $query .= "and '" . $date . "' >= ValidFrom and '" . $date . "' <= ValidTo and Active=1 order by VatID";
        #print "$query<br>\n";
        $result = $_lib['db']->db_query($query);

        $element = "<select name=\"$name\" tabindex=\"$args[tabindex]\">\n";
        $element = " $element <option value=\"0.00\">Ikke valgt";
        while($_row = $_lib['db']->db_fetch_object($result))
        {
            if($_row->VatID == 10)
            {
              if ($args['value'] == 10)
                $element .= "<option value=\"10\" selected>Salg\n";
              else
                $element .= "<option value=\"10\">Salg\n";
            }
            elseif($_row->VatID == 40)
            {
              if ($args['value'] == 40)
                  $element .= "<option value=\"40\" selected>Kj&oslash;p\n";
              else
                  $element .= "<option value=\"40\">Kj&oslash;p\n";
            }

            if($_row->VatID == 30)
            {
              if ($args['value'] == 30)
                $element .= "<option value=\"30\" selected>30 - Salg avgiftsfritt\n";
              else
                $element .= "<option value=\"30\">30 - Salg avgiftsfritt\n";
            }
            elseif($_row->VatID == 32)
            {
              if ($args['value'] == 32)
                  $element .= "<option value=\"32\" selected>32 - Salg utenfor avgiftsomr&aring;de\n";
              else
                  $element .= "<option value=\"32\">32 - Salg utenfor avgiftsomr&aring;de\n";
            }
            elseif($_row->VatID == 62)
            {
              if ($args['value'] == 62)
                  $element .= "<option value=\"62\" selected>62 - Kj&oslash;p utenfor avgiftsomr&aring;de\n";
              else
                  $element .= "<option value=\"62\">62 - Kj&oslash;p utenfor avgiftsomr&aring;de\n";
            }
            elseif($_row->VatID == 60)
            {
              if ($args['value'] == 60)
                  $element .= "<option value=\"60\" selected>60 - Kj&oslash;p avgiftsfritt\n";
              else
                  $element .= "<option value=\"60\">60 - Kj&oslash;p avgiftsfritt\n";
            }
            elseif($args['percent']==1)
            {
                if(($_row->Percent/100) == $args['value'])
                    $element .= "<option value=\"".($_row->Percent/100)."\" selected>$_row->VatID - ".$_lib['format']->Percent($_row->Percent)."\n";
                else
                    $element .= "<option value=\"".($_row->Percent/100)."\">$_row->VatID - ".$_lib['format']->Percent($_row->Percent)."\n";
            }
            elseif($args['percent2']==1)
            {
                if(($_row->Percent) == $args['value'])
                    $element .= "<option value=\"".($_row->Percent)."\" selected>$_row->VatID - ".$_lib['format']->Percent($_row->Percent)."\n";
                else
                    $element .= "<option value=\"".($_row->Percent)."\">$_row->VatID - ".$_lib['format']->Percent($_row->Percent)."\n";
            }
            elseif($args['vatid']==1)
            {
                if(($_row->VatID) == $args['value'])
                    $element .= "<option value=\"".($_row->VatID)."\" selected>$_row->VatID - ".$_lib['format']->Percent($_row->Percent)."\n";
                else
                    $element .= "<option value=\"".($_row->VatID)."\">$_row->VatID - ".$_lib['format']->Percent($_row->Percent)."\n";
            }
        }

        $element = " $element </select>\n";
        #$_lib['db']->db_free_result($result);

        return $element;
    }

###########################################################

    function URL($args)
    {
        //print_r($args);
        if($args['historyLink'] == 1)
        {
            if(!isset($args['historyValue']))
            {
                $args['historyValue'] = -1;
            }
            $args['url'] = "JavaScript:history.go(".$args['historyValue'].")";
        }
        $element = "<a href=\"".$args['url']."\"";
        if($args['accesskey'])
        {
            $element .= " accesskey=\"".$args['accesskey']."\" ";
        }
        if($args['class'])
        {
            $element .= " class=\"".$args['class']."\" ";
        }
        if($args['target'])
        {
            $element .= " target=\"".$args['target']."\" ";
        }
        if($args['title'])
        {
            $element .= " title=\"".$args['title']."\" ";
        }
        $element .= ">".$args['description']."</a>";
        return $element;
    }

###########################################################

    function show($args) {
      return $args['value'];
    }

###########################################################

    #args input: , $args[table, $args[field, $args[value, $args[tabindex, $args[accesskey, $args[pk, $num_letters, company_id
    function project_menu($args)
    {
        global $_lib;
        
        $name = $this->MakeName($args);

        if($args['company_id'])
        {
            $query = "select ProjectID, Heading from project where Active='1' and CompanyID='$args[company_id]' order by ProjectID";
        }
        else
        {
            $query = "select ProjectID, Heading from project where Active='1' order by ProjectID";
        }

        $result = $_lib['db']->db_query($query);

        if(!$args['num_letters'])
        {
            $num_letters = '20';
        }
        else
        {
            $num_letters = $args['num_letters'];
        } #Default number of letters in menu

        #print "Ant bokst: $num_letters<br>";

        $element = "<select name=\"$name\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";

        if($conf['value'])
        {
            $element = " $element <option value=\"\"/>" . substr("Finnes ikke: . $conf[value]",0, $num_letters);
        }
        else
        {
            $element = " $element <option value=\"\"/>" . substr('Velg prosjekt',0, $num_letters);
        }

        while($_row = $_lib['db']->db_fetch_object($result))
        {
          if($_row->ProjectID == $args[value])
              $element = " $element <option value=\"$_row->ProjectID\" selected>$_row->ProjectID - " . substr($_row->Heading,0,$num_letters) . "\n";
          else
              $element = " $element <option value=\"$_row->ProjectID\">$_row->ProjectID - " . substr($_row->Heading,0,$num_letters) . "\n";
        }

        $element = " $element </select>\n";
        #$_lib['db']->db_free_result($result);

        return $element;
    }

	###########################################################
	#$args[type][] = hovedbok, reskontro, employee, balance, result, customer, supplier, hovedbokwreskontro (bare list hovedbokskontoer med reskontro), hovedbokwemployee (list hovedbok og ansatt i menyen)
    function accountplan_number_menu($args) {
        //print_r($args);
        global $_lib;
        $name = $this->MakeName($args);

		$colorH = array(
			'balance'  => '#0FF000',
			'result'   => '#0FFFF0',
			'employee' => '#CCCCCC',
			'supplier' => '#FFF000',
			'customer' => '#00FF00'
		);

		$where = '';

		if(count($args['type'])) {
			#print_r($args['type']);
			foreach($args['type'] as $tmp => $type) {
	
				#print "type: $type<br>\n";
	
				if($type == 'hovedbok') {
					$where .= " ((AccountPlanType='balance' or AccountPlanType='result') and EnableReskontro = 0) or ";
				} elseif($type == 'hovedbokwreskontro') {
					$where .= " ((AccountPlanType='balance' or AccountPlanType='result') and EnableReskontro = 1) or ";
				} elseif($type == 'hovedbokwemployee') {
					$where .= " ((AccountPlanType='balance' or AccountPlanType='result' or AccountPlanType='employee') and EnableReskontro = 0) or ";
				} elseif($type == 'reskontro') {
					$where .= " (AccountPlanType != 'balance' and AccountPlanType != 'result') or ";
				} elseif($type) {
					$where .= " AccountPlanType='$type' or ";
				} else {
					print "No type argument supplied to accountplan_number_menu";
				}
			}

			$where = substr($where, 0, -4); #remove the last or
		} else {
			print "No type argument supplied to accountplan_number_menu";
		}

        $query = "select AccountPlanID, AccountName, AccountPlanType from accountplan where Active=1 and ($where) order by AccountPlanID";
        #print "$query<br>";
        $result = $_lib['db']->db_query($query);

        if(!$args['num_letters'])
        {
            $num_letters = '30';
        }
        else
        {
            $num_letters = $args['num_letters'];
        } #Default number of letters in menu

        if($args['autosubmit'])
        {
          $element .= " onchange=\"submit();\" ";
        }
        $element .= ">\n";


        if($args['required'] == false)
        {
            $element .= "<option value=\"\">" . substr('Velg konto',0, $num_letters) . "</option>";
        }

        while($_row = $_lib['db']->db_fetch_object($result))
        {

			#$optioncolor = "class=\"$_row->AccountPlanType\""; 
			$optioncolor = " style=\"background: " . $colorH[$_row->AccountPlanType] . "\"";
			if($_row->AccountPlanID == $args['value']) {
				$element .= " <option style=\"background: #CCCCCC\" value=\"$_row->AccountPlanID\" selected>" . substr("$_row->AccountPlanID-$_row->AccountName",0,$num_letters) . " (" . substr($_row->AccountPlanType,0,1) . ")</option>\n";
				$found = true;
				$selectedcolor = $optioncolor;
			}
			else
				$element .= " <option $optioncolor value=\"$_row->AccountPlanID\">" . substr("$_row->AccountPlanID-$_row->AccountName",0,$num_letters) . " (" . substr($_row->AccountPlanType,0,1) . ")</option>\n";
        }

        $query = "select AccountPlanID, AccountName, AccountPlanType from accountplan where Active=1 and ($where) order by AccountName";
        $result = $_lib['db']->db_query($query);

        while($_row = $_lib['db']->db_fetch_object($result))
        {
			#$optioncolor = "class=\"$_row->AccountPlanType\""; 
			$optioncolor = " style=\"background: " . $colorH[$_row->AccountPlanType] . "\"";

            $element .= "<option $optioncolor value=\"$_row->AccountPlanID\">" . substr("$_row->AccountName-$_row->AccountPlanID",0,$num_letters) . " (" . substr($_row->AccountPlanType,0,1) . ")</option>\n";
        }

        if(!$found && isset($args['value']) && $args['value'] > 0)
        {
			$optioncolor = " style=\"background: #FF0000\"";
            $element .= "<option $optioncolor class=\"$_row->AccountPlanType\" value=\"" . $args['value'] . "\" selected>" . substr("Konto finnes ikke: " . $args['value'],0, $num_letters) . "</option>";
			$selectedcolor = $optioncolor;
        }
        elseif(!$found && isset($args['value']))
        {
			$optioncolor = " style=\"background: #FFFFFF\"";
            $element .= "<option $optioncolor class=\"$_row->AccountPlanType\" value=\"" . $args['value'] . "\" selected>" . substr("Velg konto",0, $num_letters) . "</option>";
			$selectedcolor = $optioncolor;
        }

        $element .= "</select>\n";

        $element = "<select $selectedcolor name=\"".$name."\" tabindex=\"".$args['tabindex']."\" accesskey=\"".$args['accesskey']."\" class=\"".$args['class']."\"" . $element;

        #$_lib['db']->db_free_result($result);
        return $element;
    }

    function accountplan_number_menu3($args) {
        global $_lib;
        $name = $this->MakeName($args);

		$colorH = array(
			'balance'  => '#0FF000',
			'result'   => '#0FFFF0',
			'employee' => '#CCCCCC',
			'supplier' => '#FFF000',
			'customer' => '#00FF00'
		);

		$where = '';

		if(count($args['type'])) {
			#print_r($args['type']);
			foreach($args['type'] as $tmp => $type) {
	
				#print "type: $type<br>\n";
	
				if($type == 'hovedbok') {
					$where .= " ((AccountPlanType='balance' or AccountPlanType='result') and EnableReskontro = 0) or ";
				} elseif($type == 'hovedbokwreskontro') {
					$where .= " ((AccountPlanType='balance' or AccountPlanType='result') and EnableReskontro = 1) or ";
				} elseif($type == 'hovedbokwemployee') {
					$where .= " ((AccountPlanType='balance' or AccountPlanType='result' or AccountPlanType='employee') and EnableReskontro = 0) or ";
				} elseif($type == 'reskontro') {
					$where .= " (AccountPlanType != 'balance' and AccountPlanType != 'result') or ";
				} elseif($type) {
					$where .= " AccountPlanType='$type' or ";
				} else {
					print "No type argument supplied to accountplan_number_menu";
				}
			}

			$where = substr($where, 0, -4); #remove the last or
		} else {
			print "No type argument supplied to accountplan_number_menu";
		}

        $query = "select AccountPlanID, AccountName, AccountPlanType from accountplan where Active=1 and AccountPlanID != '0' and ($where) order by AccountPlanID";
        #print "$query<br>";
        $result = $_lib['db']->db_query($query);

        if(!$args['num_letters'])
        {
            $num_letters = '30';
        }
        else
        {
            $num_letters = $args['num_letters'];
        } //Default number of letters in menu
        
        $element_array = array();
              
        while($_row = $_lib['db']->db_fetch_object($result))
        {

			#$optioncolor = "class=\"$_row->AccountPlanType\""; 
			if($_row->AccountPlanID == $args['value']) {
                            $found = true;
                            $selectedcolor = $optioncolor;

                            $element_array[] = array(
                                $_row->AccountPlanID,
                                $colorH[$_row->AccountPlanType],
                                utf8_encode(substr("$_row->AccountPlanID-$_row->AccountName",0,$num_letters) . " (" . substr($_row->AccountPlanType,0,1) . ")")
                                );
			}
			else {
                            $element_array[] = array(
                                $_row->AccountPlanID,
                                $colorH[$_row->AccountPlanType], 
                                utf8_encode(substr("$_row->AccountPlanID-$_row->AccountName",0,$num_letters) . " (" . substr($_row->AccountPlanType,0,1) . ")")
                                ); 
                        }
        }

        $query = "select AccountPlanID, AccountName, AccountPlanType from accountplan where Active=1 and AccountPlanID != '0' and ($where) order by AccountName";
        $result = $_lib['db']->db_query($query);

        while($_row = $_lib['db']->db_fetch_object($result))
        {
                        $element_array[] = array(
                            $_row->AccountPlanID,
                            $colorH[$_row->AccountPlanType],
                            utf8_encode(substr("$_row->AccountName-$_row->AccountPlanID",0,$num_letters) . " (" . substr($_row->AccountPlanType,0,1) .")")
                            );
        }

        if(!$found && isset($args['value']) && $args['value'] > 0)
        {
            $element_array[] = array(
                $_row->AccountPlanID,
                "#FF0000",
                utf8_encode(substr("Konto finnes ikke: " . $args['value'],0, $num_letters))
                );
        }
        elseif(!$found && isset($args['value']))
        {
            $element_array[] = array(
                $_row->AccountPlanID,
                "#FFFFFF",
                utf8_encode(substr("Velg konto",0, $num_letters))
                );
        }

        return $element_array;
    }

###########################################################

    #input: width, maxwidth, width_sub, width_add
    function _width($args)
    {
        if(isset($args['width']) and isset($args['maxwidth']) and  $args['width'] > $args['maxwidth'] and $args['maxwidth'] > 0)
        {
          $width = $args['maxwidth'];
        }
        elseif(isset($args['width']))
        {
          $width = $args['width'];
        }
        else
        {
          $width = 20;
        }

        #print "$width - $args[width_sub] + $args[width_add]<br>";
        return $width - $args['width_sub'] + $args['width_add'];
    }

###########################################################

    function _height($args) {
      $char = strlen($args['value']);

      if($args['width'] > 0) {
        $height = (int) ($char / $args['width']) * 3;
      }
      #print "height: $height, bredde: $args[width], tegn: $char<br>";
      if(isset($args['height']) and $args['height'] > 0) {
        #print "retur height definert\n";
        return $args['height'];
      }
      elseif($char <= 0) {
        #print "retur ingen tegn: default 12 height\n";
        return 12; #Default height
      }
      elseIf($height > $args['max_height'] and $args['max_height'] > 0) {
        #print "retur max_height\n";
        #return $args['max_height'];
      } elseif($height < $args['min_height'] and $args['min_height'] > 0) {
        #print "retur height to low\n";
        return $args['min_height'];
      } elseif($height < $args['min_height']) {
        #print "retur min_height\n";
        return $args['min_height'];
      } else {
        #print "retur height: $height<br>\n";
        return $height;
      }
    }

    ############################################################################
    function start($args)
    {
        global $_lib;
        $element = "<form action=\"".$args['action']."\" ";
        if(isset($args['name']))
        {
            $this->_name = $args['name'];
        }

        if(isset($args['target']))
        {
            $element .= "target=\"" . $args['target'] ."\" ";
        }

        if($this->_name)
        {
            $element .= "name=\"$this->_name\" ";
        }

        if(!isset($args['method']))
        {
            $args['method'] = 'post';
        }

        if(isset($args['onsubmit']))
        {
            $element .= "onsubmit=\"".$args['onsubmit']."\" ";
        }

        $element .= "method=\"".$args['method']."\" enctype=\"multipart/form-data\">\n";

        //$max_filesize = ((int) ini_get(upload_max_filesize)) * 1024 * 1024;
        //$element .= $this->hidden(array('name' => 'MAX_FILE_SIZE', 'value' => $max_filesize));

        return $element;
    }

    /***************************************************************************
    * Only show field in edit modus
    * Otional sentences with descriptions of function
    * @param Define input parameters
    * @return Define return og function
    */    #Stop form tag
    #
    function stop($args)
    {
        //if(($args['inline'] == 'edit' and $args['type'] != 'static') or ($args['inline'] != 'edit' and $args['type'] == 'static') || !count($args))
        //{
            $element = "</form>\n";
        //}
        return $element;
    }

    ############################################################################
    #Input: url, name
    #function button($args){
    #    return "<button type=\"button\" onclick=\"location.href='" . $args['url'] . "'\">" . $args['name'] . "</button>";
    #}

    #Input: url, name
    function button($args)
    {
        global $_lib;

		if(isset($args['confirm'])) {
			$onclick .= "if(confirm('" . $args['confirm'] . "')) {location.href='".$args['url']."';}";
		}
		elseif(strlen($args['OnClick']) > 0) {
			$onclick .= "if(" . $args['OnClick'] . " == 1){location.href='".$args['url']."';}";
		}
		elseif(strlen($args['target']) > 0) {
			$onclick .= "window.open('".$args['url']."')";
		}
		else {
			$onclick .= "location.href='".$args['url']."'";
		}

		if($args['class'] == 'greybox') {
			$onclick .= "return GB_show('NAVN', '" . $args['url'] . "', 480, 600);";
		}

		//return "<button type=\"button\" onclick=\"".$onclick."location.href='".$args['url']."'\">".$args['name']."</button>"; //endret pga onclick=javascript::confirm();

		$element = "<button type=\"button\" onclick=\"".$onclick . ";\" class=\"".$args['class']."\">".$args['name']."</button>";
		return $element;
    }

}
?>
