<?php
class childKeeper
{
	var $childRef;
	function childKeeper($xsdObject)
	{
		$xsdObject->rewindElement();
		while ($xsdObject->NextElement())
		{
			$myParent = $xsdObject->getElement();
			if ($xsdObject->firstChild())
			{
				$myChild = $xsdObject->getChild();
				$this->childRef[$myParent][] = $myChild;
			}
			while ($xsdObject->nextChild())
			{
				$myChild = $xsdObject->getChild();
				$this->childRef[$myParent][] = $myChild;
			}
		}
		//print_r($this->childRef);
	}
	function findChildren($parent)
	{
		return $this->childRef[$parent];
	}
}
?>