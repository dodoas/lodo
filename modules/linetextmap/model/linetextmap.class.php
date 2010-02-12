<?
class linetextmap
{
    var $_query;

    var $_LineTextMapID;
    var $_LanguageID;
    var $_Line;
    var $_ReportID;
    var $_Text;
    var $_return;

    function linetextmap($args)
    {
        #Init
        $this->Init($args);
    }

    ##############################################################
    #Hente tekst ut i fra id
    function Init($args)
    {
        if(strlen($args['LineTextMapID']) > 0)
        {
            $this->_LineTextMapID = $args['LineTextMapID'];
        }

        if(strlen($args['LanguageID']) > 0)
        {
            $this->_LanguageID = $args['LanguageID'];
        }
        else
        {
            $this->_LanguageID = 'no';
        }

        if(strlen($args['Line']) > 0)
        {
            $this->_Line = $args['Line'];
        }
        if(strlen($args['ReportID']) > 0)
        {
            $this->_ReportID = $args['ReportID'];
        }

        if($args['return'] == 'hash')
        {
            $this->_return = $args['return'];
        }
        else
        {
            $this->_return = 'value';
        }

        if(strlen($args['Text']) > 0)
        {
            $this->_Text = $args['Text'];
        }
    }

    ##############################################################
    #Hente tekst ut i fra linjenr og språk
    function getTextFromLineNr($args)
    {
        global $_lib;

        $this->Init($args);

        if(strlen($this->_Line) > 0)
        {
	        if(strlen($this->_ReportID) > 0)
     	       $this->_query = "select * from linetextmap where ReportID = " . $this->_ReportID . " and Line='$this->_Line' and LanguageID='$this->_LanguageID'";
     	    else
        	    $this->_query = "select * from linetextmap where Line='$this->_Line' and LanguageID='$this->_LanguageID'";

            if($this->_return == 'hash')
            {
                $returnhash = $_lib['db']->get_hashhash(array('query' => $this->_query, 'key' => 'Line'));
                return $returnhash;
            }
            else
            {
                $returnvalue = $_lib['storage']->get_row(array('query' => $this->_query));
                return $returnvalue->Text;
            }
        }
        else
        {
            $message = "FEIL: Mangler Linenr";
            print $message;
            return array('message'=>$message);
        }
    }

    ##############################################################
    #Hente tekst ut i fra id
    function getTextFromLineID($args)
    {
        global $_lib;

        $this->Init($args);

        if(strlen($this->_LineTextMapID) > 0)
        {
            $this->_query = "select * from linetextmap where LineTextMapID='$this->_LineTextMapID'";

            if($this->_return == 'hash')
            {
                $returnhash = $_lib['db']->get_hashhash(array('query' => $this->_query, 'key' => 'LineTextMapID'));
                return $returnhash;
            }
            else
            {
                $returnvalue = $_lib['db']->get_row(array('query' => $this->_query));
                return $returnvalue->Text;
            }
        }
        else
        {
            $message = "FEIL: Mangler LineTextMapID";
            print $message;
            return array('message'=>$message);
        }
    }

    ##############################################################
    #Hente linjenr ut i fra tekst
    function getLineNrFromTekst($args)
    {
        global $_lib;

        $this->Init($args);

        if(strlen($this->_LineTextMapID) > 0)
        {
            $this->_query = "select * from linetextmap where Text='$this->_Text'";

            if($this->_return == 'hash')
            {
                $returnhash = $_lib['db']->get_hashhash(array('query' => $this->_query, 'key' => 'Text'));
                return $returnhash;
            }
            else
            {
                $returnvalue = $_lib['db']->get_row(array('query' => $this->_query));
                return $returnvalue->Line;
            }
        }
        else
        {
            $message = "FEIL: Mangler LineTextMapID";
            print $message;
            return array('message'=>$message);
        }
    }
}
?>
