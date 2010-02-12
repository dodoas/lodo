<?
/** ****************************************************************************
* Tollpost functionality
*
* @package tollpost1_logic_privatleveranse
* @version  $Id:
* @author Thomas Ekdahl, Empatix AS
* @copyright http://www.empatix.com/ Empatix AS, 1994-2005, post@empatix.com
*/

class XMLElement extends SimpleXMLElement
{
    public function addElement (SimpleXMLElement $xmlTree,$root=false)
    {
        if($root)
        {
            $child = $this->addChild ($xmlTree->getName());
            foreach ($xmlTree->attributes() as $k => $v)
            {
                $child->addAttribute($k,$v);
            }
            $child->addElement($xmlTree);
        }
        else
        {
            foreach ($xmlTree as $childName => $childTree)
            {

                $child = $this->addChild($childName,$this->fix_content((string) $childTree)); // this is not comletely correct
                foreach ($childTree->attributes() as $k => $v)
                {
                    $child->addAttribute($k,$v);
                }
                $child->addElement($childTree->children());
            }
        }
    }
    
    #When the xml is read by simplexml_load_file or simplexml_load_file the values that contains &amp; is converted to &. And then it is not possible to add these values without getting errors.
    #THis function should fix this
    public function fix_content($value) {
        return str_replace('&', '&amp;', $value);
    }
}
?>