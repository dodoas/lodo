<?
# This class is made as a replacement of simplexml where simple html fails to handle complex XML schema defined files with namespaces
# If simplexml works wioth your XML files, use it. If it does not work - then this class will save you.
# It works by converting your XML to a dom object and then recursively iterate the dom tree while making a conventional php obecjt that can be inspected by print_r
# constructor takes: arrayTags - an array with all tags that you always wish to return as an array for simpler procssing later.
# All data in decoded from UTF-8
# All namespaces in tags are removed for easier referencing of the tags later
# Copy given from Empatix framework 2008-09-25 - Thomas Ekdahl - www.empatix.no

class empatix_framework_logic_xmldomtoobject {
    private $debug              = false;
    private $preserveWhiteSpace = false;
    private $formatOutput       = true;
    private $arrayTagH         = array();
    
    ################################################################################################
    #input hash: ['debug' => true/false, 'arrayTag' = $args['arrayTags']] - list of the tags that always should be converted to arrays in return result - even if only one occurence is found.
    public function __construct($args) {
 
        foreach($args as $key => $value) {
            $this->{$key} = $value;        
        }

		if (!isset($args['attributesOfInterest'])) {
			$this->attributesOfInterest = array();
		}
 
        $this->arrayTagH = $args['arrayTags'];

    }
    
    ################################################################################################
    public function convert($xml) {
        $domxml = new DOMDocument();
        $domxml->preserveWhiteSpace = $this->preserveWhiteSpace;
        $domxml->formatOutput       = $this->formatOutput;

        #print "<br>\n<br>\n$xml<br>\n<br>\n";
        
        $domxml->loadXML($xml);
    
        $Oject = $this->domtoobject($domxml->documentElement, 0);
        return $Oject;    
    }

	protected function getAttribute($node, $attribute_name) {
		if (!$node->hasAttributes()) {
			return false;
		}

		foreach ($node->attributes as $name => $attr_node) {
			if ($name == $attribute_name) {
				return $attr_node->nodeValue;
			}
		}
		return false;

		// $node->hasAttribute($attribute_name);
		// return $node->getAttribute($attribute_name);
	}
    
    ################################################################################################
    private function domtoobject($node, $level) {
        $level++;

        if($node->hasChildNodes()) {

            for($i=0; $i < $level;$i++) {
                $blanks .= '     ';
            }

            if($this->debug) print "Level: $level $blanks tagName: $node->tagName<br>\n";

            foreach($node->childNodes as $childnode){

				// save attributes defined as legal
				if (!empty($this->attributesOfInterest)) {
					foreach ($this->attributesOfInterest as $attr) {
						if ($attr_value = $this->getAttribute($childnode, $attr)) {
							$attribute_name = $this->nodeName($childnode->nodeName) ."_Attr_" . $attr;
							$obj->{$attribute_name} = $attr_value;
						}
					}
				}

                if($this->debug) print "Level: $level $blanks childnode: tagName: $childnode->tagName, tagValue: $childnode->tagValue, nodeName: $childnode->nodeName, nodeValue: $childnode->nodeValue<br>\n";
                #Invoice og InvoiceLine mŒ alltid bli satt til array.
                if($obj->{$childnode->nodeName} && !is_array($obj->{$childnode->nodeName})) {
                    #node name is set from before as a string- we have to convert it to an array if its not already
                    if($this->debug) print "Level: $level $blanks $node->nodeName - Not an array - convert it<br>\n";
                    $copy = $obj->{$childnode->nodeName};
                    unset($obj->{$childnode->nodeName}); # will this zero out so that we can set the variable as an array (change it)
                    $obj->{$childnode->nodeName}[] = $copy; #We have to keep the existing element
                }

                #print "Level: $level $blanks Has child nodes: tagName: $childnode->tagName, tagValue: $childnode->tagValue, nodeName: $childnode->nodeName, nodeValue: $childnode->nodeValue<br>\n";
                #print "Level: $level $blanks Antall noder i barn: " . $childnode->childNodes->length . "<br>\n";
                #print "Level: $level $blanks Forste barn (foreldre): " . $childnode->firstChild->nodeName . ' = ' . $childnode->firstChild->nodeValue . "<br>\n";

                $nodeName = $this->nodeName($childnode->nodeName);

                if(!is_array($obj->{$nodeName}) && !$this->arrayTagH[$nodeName]) { #Only if this is not an array or does not exist
                    #ArrayTags should always be arrays. Even if its only one element.

                    if($childnode->childNodes->length == 1  && $childnode->firstChild->nodeName == '#text') {
                        #If its only one - and it is of type text - couple it directly.
                        if(strlen($childnode->firstChild->nodeValue))
                            $obj->{$nodeName} = utf8_decode($childnode->firstChild->nodeValue);

                    } else {
                        $obj->{$nodeName} = $this->domtoobject($childnode, $level);
                    }

                ####################################################################################
                } else { #If we come here - it is an array for sure
   
                    if($childnode->childNodes->length == 1  && $childnode->firstChild->nodeName == '#text') {
                        #If its only one - and it is of type text - couple it directly.
                        if(strlen($childnode->firstChild->nodeValue))
                            list($obj->{$nodeName}[]) = utf8_decode($childnode->firstChild->nodeValue);

                    } else {
                        $obj->{$nodeName}[] = $this->domtoobject($childnode, $level);
                    }                   
                }
            }
        } 
        return $obj;
    }
    
    ################################################################################################
    private function nodeName($nodeName) {
        if(strstr($nodeName, ':')) {
            list($tmp, $nodeName) = split(':', $nodeName);
        }
        return $nodeName;
    }
}
?>