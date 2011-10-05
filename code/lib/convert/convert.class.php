<?
#FUnctions to get data correctly into the database
#All functions in the convert library shoudl as a minimum return the hash, som like image and file returns an extra hash with further fieldnames for auto use.
#return array('value' => $amount, 'error' => $error);
#all input shoukld be a hash with minimum: value set, Image needs name set to.

class convert {
  public $_dsn;
  public $_dbh;

    function __construct($args)
    {
        $this->_dsn = $args['_dsn'];
        $this->_dbh = $args['_dbh'];
    }

    /***************************************************************************
    * Array handling
    * @param $value or array('value' => xxxx)
    * @return $value or array
    */
    function CheckInput($args)
    {
        global $_lib;

        if(!is_array($args))
        {
            $args = array('value'=>$args, 'return'=>'value');
        }
        $args['value_org']  = $args['value']; #This should only be used by one function. StringHtml
        #Det kan hende denne tar litt mye. Jmfr tillatt html input.
        $args['value']      = strip_tags($args['value']); #Strip all tags. DOes not strip hex encoded tags?
        #FIX: Strip hex encoded tags to
        $args['value']      = $_lib['db']->db_escape($args['value']); #Remove all database attacs

        return $args;
    }

    /***************************************************************************
    * Array handling
    * @param $value or array('value' => xxxx)
    * @return $value or array
    */
    function CheckOutput($args)
    {
        if($args['return'] == 'value')
        {
            #print "retur  her ja<br>";
            return $args['value'];
        }
        else
        {
            return $args;
        }
 	}

  	function Amount($args) {
  
        $args = $this->CheckInput($args);

		$args['value'] = str_replace(' ','', $args['value']);
		$args['value'] = str_replace(chr(160), '', $args['value']);
		$args['value'] = str_replace(',', '.', $args['value']);
		$args['value'] = str_replace('|', '.', $args['value']); #Character bug in forefox/mozilla on Linux where pipe is comma. weird.
   
		#$args['value'] = round($args['value'], 2); #Removed by Arnt 2005-03-10
   
		if(is_numeric($args['value'])) {
   
        } elseif(strlen($args['value']) == 0) { #Empty string will be translated to 0 value
            $args['value'] = 0;
		} else {
		   	$args['error'] = "This is not amount: #" . $args['value'] . "#";
		}
		return $this->CheckOutput($args);
	}

    #Support the dollowing date form,ats
    # YYYY-MM-DD (10C)
    # DD-MONTH-YY/YY[2/4] (>= 10C)
    # +D (1C) (sets current mont and year automatically
    # +DD (2C)(sets current mont and year automatically
    # +DDMM (4C) (Sets current year automatically)
    # +DDMMYY (6C)
    # +DDMMYYYY (8C)
    # DD.MM.YY (8C)
    # DD.MM.YYYY (10C)
    # DD.MONTH.YY/YY[2/4]
    #Conmverts all these formats to iso
    function Date($args)
    {
        global $_lib;

        $args = $this->CheckInput($args);

        $date = $args['value'];
        //print "START: $date<br>";
        if(!$date)
        {
            return $this->CheckOutput(array('value'=>'0000-00-00', 'error'=>'', 'return' => $args['return']));
        }
        elseif($date == '00  0000')
        {
            return $this->CheckOutput(array('value' => '0000-00-00', 'error' => "Ikke gyldig datoformat: $date", 'return' => $args['return']));
        }

        if(is_numeric($date))
        {
            //print "Talldato: $date - ".strlen($date)."<br>";
            #All dates only containing numbers
            if(strlen(strlen($date) == 1))
            {
                #Only day
                $date = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'))."-0$date";
            }
            elseif(strlen($date) == 2)
            {
                #2 character date
                $date = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'))."-$date";
            }
            elseif(strlen($date) == 4)
            {
                #4 character date
                preg_match('{(\d\d)(\d\d)}', $date, $m); #Find the pk value  (text or int)
                $date = $_lib['date']->get_this_year($_lib['sess']->get_session('Date'))."-$m[2]-$m[1]";
            }
            elseif(strlen($date) == 6)
            {
                //print "6 character date";
                preg_match('{(\d\d)(\d\d)(\d\d)}', $date, $m); #Find the pk value  (text or int)
                $date = substr($_lib['date']->get_this_year($_lib['sess']->get_session('Date')), 0, 2)."$m[3]-$m[2]-$m[1]";
            }
            elseif(strlen($date) == 8)
            {
                preg_match('{(\d\d)(\d\d)(\d\d\d\d)}', $date, $m); #Find the pk value  (text or int)
                $date = "$m[3]-$m[2]-$m[1]";
            }
            else
            {
                $args['error'] = "Unsupported dateformat: $date";
            }
        }
        else
        {
            #All dates containing more than numbers
            #These 3 methods are the same, replace by one common
            #Check if it contains dot (.) or (-)
            #print "ikketall: $date<br>";

            if(preg_match('{(\d*)\.(\w*)\.(\d*)}', $date, $m))
            {
                #print "Contains dot as separator<br>";
                if($m[2] and !is_numeric($m[2]))
                {
                    $m[2] = $this->TextToMonth(array('value'=>$m[2]));
                    $m[2] = $m[2]['value'];
                    if(!$m[2])
                    {
                        $args['error'] = "Unsupported dateformat: $date";
                    }
                }

                if(strlen($m[3]) == 2)
                {
                    $date = "20$m[3]-$m[2]-$m[1]";
                }
                elseif(strlen($m[3]) == 4)
                {
                    $date = "$m[3]-$m[2]-$m[1]";
                }
                else
                {
                    $args['error'] = "Unsupported dateformat: $date";
                }

            }
            elseif(preg_match('{(\d*)-(\w*)-(\d*)}', $date, $m))
            {
                #print "- separated : $date<br>";
                #Contains - as separator
                if($m[2] and !is_numeric($m[2]))
                {
                    #print "Ikke numerisk måned<br>";
                    $m[2] = $this->TextToMonth(array('value'=>$m[2]));
                    $m[2] = $m[2]['value'];
                    if(!$m[2])
                    {
                        $args['error'] = "Unsupported dateformat: $date";
                    }
                }

                if(strlen($m[1]) == 4)
                {
                    # Format YYYY-MM-DD
                    #print "ISO<bR>";
                    $date = "$m[1]-$m[2]-$m[3]";
                }
                elseif(strlen($m[3]) == 4)
                {
                    #print "4 tegn i år<br>";
                    $date = "$m[3]-$m[2]-$m[1]";
                }
                elseif(strlen($m[3]) == 2)
                {
                    #print "2 tegn i år<br>";
                    $date = "20$m[3]-$m[2]-$m[1]";
                }
                else
                {
                    #print "Unsupported dateformat: $date<br>";
                    $args['error'] = "Unsupported dateformat: $date";
                }
            }
            elseif(preg_match('{(\d{2}) (\w*) (\d{4})}', $date, $m))
            {
                #Contains blank as separator
                #print "Blank separator<br>";
                if($m[2] and !is_numeric($m[2]))
                {
                    #print "m2: $m[2]<br>";
                    $m[2] = $this->TextToMonth(array('value'=>$m[2]));
                    $m[2] = $m[2]['value'];
                    #print "m2: $m[2]<br>";
                    if(!$m[2])
                    {
                        #print "Unsupported dateformat: $date, $m[2] not convertible<br>";
                        $args['error'] = "Unsupported dateformat: $date, $m[2] not convertible";
                    }
                }

                if(strlen($m[3]) == 2)
                {
                    #print "2 tegn år<br>";
                    $date = "20$m[3]-$m[2]-$m[1]";
                }
                elseif(strlen($m[3]) == 4)
                {
                    #print "4 tegn år<br>";
                    $date = "$m[3]-$m[2]-$m[1]";
                }
                else
                {
                    $args['error'] = "Unsupported dateformat: $date";
                    #print "$args['error']<br>\n";
                }
            }
            elseif(preg_match('{(\d{4}) (\w*) (\d{2})}', $date, $m))
            {
                #Contains blank as separator
                #print "Blank separator<br>";
                if($m[2] and !is_numeric($m[2]))
                {
                    #print "m2: $m[2]<br>";
                    $m[2] = $this->TextToMonth(array('value'=>$m[2]));
                    $m[2] = $m[2]['value'];
                    #print "m2: $m[2]<br>";
                    if(!$m[2])
                    {
                        #print "Unsupported dateformat: $date, $m[2] not convertible<br>";
                        $args['error'] = "Unsupported dateformat: $date, $m[2] not convertible";
                    }
                }

                if(strlen($m[3]) == 2)
                {
                    #print "2 tegn år<br>";
                    $date = "$m[1]-$m[2]-$m[3]";
                }
                elseif(strlen($m[3]) == 4)
                {
                    #print "4 tegn år<br>";
                    $date = "$m[3]-$m[2]-$m[1]";
                }
                else
                {
                    $args['error'] = "Unsupported dateformat: $date";
                    #print "$args['error']<br>\n";
                }
            }
            elseif(preg_match('{(\d*)\/(\w*)\/(\d*)}', $date, $m) or preg_match('{(\d*)\/(\w*)-(\d*)}', $date, $m))
            {
                #Contains blank as separator
                #print "Blank separator<br>";
                if($m[2] and !is_numeric($m[2]))
                {
                    #print "m2: $m[2]<br>";
                    $m[2] = $this->TextToMonth(array('value'=>$m[2]));
                    $m[2] = $m[2]['value'];
                    #print "m2: $m[2]<br>";
                    if(!$m[2])
                    {
                        #print "Unsupported dateformat: $date, $m[2] not convertible<br>";
                        $args['error'] = "Unsupported dateformat: $date, $m[2] not convertible";
                    }
                }

                if(strlen($m[3]) == 2)
                {
                    #print "2 tegn Âr<br>";
                    $date = "$m[3]-$m[2]-$m[1]";
                }
                elseif(strlen($m[3]) == 4)
                {
                    #print "4 tegn Âr<br>";
                    $date = "$m[3]-$m[2]-$m[1]";
                }
                else
                {
                    $args['error'] = "Unsupported dateformat: $date";
                    #print "$args['error']<br>\n";
                }
            }
            else
            {
                $args['error'] = "Unsupported dateformat: $date";
                #print "$args['error']<br>\n";
            }
        }
        #Does nothing yet
        //print "date: ' $date '<br>";
        if(!$args['error'])
        {
            if(preg_match('{(\d{4})-(\d{2})-(\d{2})}', $date, $m))
            {
                if($m[2] <= 0 or $m[2] > 12)
                {
                    #Month not valid
                    $args['error'] .= "M&aring;ned ikke gyldig: $m[2]<br />";
                }
                if($m[3] <= 0 or $m[3] > 31)
                {
                    #Day not valid
                    $args['error'] .= "Dag er ikke gyldig: $m[3]<br \>";
                }
            }
            elseif(preg_match('{(\d{2})-(\d{2})-(\d{2})}', $date, $m))
            {
            }
            else
            {
                $args['error'] .= "Unbable to format correctly<br \>";
            }
        }
        $args['value'] = $date;
        return $this->CheckOutput($args);
    }

  function Datetime($args)
  {
    $datetime = $args['value'];
    #print "Datetime input: $datetime<br>";
    if(preg_match('{(.*) (\d\d:\d\d:\d\d)?$}', $datetime, $m))
    {
      #Find the pk value  (text or int)
      $date  = $this->Date(array('value' => $m[1]));
      $m[1]  = $date['value'];
      $error = $date['error'];
    }
    else
    {
       #Probably only a date field
       $date = $this->Date(array('value' => $datetime));
       $m[1]  = $date['value'];
       $error = $date['error'];
       #print "Trying to convert this datetime: #$datetime##, m1: $m[1], m2: $m[2]";
       $m[2] = '00:00:00';
    }
  #print "Datetime output: #$m[1]#$m[2]#<br>";
  return array('value' => "$m[1] $m[2]", 'error' => $error);
  }

  function Time($args) {
     $time = $args['value'];
      #Does nothing yet
      return array('value' => $time, 'error' => $error);
  }

 function Int($args) {
   $int = $args['value'];
      if(!$int) {
        $int = 0;
      }

      if(is_numeric($int)) {

      } else {
        $error = "This is not a valid int";
      }
      return array('value' => $int, 'error' => $error);
 }

 function String($args) {
   $string = $args['value'];
      #Does nothing yet
      if(is_string($string)) {

      } else {
        $error = "This is not a valid string";
      }

      return array('value' => $string, 'error' => $error);
  }

  function StringPlain($args) {
    $string = $args['value'];
    return array('value' => $string, 'error' => $error);
    #No html allowed only plain, safe text
  }

  function StringHtml($args) {
    $string = $args['value'];
    return array('value' => strip_tags($string), 'error' => $error);
    #No html allowed only plain, safe text
  }

  function StringSimpleHTML($args) {
    $string = $args['value'];
    return array('value' => strip_tags($string), 'error' => $error);
    #allow html codes that is not dangerous
  }

 function URL($args) {
   $url = $args['value'];
      #Does nothing yet
      if(preg_match("/http:\/\//", $url)){

      } else {
        $error = "This is not valid URL";
      }

      return array('value' => $url, 'error' => $error);
  }

   function Country($args) {
     $code = $args['value'];
      #Does nothing yet
      if(preg_match("/@/", $code)){

      } else {
        $error = "This is not a valid country code";
      }
      return array('value' => $code, 'error' => $error);
  }

   function CountryCode($args) {
     $code = $args['value'];
      #Does nothing yet
      if(preg_match("/[A-Z][A-Z]/", $code)){

      } else {
        # $error = "This is not a valid country code";
      }
      return array('value' => $code, 'error' => $error);
  }

   function Phone($args) {
     $phone = $args['value'];
      #Does nothing yet
      #if(preg_match("/@/", $email)){

      #} else {
      #  $error = "This is not a valid email adress";
      #}
      return array('value' => $phone, 'error' => $error);
  }

   function Email($args) {
     $email = $args['value'];
      #Does nothing yet
      if(preg_match("/@/", $email)){

      } else {
        $error = "This is not a valid email adress";
      }
      return array('value' => $email, 'error' => $error);
  }

 function ZipCode($args) {
   $zipcode = $args['value'];
      #Does nothing yet
      if(is_numeric($zipcode)) {
        if($zipcode > 1000 and zipcode < 10000) {

        } else {
          $error = "This is not a valid ZipCode";
        }
      } else {
        $error = "This is not a valid ZipCode";
      }

      return array('value' => $zipcode, 'error' => $error);
  }

    function TextToMonth($args)
    {
        $mndNr = $args['value'];
        $mnd = array();
        $mnd['Januar']     = '01';
        $mnd['Februar']    = '02';
        $mnd['Mars']       = '03';
        $mnd['April']      = '04';
        $mnd['Mai']        = '05';
        $mnd['Juni']       = '06';
        $mnd['Juli']       = '07';
        $mnd['August']     = '08';
        $mnd['September']  = '09';
        $mnd['Oktober']    = '10';
        $mnd['November']   = '11';
        $mnd['Desember']   = '12';
        $mnd['jan']        = '01';
        $mnd['feb']        = '02';
        $mnd['mar']        = '03';
        $mnd['apr']        = '04';
        $mnd['mai']        = '05';
        $mnd['jun']        = '06';
        $mnd['jul']        = '07';
        $mnd['aug']        = '08';
        $mnd['sep']        = '09';
        $mnd['okt']        = '10';
        $mnd['nov']        = '11';
        $mnd['des']        = '12';

        return array('value' => $mnd[$mndNr], 'error' => $error);
    }

    #name = full form name, table=tablename, field=fieldname, pk=primary key of table
    function File($args)
    {
        global $_SETUP, $_sess, $_image;

        if(strlen($args['name']) == 0 and $_FILES[$args['name']]['size'] == 0)
        {
            return false;
        }
        else
        {
            $error = $_FILES[$args['name']]['error'];
            if(isset($_FILES[$args['name']]['name']) and !$error) #Check if we have been uploading a picture here.
            {
                $_SETUP['DOWNLOAD_DIR'] = $_SETUP['HOME_DIR'].$_SETUP['SLASH']."html".$_SETUP['SLASH']."auto".$_SETUP['SLASH'].$args['table'].$_SETUP['SLASH'];
                if(is_dir($_SETUP['DOWNLOAD_DIR']) != 1)
                {
                    if(!mkdir($_SETUP['DOWNLOAD_DIR']))
                    {
                        $error = "Klarte ikke opprette katalogen" . $_SETUP['DOWNLOAD_DIR'];
                        $_sess->Error($error);
                        print "$error<br>";
                    }
                    #Create catalog if it does not exist
                    #print "Lager katalog: " . $_SETUP['DOWNLOAD_DIR'] . "<br>";
                }
                #print "FilImage2<br>\n";
                $Type = $_FILES[$args['name']]['type']; #Mime type as image/gif
                if(!$args['type'] == 'image' && $Type != 'image/gif' && $Type != 'image/jpeg' && $Type != 'image/png' && $Type != 'image/bmp')
                {
                    #Extra: Maybe this check should lookup extras to se the accepted file types?
                    $error = "Feil filtype, $Type er ikke akseptert. Kun MIME type image/jpeg, image/png, image/gif og image/bmp aksepteres";
                    $Type='';
                }
                else
                {
                    #pk_field.type is name of file uploaded so it is possible with multiple files pr record
                    if($args['type'] == 'image')
                    {
                        $image = GetImageSize($_FILES[$args['name']]['tmp_name']);
                        $filename = $args['pk']."_".$args['field'].".".$_image->getImageType(array('fileType'=>$image[2]));
                        $FileEnd = $_image->getImageType(array('fileType'=>$image[2]));
                    }
                    else
                    {
                        $FileEnd = substr(strrchr($_FILES[$args['name']]['name'], '.'), 1); #We dont know mime types on regular files
                        $filename = $args['pk']."_".$args['field'].".".$FileEnd;
                    }
                    #print "FilImage3: type kode" . $image[2] . "<br>\n";

                    $moveto   = $_SETUP['DOWNLOAD_DIR'].$filename;
                    //print "file: $moveto<br>";

                    $retval   = move_uploaded_file($_FILES[$args['name']]['tmp_name'], $moveto);
                    #print "MOVE: " . $_FILES[$args['name']]['tmp_name'] . " =>  $moveto<br>";
                    if($retval == false)
                    {
                        $error = "Det skjedde en feil under opplasting av denne filen. Pr¯v igjen litt senere.";
                    }
                    else
                    {
                        #Extra: Add support for checking that a image is within scale limts and auto scale the image, or create thumbnail or other things
                        if($args['type'] == 'image')
                        {
                            if($image[0] > 0)
                            {
                                $fields[$args['field'].'Width']  = $image[0];
                            }
                            if($image[1] > 0)
                            {
                                $fields[$args['field'].'Height'] = $image[1];
                            }
                        }

                        $fields[$args['field'].'Size']  = $_FILES[$args['name']]['size'] / 1024; #Size in kbytes of file
                        $fields[$args['field'].'Type']  = $FileEnd;
                        $fields[$args['field'].'Heading']   = $_FILES[$args['name']]['name'];
                    }
                }
            }
            //print_r($fields);
            $fileInfo = array('path'=>$_SETUP['DOWNLOAD_DIR'], 'pk'=>$args['pk'], 'field'=>$args['field'], 'fileEnd'=>$FileEnd);
            return array('value' => $filename, 'fileInfo'=>$fileInfo, 'error' => $error, 'fields' => $fields);
        }
    }

    function File2($args)
    {
        global $_SETUP, $_sess, $_image;

        if($_FILES[$args['name']]['size'] == 0)
        {
            return false;
        }
        else
        {
            $error = $_FILES[$args['name']]['error'];
            if(isset($_FILES[$args['name']]['name']) and !$error) #Check if we have been uploading a picture here.
            {
                if(strlen($_SETUP['MEDIABANK_DIR']) == 0)
                {
                    $error = "$_SETUP[MEDIABANK_DIR] var tom";
                    $_SETUP['MEDIABANK_DIR'] = $_SETUP['HOME_DIR'].$_SETUP['SLASH']."html".$_SETUP['SLASH']."auto".$_SETUP['SLASH']."mediastorage".$_SETUP['SLASH'];
                }

                if(is_dir($_SETUP['MEDIABANK_DIR']) != 1)
                {
                    #Create catalog if it does not exist
                    if(!mkdir($_SETUP['MEDIABANK_DIR']))
                    {
                        $error = "Klarte ikke opprette katalogen" . $_SETUP['MEDIABANK_DIR'];
                        $_sess->Error($error);
                        print "$error<br>";
                    }
                }

                if($args['type'] == 'image')
                {
                    $image = GetImageSize($_FILES[$args['name']]['tmp_name']);
                    $filename = $args['MediaID']."_".$args['pk']."_".$args['field'].".".$_image->getImageType(array('fileType'=>$image[2]));
                    $FileEnd = $_image->getImageType(array('fileType'=>$image[2]));
                }
                else
                {
                    $FileEnd = substr(strrchr($_FILES[$args['name']]['name'], '.'), 1); #We dont know mime types on regular files
                    $filename = $args['MediaID']."_".$args['pk']."_".$args['field'].".".$FileEnd;
                }

                $moveto = $_SETUP['MEDIABANK_DIR'].$filename;
                //print "move tmpfile to: $moveto<br>";

                $retval   = move_uploaded_file($_FILES[$args['name']]['tmp_name'], $moveto);

                if($retval == false)
                {
                    $error = "An error ocoured. Try again later.";
                }
                else
                {
                    #Extra: Add support for checking that a image is within scale limts and auto scale the image, or create thumbnail or other things
                    if($args['type'] == 'image')
                    {
                        if($image[0] > 0)
                        {
                            $fields['Width']  = $image[0];
                        }
                        if($image[1] > 0)
                        {
                            $fields['Height'] = $image[1];
                        }
                    }

                    $fields['Size'] = $_FILES[$args['name']]['size'] / 1024; #Size in kbytes of file
                    $fields['Type'] = $FileEnd;
                }
            }
            $fileInfo = array('path'=>$_SETUP['MEDIABANK_DIR'], 'MediaID'=>$args['MediaID'], 'field'=>$args['field'], 'fileEnd'=>$FileEnd);
            return array('value' => $filename, 'fileInfo'=>$fileInfo, 'error' => $error, 'fields' => $fields);
        }
    }

    function Image($args)
    {
        global $_image;

        if(strlen($args['name']) == 0 and $_FILES[$args['name']]['size'] == 0)
        {
            return 0;
        }
        else
        {
            $args['type'] = 'image';
            $fileinfo = $this->File($args);

            if($args['createThumbnail'] != 1)
            {
                $imageinfo = array();
                $imageinfo['orgFile'] = $fileinfo['fileInfo']['path'].$fileinfo['value'];
                $imageinfo['newFile'] = $fileinfo['fileInfo']['path'].$fileinfo['fileInfo']['pk']."_".$fileinfo['fileInfo']['field']."_thumbnail.".$fileinfo['fileInfo']['fileEnd'];
                $_image->createThumbnail($imageinfo);
            }

            return $fileinfo;
        }
    }

    function Image2($args)
    {
        global $_image, $_media;

        if($_FILES[$args['name']]['size'] == 0)
        {
            return array('delete'=>true);
        }
        else
        {
            $Type = $_FILES[$args['name']]['type'];
            if(!$args['type'] == 'image' && $Type != 'image/gif' && $Type != 'image/jpeg' && $Type != 'image/png' && $Type != 'image/bmp')
            {
                #Extra: Maybe this check should lookup extras to se the accepted file types?
                $error = "Feil filtype, $Type er ikke akseptert. Kun MIME type image/jpeg, image/png, image/gif og image/bmp aksepteres";
                $Type='';
            }
            else
            {
                $args['type'] = 'image';
                $fileinfo = $this->File2($args);

                $_media->setMediaID(array('MediaID'=>$args['MediaID']));

                if($fileinfo != false or 1==1)
                {
                    #vi mÂ legge til bilde i databasen
                    $_media->newOriginalImage($fileinfo);
                }

                return $fileinfo;
            }
        }
    }

    function Media2($args)
    {
        $fileinfo = $this->file2($args);

        return $fileinfo;
    }

    function Media3($args)
    {
        global $_cache, $_input, $_dsn, $_dbh, $_media, $_lib;
        //print_r($args);

        if($_FILES[$args['name']]['size'] == 0)
        {
            return array('value'=>false, 'error'=>'not image', 'delete'=>true);
        }
        else
        {
            if($_REQUEST['table'] == 'media' and $_REQUEST['field'] == 'MediaID')
            {
                #Dette er i fra mediabanken.. vi mÂ bestemme Inputvalidation manuelt
                $type = $_FILES[$args['name']]['type'];
                $type = split('/', $type);
                //print "(".$type['0']." == 'image')";

                if($type['0'] == 'image')
                {
                    $InputValidation = 'image2';
                }
                else
                {
                    $InputValidation = 'file2';
                }
            }
            else
            {
                $validatehash = $_cache->table(array('table' => $_REQUEST['table'], 'field' => $_REQUEST['field']));
                $InputValidation = $validatehash[$_REQUEST['field']]['InputValidation'];
            }

            $args['MediaID'] = $_REQUEST[$_REQUEST['field']];

            if(method_exists($this, $InputValidation))
            {
                $fileinfo = $this->{$InputValidation}($args);
            }
            else
            {
                $fileinfo = $this->file2($args);
            }

            $fields = $fileinfo['fields'];
            $query_update = "update mediastorage set Height='".$fields['Height']."', Width='".$fields['Width']."', Size='".$fields['Size']."', Type='".$fields['Type']."' where MediaStorageID=".$args['pk'];
            //print $query_update;
            $_lib['db']->db_update($query_update);

            return $fileinfo;
        }
    }
}
?>
