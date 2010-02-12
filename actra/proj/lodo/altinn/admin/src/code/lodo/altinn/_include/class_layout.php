<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Class for doing layout stuff.
****************************************************************************/
class Layout
{
	var $lodo;
	var $documentPhase;

	var $PHASE_HTTPHEAD;
	var $PHASE_HTMLHEAD;
	var $PHASE_HTMLBODY;
	var $PHASE_HTMLFOOT;

	function Layout( $lodo )
	{
		$this->lodo = $lodo;

		$this->PHASE_HTTPHEAD = 0;
		$this->PHASE_HTMLHEAD = 1;
		$this->PHASE_HTMLBODY = 2;
		$this->PHASE_HTMLFOOT = 3;
		$this->documentPhase = $this->PHASE_HTTPHEAD;
	}
	
	function PrintHead( $title )
	{
		global $_doctype;
		
		if ( $this->lodo->inLodo ) {
			echo( $_doctype );
		}
		else {
/* Insert doctype! */
			echo( "<html>" );
		}
?>
<head>
	<title><?php echo( $title );?></title>
	<?php echo( $this->lodo->PrintHeadContent() );?>
</head>
<body>
	<?php echo( $this->lodo->PrintBodyContent() );?>
<?php
		$this->documentPhase = $this->PHASE_HTMLBODY;
	}

	function PrintFoot(  )
	{
		/* If we haven't sent the header already, do it */
		if ($this->documentPhase < $this->PHASE_HTMLBODY)
		{
			$this->PrintHead("");
		}
		/* Now we can end the document */
?>
</body>
</html>
<?php
		$this->documentPhase = $this->PHASE_HTMLFOOT;
	}

	function PrintError( $msg )
	{
		/* If we haven't left the HTTP header yet, send header */
		if ($this->documentPhase < $this->PHASE_HTMLBODY)
		{
			$this->PrintHead("");
		}
		echo('<h1>Feil</h1><p>En feil oppstod: ' . $msg . '</p>');

		if ($this->documentPhase < $this->PHASE_HTMLFOOT)
		{
			$this->PrintFoot("");
		}
	}

	function PrintWarning( $msg )
	{
		$this->PrintError( $msg );
	}
}
?>
