<?php
class altinn_file {
  function __construct( $folder = false) {
    global $_SETUP;
    if (!$folder)
      $folder = (string) time();

    // TODO change this path to somewhere outside repo
    $this->folderPath = $_SETUP['HOME_DIR']."/".$folder;
  }

  function save($content) {
    global $_lib;
    // make folder to work in
    self::makeFolder($this->folderPath);

    // write file to disk
    return self::writeFile("/A-melding.xml", $content);
  }

  function extract($zipContent) {
    global $_lib;
    // make folder to work in
    self::makeFolder($this->folderPath);

    // Save the ziped file to disk
    $file = self::writeFile("/tilbakemelding.zip", $zipContent);

    // unzip the file. Need -d(esitination folder) otherwise it will
    // will create them in in current folder. I this case '~/'.
    exec("unzip ".$this->folderPath. "/tilbakemelding.zip -d ".$this->folderPath);

    // Read the xml from file
    $xmlContent = self::readFile("/tilbakemelding.xml");
    return $xmlContent;
  }

  function makeFolder($name){
    exec("mkdir -p ".$name);
  }

  function readFile($file_name){
    $file = fopen($this->folderPath . $file_name, "r");
    $content = fread($file,filesize($this->folderPath.$file_name));
    fclose($file);
    return $content;
  }

  function writeFile($file_name, $content){
    $file = fopen($this->folderPath . $file_name, "w");
    fwrite($file, $content);
    fclose($file);
    return true;
  }
}
