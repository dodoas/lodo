<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Class for handling configuration settings for this user.
****************************************************************************/
class Config
{
	var $config;
	var $db;

	var $TYPE_TERMIN;
	var $TYPE_ORGNO;
	var $TYPE_MVABANKACCOUNT;
	var $TYPE_MVAKID;
	var $TYPE_FAGSYSTEMID;
	var $TYPE_PASSWORD;
	var $TYPE_BATCHSUBNO;

	/* Constructor */
	function Config( $db )
	{
		$this->TYPE_TERMIN = 0;
		$this->TYPE_ORGNO = 1;
		$this->TYPE_MVABANKACCOUNT = 2;
		$this->TYPE_MVAKID = 3;
		$this->TYPE_FAGSYSTEMID = 4;
		$this->TYPE_PASSWORD = 5;
		$this->TYPE_BATCHSUBNO = 6;
		$this->db = $db;

		$this->config = array(
			'termintype' => 4,
			'mvabankaccount' => '',
			'batchsubno' => '',
			'fagsystemid' => 0,
			'password' => ''
			);

		$this->LoadConfig();
	}

	function LoadConfig( )
	{
		$sqlStr = 'SELECT * FROM altinn_config';
		$rs = $this->db->Query( $sqlStr );
		if ($rs)
		{
			if ($row = $this->db->NextRow($rs))
			{
				$this->config = array(
					'termintype' => $row['termintype'],
					'mvabankaccount' => $row['mvabankaccount'],
					'batchsubno' => $row['batchsubno'],
					'fagsystemid' => $row['fagsystemid'],
					'password' => $row['password']
					);
			}
			$this->db->EndQuery($rs);
		}
	}

	function OpenConfig(  )
	{
	}

	function GetConfig( $type )
	{
		global $_sess;

		if ( $type == $this->TYPE_TERMIN )
		{
			$retVal = $this->config['termintype'];
			if ($_lib['sess']->get_company('VatPeriod') != '') {$retVal = $this->config['termintype'];}

			return($retVal);
		}
		elseif ( $type == $this->TYPE_ORGNO ) { return(str_replace(' ', '', $_lib['sess']->get_company('OrgNumber'))); }
		elseif ( $type == $this->TYPE_MVABANKACCOUNT )
		{
			$retVal = $this->config['mvabankaccount'];
			if ( !is_numeric($retVal) || $retVal == 0 )
			{
				$retVal = str_replace(' ', '', $_lib['sess']->get_company('BankAccount'));
			}

			return($retVal);
		}
		elseif ( $type == $this->TYPE_MVAKID ) { return($this->config['mvakid']); }
		elseif ( $type == $this->TYPE_FAGSYSTEMID ) { return($this->config['fagsystemid']); }
		elseif ( $type == $this->TYPE_PASSWORD ) { return($this->config['password']); }
		elseif ( $type == $this->TYPE_BATCHSUBNO ) { return($this->config['batchsubno']); }
	}

	function CloseConfig(  )
	{
	}
}
?>
