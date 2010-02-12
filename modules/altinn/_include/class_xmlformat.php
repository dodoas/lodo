<?php
class xmlFormat
{
	var $xsd;
	var $xsdElements;
	var $Format;
	function xmlFormat($params)
	{
		$this->xsd = new DOMDocument;
		if ($params["filename"] != "")
			$this->xsd->load($params["filename"]);
		else if ($params["xmldata"] != "")
			$this->xsd->loadXML($params["xmldata"]);
		$this->xsdElements = $this->xsd->firstChild;
		$this->xsdElements = $this->xsdElements->firstChild;
	}
	function getFormat()
	{
		while ($this->NextElement())
		{
			$myName = $this->getElement();
			$format[$myName]["name"] = $myName;
			$format[$myName]["varType"] = $this->getVarType();
			$format[$myName]["maxLength"] = $this->getAttrib("maxLength");
			$format[$myName]["minLength"] = $this->getAttrib("minLength");
			$format[$myName]["length"] = $this->getAttrib("length");
			$format[$myName]["pattern"] = $this->getAttrib("pattern");
			$format[$myName]["totalDigits"] = $this->getAttrib("totalDigits");
			$format[$myName]["enumerators"] = $this->getOptions();
		}
		$this->Format = $format;
		return $format;
	}
	function NextElement()
	{
		$this->xsdElements = $this->xsdElements->nextSibling;
		if (is_null($this->xsdElements))
			return NULL;
		if ($this->xsdElements->nodeName == "xs:simpleType")
		{
			return true;
		}
		else
			return $this->NextElement();
	}
	function getElement()
	{
		if ($this->xsdElements->hasAttribute("name"))
			return $this->xsdElements->getAttribute("name");
	}
	/** VariableType **/
	function getVarType()
	{
		if ($this->xsdElements->hasChildNodes())
		{
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:restriction");
			if (!is_null($myComponent))
			{
				list($tull, $ret) = split(":", $myComponent->getAttribute("base"));
				return $ret;
			}
			else
				return false;
		}
		else
			return false;
	}
	/** Attribs **/
	function getAttrib($name)
	{
		if ($this->xsdElements->hasChildNodes())
		{
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:restriction");
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:" . $name);
			else
				return "";
			if (!is_null($myComponent))
			{
				if ($myComponent->hasAttribute("value"))
					return $myComponent->getAttribute("value");
			}
		}
	}
	/** Options (enumerators) **/
	function getOptions()
	{
		if ($this->xsdElements->hasChildNodes())
		{
			$myComponent = $this->nextSubComponent($this->xsdElements, "xs:restriction");
			if ($myComponent && $myComponent->hasChildNodes())
				$myComponent = $this->nextSubComponent($myComponent, "xs:enumeration");
			else
				return false;
			while (!is_null($myComponent))
			{
				if($myComponent->nodeName == "xs:enumeration")
					if ($myComponent->hasAttribute("value"))
						$ret[] = $myComponent->getAttribute("value");
				$myComponent = $myComponent->nextSibling;
			}
			return $ret;
		}
		else
			return false;
	}
	/** Other functions **/
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