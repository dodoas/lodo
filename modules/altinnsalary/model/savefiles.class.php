<?php

class altinn_file {
  function __construct($zipContent) {
    global $_SETUP;
    $this->zipContent = $zipContent;
    // change this path to somewhere outside repo
    $this->folderPath = $_SETUP['HOME_DIR']."/".(string) time();
  }

  function extract() {
    global $_lib;
    // var_dump($this->folderPath);

    // make folder to work in
    exec("mkdir ".$this->folderPath);

    // Save the ziped file to disk
    $file = fopen($this->folderPath . "/tilbakemelding.zip", "w");
    fwrite($file, $this->zipContent);
    fclose($file);

    // unzip the file. Need -d(esitination folder) otherwise it will
    // will create them in in current folder. I this case '~/'.
    exec("unzip ".$this->folderPath. "/tilbakemelding.zip -d ".$this->folderPath);

    // Read the xml from file
    $file = fopen($this->folderPath . "/tilbakemelding.xml", "r");
    $xmlContent = fread($file,filesize($this->folderPath . "/tilbakemelding.xml"));
    fclose($file);

    return $xmlContent;
  }


};
