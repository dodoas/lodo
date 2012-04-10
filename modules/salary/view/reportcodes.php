<?php

$codes_query = "SELECT SalaryCode FROM salaryconfline WHERE SalaryConfID = 1";
$codes_result = $_lib['db']->db_query($codes_query);
$codes = array('000');
while($codes_row = $_lib['db']->db_fetch_assoc($codes_result)) {
    if($codes_row['SalaryCode'] != '' && !in_array($codes_row['SalaryCode'], $codes)) 
        $codes[] = $codes_row['SalaryCode'];
}

sort($codes);

?>
