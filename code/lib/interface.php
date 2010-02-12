<?

if(strlen($_REQUEST['currentInterface']) > 0)
    $currentInterface = $_REQUEST['currentInterface'];
else
    $currentInterface = $_SETUP['LOGIN_INTERFACE'];

print $_lib['sess']->doctype ?>
<head>
    <title>Lodo - customer</title>
    <meta name="cvs"                content="$Id: interface.php,v 1.24 2005/10/28 17:59:40 thomasek Exp $" />
    <link rel="stylesheet"          href="/css/default_tab.css" media="screen" type="text/css" />
    <link rel="stylesheet"          href="/css/default_intranett.css" media="screen" type="text/css" />
</head>
<body class="tab">
<center>
  <table class="tab">
  <tr>
    <?
    $interfaces = $_lib['sess']->get_interface(); #Array with available interfaces for the role

    foreach ($interfaces as $i => $value)
    {
        $INT = strtoupper($value);
        if($INT == strtoupper($currentInterface))
        {
            $class = "active_tab";
        }
        else
        {
            $class = "tab";
        }
        ?>
        <td><div  class="<? print $class ?>"><a href="<? print $value ?>.php?t=<? print $_SETUP['LOGIN'][$INT]['FIRSTPAGE'] ?>" target="interface" onClick="window.location = '<? print $_lib['sess']->dispatch ?>t=lib.interface&amp;currentInterface=<? print $value ?>';"><? print $value ?></a></div></td>
        <?
    }
    ?>
    </tr>
  </table>
</center>
</body>
</html>
