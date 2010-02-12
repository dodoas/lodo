<?
# $Id: frame.php,v 1.33 2005/10/28 17:59:39 thomasek Exp $ index2.php,v 1.8 2001/12/02 09:19:50 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

#sleep(5);

//print $loginPage." - ".$userLogin->DefaultInterface.".".$userLogin->DefaultModule.".".$userLogin->DefaultTemplate;
//exit;
print $_lib['sess']->doctype ?>
<head>
    <title><? print $_sess->title ?></title>
    <meta name="cvs"                content="$Id: frame.php,v 1.33 2005/10/28 17:59:39 thomasek Exp $" />
    <? require_once $_SETUP['HOME_DIR']."/code/lib/html/head.inc"; ?>
    <script language="JavaScript" type="text/javascript">
    	<!--
    	// Kvitte seg med frames in frames.
    	if (self.parent.frames.length != 0)
    		self.parent.location = document.location;
    	-->
    </script>
</head>
<frameset rows="50, *" frameborder="no" border="0" framespacing="0">
    <frame src="<? print $_lib['sess']->dispatchs ?>t=lib.interface" name="interface_menu" noresize frameborder="no" scrolling="no" marginwidth="0" marginheight="0">
    <frame src="<? print $_lib['sess']->dispatchs ?>t=lodo.main" name="interface" frameborder="NO" marginheight="0">
</frameset>
<noframes>
<body>

Zorry, your browser has to support frames.

</body>
</noframes>
</html>
