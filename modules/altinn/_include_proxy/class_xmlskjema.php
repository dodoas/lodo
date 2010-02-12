<?php
class xmlSkjema
{
	var $xsd;
	var $xsdElements;
	var $xsdChild;
	var $oldParent;
	var $xsdAttrib;
	var $xsdAttrib2;
	var $printErr;
	function xmlSkjema($params)
	{
		$this->xsd = new DOMDocument;
		if ($params["filename"] != "")
		{
			$this->xsd->load($params["filename"]) or die("Aborting! -- Unable to load schema definition file: " . $params["filename"]);
		}
		else
		{
			$this->xsd->loadXML($params["xmldata"]);
		}
		$this->xsdElements = $this->xsd->firstChild;
		$this->xsdElements = $this->xsdElements->firstChild;
	}
	function rewindElement()
	{
		$this->xsdElements = $this->xsd->firstChild;
		$this->xsdElements = $this->xsdElements->firstChild;
	}
	function NextElement()
	{
		$this->xsdElements = $this->xsdElements->nextSibling;
		if (is_null($this->xsdElements))
			return false;
		if ($this->xsdElements->nodeName == "xs:element")
		{
			return true;
		}
		else
			return $this->NextElement();
	}
	function printErrors($b)
	{
		$this->printErr = $b;
	}
	function getElement()
	{
		// print "nodeName: " . $this->xsdElements->nodeName . "<br>";
		return $this->xsdElements->getAttribute("name");
	}
	/** Children **/
	function firstChild()
	{
		if ($this->xsdElements->hasChildNodes())
		{
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:complexType");
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:sequence");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$this->xsdChild = $this->nextSubComponent($myComponent, "xs:element");
			else
				return false;
			if (!is_null($this->xsdChild))
			{
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	function getChild()
	{
		return $this->xsdChild->getAttribute("ref");
	}
	function getChildMin()
	{
		return $this->xsdElements->getAttribute("minOccurs");
	}
	function getChildMax()
	{
		return $this->xsdElements->getAttribute("maxOccurs");
	}
	function nextChild()
	{
		$this->xsdChild = $this->xsdChild->nextSibling;
		if (!is_null($this->xsdChild))
		if ($this->xsdChild->nodeName == "xs:element")
		{
			return true;
		}
		else
			return $this->nextChild();
	}
	/** Attribs **/
	function firstAttrib()
	{
		if ($this->xsdElements->hasChildNodes())
		{
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:complexType");
			if ($myComponent && $myComponent->hasChildNodes())
				$this->xsdAttrib = $this->nextSubComponent($myComponent, "xs:attribute");
			else
				return false;
			if (!is_null($this->xsdAttrib))
			{
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	function getAttrib($name)
	{
		if ($this->xsdAttrib->hasAttribute($name))
			return $this->xsdAttrib->getAttribute($name);
		else
			return "";
	}
	function nextAttrib()
	{
		$this->xsdAttrib = $this->xsdAttrib->nextSibling;
		if (!is_null($this->xsdAttrib))
		if ($this->xsdAttrib->nodeName == "xs:attribute")
		{
			return true;
		}
		else
			return $this->nextAttrib();
	}
	/** Attribs 2 **/
	function firstAttrib2()
	{
		if ($this->xsdElements->hasChildNodes())
		{
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:complexType");
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:simpleContent");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:extension");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$this->xsdAttrib2 = $this->nextSubComponent($myComponent, "xs:attribute");
			else
				return false;
			if (!is_null($this->xsdAttrib2))
			{
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	function getAttrib2($name)
	{
		if ($this->xsdAttrib2->hasAttribute($name))
			return $this->xsdAttrib2->getAttribute($name);
		else
			return "";
	}
	function nextAttrib2()
	{
		$this->xsdAttrib2 = $this->xsdAttrib->nextSibling;
		if (!is_null($this->xsdAttrib2))
			if ($this->xsdAttrib2->nodeName == "xs:attribute")
			{
				return true;
			}
			else
				return $this->nextAttrib2();
		else
			return false;
			
	}
	/** Format **/
	function getFormat()
	{
		if ($this->xsdElements->hasChildNodes())
		{
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:complexType");
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:simpleContent");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:extension");
			else
				return false;
			if (!is_null($myComponent))
			{
				if ($myComponent->hasAttribute("base"))
					return $myComponent->getAttribute("base");
				else
					return "";
			}
			else
				return false;
		}
		else
			return false;
	}
	/** Options (enumerators) **/
	function getOptions()
	{
		if ($this->xsdElements->hasChildNodes())
		{
		
		
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:complexType");
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:attribute");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:simpleType");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:restriction");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:enumeration");
			else
				return false;
			while (!is_null($myComponent))
			{
				if ($myComponent->hasAttribute("value"))
					$ret[] = $myComponent->getAttribute("value");
				$myComponent = $myComponent->nextSibling;
			}
			return $ret;
		}
		else
			return false;
	}
	/** ValueType **/
	function getValueType()
	{
		if ($this->xsdElements->hasChildNodes())
		{
		
		
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:complexType");
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:attribute");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:simpleType");
			else
				return false;
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:restriction");
			else
				return false;
			if (!is_null($myComponent))
			{
				if ($myComponent->hasAttribute("base"))
					return $myComponent->getAttribute("base");
			}
			else
				return false;
		}
		else
			return false;
	}
	/** Other functions **/
	function isValue()
	{
		if ($this->firstAttrib2())
			if ($this->getAttrib2("name") == "orid")
			{
				// print "ORID" . $this->getAttrib2("fixed") . "<br>";
				return true;
			}
		return false;
	}
	function getOrid()
	{
		if ($this->firstAttrib2())
			if ($this->getAttrib2("name") == "orid")
				return $this->getAttrib2("fixed");
		while($this->nextAttrib2())
			if ($this->getAttrib2("name") == "orid")
				return $this->getAttrib2("fixed");
	}
	function getGruppeid()
	{
		if ($this->firstAttrib())
			if ($this->getAttrib("name") == "gruppeid")
				return $this->getAttrib("fixed");
		while($this->nextAttrib())
			if ($this->getAttrib("name") == "gruppeid")
				return $this->getAttrib("fixed");
	}
	function nextSubComponent($node, $componentName)
	{
		if ($node->hasChildNodes())
		{
			$subNode = $node->firstChild;
			$i = 0;
			while(!is_null($subNode))
			{
				$i++;
				if ($subNode->nodeName == $componentName)
					return $subNode;
				$subNode = $subNode->nextSibling;
			}
			return NULL;
		}
		else
		return NULL;
	}
}
?>
