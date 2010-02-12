<?
/* $Id: login_screen.php,v 1.8 2005/09/08 08:51:17 thomasek Exp $ index.php,v 1.4 2001/12/12 20:41:21 thomasek Exp $ */
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

#Initialize session

$login_password = "";
$login_ip       = "";
$login_timeout   = 0;

if(!$message) { $message = "Enter username and password"; };

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix login</title>
    <meta name="cvs"                content="$Id: login_screen.php,v 1.8 2005/09/08 08:51:17 thomasek Exp $" />
    <? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
    <script language="javascript">
    	top.document.location = '/';
    </script>
</head>
<body>
    <!-- SID: <? print session_id() ?>-->
    <form action="<? print $_SERVER['REQUEST_URI'] ?>" method="post" target=_top>
        <table width="100%" cellspacing="0">
            <tr>
                <th width="80%" colspan="4"><? print $_lib['sess']->get_companydef('VName') ?></th>
                <th width="19%"><? print $_lib['lang']->s(array('id' => 'idSystemName', 'static' => true)) ?> <? print $_lib['lang']->s(array('id' => 'idSystemLogin', 'static' => true)) ?></th>
                <th width="13%"><? print $_lib['lang']->s(array('id' => 'idSystemDatabase', 'static' => true)) ?><br/>
                    <input type="text" name="DB_NAME_LOGIN">

                </th>
                <td colspan="4"><img src="img/poweredbyempatix.png">Empatix login </td>
                <td>
                    <? print $_lib['lang']->s(array('id' => 'idUsername', 'static' => true)) ?> (<? print $_lib['lang']->s(array('id' => 'idEmail', 'static' => true )) ?>)<br />
                    <input type="text" name="username" value="<? print $_SESSION['login_username'] ?>" size="20" maxlength="30" tabindex="1" />
                </td>
                <td>
                    <? print $idPassword ?><br />
                    <input type="password"  name="password" value="" size="20" maxlength="30" tabindex="2" />
                </td>
            </tr>
            <tr class="BGColorDark">
                <td colspan="4"><? print $_lib['sess']->get_companydef('Slogan') ?>: <? print $_lib['lang']->s(array('id' => 'idVersion', 'static' => true)) ?> <? print $_lib['lang']->s(array('id' => 'idReleaseDate', 'static' => true)) ?> <? print $_lib['lang']->s(array('id' => 'idMadeBy', 'static' => true)) ?></td>
                <td><? print "$message"; ?></td>
                <td align="right"><input name="submit_login" type="submit" accesskey="L" tabindex="4" value="Login (L)" /></td>
            </tr>
        </table>
    </form>
</body>
