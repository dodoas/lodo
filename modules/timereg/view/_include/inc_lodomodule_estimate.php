<?PHP
/***************************************************************************
** Copyright (c) 2004-2005 Lodo.no.
** Developed by Actra / Gunnar Skeid (gunnar@actra.no)
**
** Interface between Lodo account system and this time account system.
***************************************************************************/
require_once 'inc_database.php';
require_once 'inc_lodo.php';

class LodoTimerEstimate
{
	var $db;
	var $lodo;

	var $sessionCurrentEstimateSheet = "LTR_ESTIMATESHEET";
	var $currentEstimateSheet = 0;

	var $totalHours;
	var $totalMoney;

	var $totalProjectHours;
	var $totalProjectMoney;

	var $goUrlArray;

	var $disabledText; // If there is no current estimate sheets, input should be blocked

	/**
	 * Constructor: db object and lodo object. Also saves info entered in form for a customer.
	 *
	 * @param unknown $db
	 * @return LodoTimerEstimate
	 */
	function LodoTimerEstimate( $db, $lodo, $goUrlArray )
	{
		$this->db = $db;
		$this->lodo = $lodo;
		$this->totalHours = 0;
		$this->totalMoney = 0;
		$this->totalProjectHours = array();
		$this->totalProjectMoney = array();
		$this->disabledText = "";

		$this->goUrlArray = $goUrlArray;

		if ( !isset( $_SESSION[ $this->sessionCurrentEstimateSheet ] ) ) {
			$_SESSION[ $this->sessionCurrentEstimateSheet ] = 0;
		}
		$this->currentEstimateSheet = $_SESSION[ $this->sessionCurrentEstimateSheet ];

		/* Check if we should save something */
		if ( $_REQUEST['cmd'] == "savecustomer" )
		{
			$this->SaveForm();
		}
		/* Insert a new estimate into the database */
		elseif ( $_REQUEST['newestimate'] != "" )
		{
			/* First check if there is an estimate with this name already */
			$update = true;
			$sqlStr = "SELECT estimate_id FROM timer_estimatesheet WHERE name='" . str_replace("'", "''", $_REQUEST['newestimate']) . "' AND ext_user_id=" . $this->lodo->currentUserId;
			if ( $rs = $this->db->Query( $sqlStr ) )
			{
				if ( $row = $this->db->NextRow( $rs ) )
				{
					$update = false;
					header("Location: " . $this->lodo->GetGoUrl( 0, $this->goUrlArray) . "&msg=Dette+estimatnavnet+finnes+allerede!\n");
					$this->db->EndQuery( $rs );
				}
				$this->db->EndQuery( $rs );
			}

			if ($update)
			{
				/* First insert new estimate sheet */
				$values = array("ext_client_id" => $this->lodo->currentClientId,
					"ext_user_id" => $this->lodo->currentUserId,
					"name" => trim($_REQUEST['newestimate'])
					);
				$sqlStr = "INSERT INTO timer_estimatesheet " . $this->db->BuildSqlString( $this->db->BUILD_INSERT, $values);
				$this->db->Query( $sqlStr );

				/* Get id of new estimate */
				$estimateId = 0;
				$sqlStr = "SELECT MAX(estimate_id) FROM timer_estimatesheet WHERE ext_user_id=" . $this->lodo->currentUserId;
				if ( $rs = $this->db->Query( $sqlStr ) )
				{
					if ( $row = $this->db->NextRow( $rs ) )
					{
						$estimateId = $row[0];
					}
					$this->db->EndQuery( $rs );
				}

				/* Now insert projecttypes for this sheet */
				if ( $estimateId > 0 )
				{
					/* Loop through all projecttypes and copy them */
					$sqlStr = "SELECT * FROM timer_projecttype";
					if ( $rs = $this->db->Query($sqlStr) )
					{
						while ( $row = $this->db->NextRow( $rs ) )
						{
							$insertValues = array(
								'projecttype_id' => $row['projecttype_id'],
								'estimate_id' => $estimateId,
								'hourly_cost' => $row['hourly_cost'],
								'name' => $row['name']);

							$insertSql = "INSERT INTO timer_projectestimate " . $this->db->BuildSQLString( $this->db->BUILD_INSERT, $insertValues );
							$this->db->Query( $insertSql );
						}
						$this->db->EndQuery( $rs );
					}
				}

				header("Location: " . $this->lodo->GetGoUrl( 0, $this->goUrlArray) . "\n");
			}
		}
		/* Edit estimate name */
		elseif ( $_REQUEST['editestimate'] != "" )
		{
			/* First check if there is an estimate with this name already */
			$update = true;
			$sqlStr = "SELECT estimate_id FROM timer_estimatesheet WHERE name='" . str_replace("'", "''", $_REQUEST['editestimate']) . "' AND ext_user_id=" . $this->lodo->currentUserId;
			if ( $rs = $this->db->Query( $sqlStr ) )
			{
				if ( $row = $this->db->NextRow( $rs ) )
				{
					if ($_REQUEST['estimate_id'] != $row['estimate_id'])
					{
						$update = false;
						header("Location: " . $this->lodo->GetGoUrl( 0, $this->goUrlArray) . "&msg=Dette+estimatnavnet+finnes+allerede!\n");
						$this->db->EndQuery( $rs );
					}
				}
				$this->db->EndQuery( $rs );
			}

			if ($update)
			{
				$values = array("name" => trim($_REQUEST['editestimate']));
				$sqlStr = "UPDATE timer_estimatesheet SET " . $this->db->BuildSqlString( $this->db->BUILD_UPDATE, $values) . " WHERE estimate_id=" . $_REQUEST['estimate_id'];
				$this->db->Query( $sqlStr );

				header("Location: " . $this->lodo->GetGoUrl( 0, $this->goUrlArray) . "\n");
			}
		}
		/* Make a new estimate active */
		elseif ( $_REQUEST['setestimate'] != "" )
		{
			$this->currentEstimateSheet = $_SESSION[ $this->sessionCurrentEstimateSheet ] = $_REQUEST['setestimate'];
		}
		/* Delete estimate? */
		elseif ( $_REQUEST['deleteestimate'] != "" )
		{
			$sqlStr = "DELETE FROM timer_estimatesheet WHERE ext_user_id=" . $this->lodo->currentUserId . " AND ext_client_id=" . $this->lodo->currentClientId . " AND estimate_id=" . $this->currentEstimateSheet;
			$this->db->Query( $sqlStr );

			$sqlStr = "DELETE FROM timer_projectestimate WHERE estimate_id=" . $this->currentEstimateSheet;
			$this->db->Query( $sqlStr );
			$this->currentEstimateSheet = $_SESSION[ $this->sessionCurrentEstimateSheet ] = 0;

			header("Location: " . $this->lodo->GetGoUrl( 0, $this->goUrlArray) . "\n");
		}
	}

	/**
	 * Estimate header add, modify and delete estimate sheets.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayMainHeader()
	{
		global $_REQUEST, $MY_SELF, $_SETUP;

		$retVal = "";

		$estimateName = '';
		/* Get a list of sheets */
		$sqlStr = "SELECT estimate_id,name FROM timer_estimatesheet " .
			" WHERE ext_client_id=" . $this->lodo->currentClientId . " AND ext_user_id=" . $this->lodo->currentUserId;
		$retVal .= "<table class=\"tab\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\"><tr>";
		if ( ($rs = $this->db->Query( $sqlStr )) )
		{
			$first = true;
			$found = false;	// Found current estimate sheet
			while ( ($row = $this->db->NextRow( $rs )) )
			{
				/* If this is the first loop and current estimate sheet is not set: make the first sheet default */
				if ($first)
				{
					$first = false;
					if ( $this->currentEstimateSheet == 0) {
						$_SESSION[ $this->sessionCurrentEstimateSheet ] = $this->currentEstimateSheet = $row['estimate_id'];
					}
				}
				/* Is this current estimate? */
				if ( $row['estimate_id'] == $this->currentEstimateSheet ) {
					$estimateName = $row['name'];
					$found = true;
					$retVal .= "<td><div class=\"active_tab\"><a href=\"" . $this->lodo->GetGoUrl( 0, $this->goUrlArray ) . "&amp;setestimate=" . $row['estimate_id'] . "\">" . $row['name'] . "</a></div></td>";
				}
				else {
					$retVal .= "<td><div class=\"tab\"><a href=\"" . $this->lodo->GetGoUrl( 0, $this->goUrlArray ) . "&amp;setestimate=" . $row['estimate_id'] . "\">" . $row['name'] . "</a></div></td>";
				}
			}
			$this->db->EndQuery( $rs );

			if (!$found)
			{
				$_SESSION[ $this->sessionCurrentEstimateSheet ] = $this->currentEstimateSheet = 0;
			}
		}
		$retVal .= "</tr></table><div style=\"float: right;\">&#160;<form method=\"post\" action=\"" . $MY_SELF .  "\" name=\"nestimate\" style=\"margin: 0px; padding: 0px;\" onsubmit=\"return DoNewEstimate( '" . $_SETUP['DISPATCH'] . substr($this->lodo->GetGoUrl( 0, $this->goUrlArray), 1) . "' );\">" . $this->lodo->GetGoUrl( 1, $this->goUrlArray ). "<input type=\"hidden\" name=\"newestimate\" value=\"Ny\" /><input type=\"submit\" value=\"Nytt estimat (N)\" accesskey=\"N\" /></form>";
		if ( $estimateName != '' )
		{
			$retVal .= "<form method=\"post\" action=\"" . $MY_SELF .  "\" name=\"eestimate\" style=\"margin: 0px; padding: 0px;\" onsubmit=\"return DoEditEstimate( '" . $_SETUP['DISPATCH'] . substr($this->lodo->GetGoUrl( 0, $this->goUrlArray), 1) . "&amp;estimate_id=" . $this->currentEstimateSheet . "','" . $estimateName . "' );\">" . $this->lodo->GetGoUrl( 1, $this->goUrlArray ). "<input type=\"hidden\" name=\"editestimate\" value=\"Edit\" /><input type=\"hidden\" name=\"estimate_id\" value=\"" . $this->currentEstimateSheet . "\" /><input type=\"submit\" value=\"Editer estimatnavn\" /></form></div>";
		}
		$retVal .= "</div>";
//[<a href=\"javascript:DoEditEstimate('" . $this->lodo->GetGoUrl( 0, $this->goUrlArray ) . "&amp;setestimate=" . $row['estimate_id'] . "','Test');\">editer</a>]
		if ($this->currentEstimateSheet != 0) {$this->disabledText = "";}
		else {$this->disabledText = " disabled";}


		return( $retVal );
	}

	/**
	 * Internal: Saves info entered in the form for a customer.
	 *
	 * @param
	 * @return
	 */
	function SaveForm()
	{
		global $_REQUEST;

		/* Get data */
		$customer_id = $_REQUEST['customer_id'];
		$estimate_id = $_REQUEST['estimate_id'];

		/* If the customer is active or not */
		$active = $_REQUEST['active'];
		if ( $active == "1" ) {$active = 1;}
		else {$active = 0;}

		/* Save the active state */
		/* Delete current */
		$sqlStr = "DELETE FROM timer_customeruser WHERE ext_user_id=" . $this->lodo->currentUserId . " AND customer_id=" . $customer_id;
//echo($sqlStr);
		$this->db->Query( $sqlStr );

		/* If the state is unactive: Insert that state into the database */
		if ($active == 0)
		{
			$values = array ("ext_user_id" => $this->lodo->currentUserId, "unactive" => 1, "customer_id" => $customer_id);
			$sqlStr = "INSERT INTO timer_customeruser " . $this->db->BuildSQLString( $this->db->BUILD_INSERT, $values);
			$this->db->Query( $sqlStr );
		}

		/* Save estimate items */
		/* First delete the old ones for this company */
		$sqlStr = "DELETE FROM timer_estimateitem WHERE estimate_id=" . $estimate_id . " AND customer_id=" . $customer_id;
		$this->db->Query( $sqlStr );

		/* Now insert the new estimates. Loop through amount_ */
		/* Loop through eveything in $_REQUEST and pick out the amount_ variables */
		foreach ($_REQUEST as $key => $value)
		{
			/* Match amount_ ? */
			if ( !strncmp($key, "amount_", 7) )
			{
				/* Get the number from amount_x. x is project type id */
				$projecttype_id = substr( $key, 7 );

				/* Check that we really got a number */
				if ( is_numeric( $projecttype_id ) )
				{
					/* Save a new timer_estimate_item */
					$values = array(
						"estimate_id" => $estimate_id,
						"projecttype_id" => $projecttype_id,
						"customer_id" => $customer_id,
						"amount" => str_replace(",", ".", $value)
						);

					$sqlStr = "INSERT INTO timer_estimateitem " . $this->db->BuildSQLString( $this->db->BUILD_INSERT, $values);
					$this->db->Query( $sqlStr );
				}
			}
		}
	}

	/**
	 * Displays the header above the companies.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayHeader(  )
	{
		$retVal = "";

		/* List all project types */
		$sqlStr = "SELECT projecttype_id,hourly_cost,name " .
			" FROM timer_projectestimate WHERE estimate_id=" . $this->currentEstimateSheet;
		if ( ($rs = $this->db->Query( $sqlStr )) )
		{
			$loopCounter = 1;
			while ( ($row = $this->db->NextRow( $rs )) )
			{
				/* Just for init */
				$this->totalProjectHours[ $loopCounter ] = 0;
				$this->totalProjectMoney[ $loopCounter ] = 0;

				/* First cell should have border on left side */
				if ( $loopCounter == 1 ) {
					$retVal .= "<td style=\"border-left: 1px solid #000000;\">";
				}
				/* Cells after the first one should have nothing */
				else {
					$retVal .= "<td>";
				}
				$retVal .= "<b>" . $row['name'] . "</b></td>";
				$loopCounter++;
			}
			$this->db->EndQuery( $rs );
		}
		$retVal .= "<td>&#160;</td>";
		$retVal .= "<td>&#160;</td>";
		$retVal .= "<td style=\"border-right: 1px solid #000000;\">&#160;<form style=\"margin: 0px; padding: 0px;\" action=\"javascript:PopUp( '" . $_SETUP['DISPATCH'] . "?t=timereg.projecttypes', 400, 300 );\"><input type=\"submit\" value=\"Prosjekter\"/></form></td>";

		return( $retVal );
	}

	/**
	 * Displays the footer below the companies.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayFooter(  )
	{
		$retVal = "";

		/* List all project types */
		$sqlStr = "SELECT projecttype_id,hourly_cost,name " .
			" FROM timer_projectestimate WHERE estimate_id=" . $this->currentEstimateSheet;
		if ( ($rs = $this->db->Query( $sqlStr )) )
		{
			$loopCounter = 1;
			$this->totalHours = 0;
			$this->totalMoney = 0;
			while ( ($row = $this->db->NextRow( $rs )) )
			{
				/* First cell should have border on left side */
				if ( $loopCounter == 1 ) {
					$retVal .= "<td style=\"border-left: 1px solid #000000;\">";
				}

				/* Cells after the first one should have nothing */
				else {
					$retVal .= "<td>";
				}
				$sqlStrSum = "SELECT SUM(amount) " .
					" FROM timer_estimateitem " .
					" WHERE projecttype_id=" . $row['projecttype_id'] . " AND estimate_id=" . $this->currentEstimateSheet;
				if ( ($rsSum = $this->db->Query( $sqlStrSum )) )
				{
					if ( ($rowSum = $this->db->NextRow( $rsSum )) )
					{
						$totalProjectHours = $rowSum[0];
					}
					$this->db->EndQuery( $rsSum );
				}
				if ( !is_numeric($totalProjectHours) ) {$totalProjectHours = 0;}

//				$retVal .= "" . str_replace(".", ",", $this->totalProjectHours[ $loopCounter ]) . "t/kr" . $this->totalProjectMoney[ $loopCounter ] . "&#160;</td>";
				$retVal .= "" . str_replace(".", ",", $totalProjectHours . "t/kr" . ($totalProjectHours * $row['hourly_cost']) . "&#160;</td>");

				$this->totalHours += $totalProjectHours;
				$this->totalMoney += ($totalProjectHours * $row['hourly_cost']);

				$loopCounter++;
			}
			$this->db->EndQuery( $rs );
		}
		$retVal .= "<td align=\"right\">" . str_replace(".", ",", $this->totalHours) . "t</td><td align=\"right\">kr" . $this->totalMoney . "</td>";
		$retVal .= "<td style=\"border-right: 1px solid #000000;\"><form action=\"" . $_SERVER['SCRIPT_NAME'] . "\" name=\"estimatedel\" onsubmit=\"return AskDelete();\">" . $this->lodo->GetGoUrl( 1, $this->goUrlArray) . "<input type=\"hidden\" name=\"deleteestimate\" value=\"Slett estimat\" /><input type=\"submit\" name=\"n1\" value=\"Slett estimat\"" . $this->disabledText . " /></form></td>";

		return( $retVal );
	}

	function DisplayMainFooter(  )
	{
		$retVal = "<td style=\"border-top: 1px solid #000000;\" colspan=\"100\">&#160;</td>";

		return( $retVal );
	}

	/**
	 * Handles estimate for each company.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayCompany( $customerId )
	{
		$retVal = "";

		/* List all project types */
		$sqlStr = "SELECT projecttype_id,hourly_cost,name " .
			" FROM timer_projectestimate WHERE estimate_id=" . $this->currentEstimateSheet;
		if ( ($rs = $this->db->Query( $sqlStr )) )
		{
			$totalAmount = 0;
			$totalMoney = 0;
			$loopCounter = 1;
			while ( ($row = $this->db->NextRow( $rs )) )
			{
				/* First cell should have border on left side */
				if ( $loopCounter == 1 ) {
					$retVal .= "<td class=\"bgld\">";
				}

				/* Cells after the first one should have nothing */
				else {
					$retVal .= "<td class=\"bgld\">";
				}

				/* If estimate_id was not set by DisplayHeader, it means there is no estimate, though
				   everything is 0 */
				$amount = 0;

				if ( $this->currentEstimateSheet > 0 )
				{
					/* Ok, find current amount */
					$sqlStrItem = "SELECT amount FROM timer_estimateitem ".
						" WHERE customer_id=" . $customerId .
						" AND projecttype_id=" . $row['projecttype_id'] .
						" AND estimate_id=" . $this->currentEstimateSheet;

					if ( ($rsItem = $this->db->Query( $sqlStrItem )) )
					{
						if ( ($rowItem = $this->db->NextRow( $rsItem )) )
						{
							$amount = $rowItem['amount']; // How many hours for this project type for this company
							$money = ($amount * $row['hourly_cost']); // How much money for this project type for this company
							$totalAmount += $amount;
							$totalMoney += $money;

							$this->totalProjectHours[ $loopCounter ] += $amount;
							$this->totalProjectMoney[ $loopCounter ] += $money;
						}
						$this->db->EndQuery( $rsItem );
					}
				}

				$retVal .= "<input class=\"xs\" style=\"text-align: right;\" name=\"amount_" . $row['projecttype_id'] . "\" value=\"" . str_replace(".", ",", $amount) . "\" size=\"4\"" . $this->disabledText . " /></td>";
				$loopCounter++;
			}
			$this->db->EndQuery( $rs );
		}

		$retVal .= "<td class=\"bgld\" align=\"right\">&#160;" . str_replace(".", ",", $totalAmount) . "t</td>";
		$retVal .= "<td class=\"bgld\" align=\"right\">&#160;kr" . $totalMoney . "</td>";
		$retVal .= "<td class=\"bgld\" style=\"border-right: 1px solid #000000;\">&#160;<input type=\"hidden\" name=\"estimate_id\" value=\"" . $this->currentEstimateSheet . "\" /><input type=\"submit\" value=\"Lagre linje\" /></td>";

		$this->totalHours += $totalAmount;
		$this->totalMoney += $totalMoney;

		return( $retVal );
	}
}
?>
