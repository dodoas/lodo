<?php
/**
 * XLS parsing uses php-excel-reader from https://github.com/nuovo/spreadsheet-reader
 */
	require('xls/php-excel-reader/excel_reader2.php');
	require('xls/SpreadsheetReader.php');
class lodo_bank_xls {

  private $file_path = NULL;

  public function __construct($file_path) {
    $this->file_path = $file_path;
  }
  public function get_csv_array($skip_before_count = 0, $skip_after_count = 0, $file_type = 'application/vnd.ms-excel') {
    $Filepath = $this->file_path;
    $Sheet_Data = array();
    try {
		  $Spreadsheet = new SpreadsheetReader($Filepath, false, $file_type);
		  $Sheets = $Spreadsheet->Sheets();
		  foreach ($Sheets as $Index => $Name) {
			  $Spreadsheet->ChangeSheet($Index);
        //var_dump($Spreadsheet);
			  foreach ($Spreadsheet as $Key => $Row) {
          $Sheet_Row_Data = array();
          if ($skip_before_count > 0) {
            $skip_before_count--;
          }
          else {
            foreach($Row as $RowKey => $Value) {
              $Sheet_Row_Data[$RowKey] = $Value;
            }
            $Sheet_Data[] = $Sheet_Row_Data;
          }
        }
		  }
      for($i=0;$i<$skip_after_count;$i++) array_pop($Sheet_Data);
      return $Sheet_Data;
	  }
	  catch (Exception $E) {
		  echo $E->getMessage();
    }
  }
}
?>
