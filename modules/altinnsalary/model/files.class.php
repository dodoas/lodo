<?php
class altinn_file {
  function __construct($date) {
    global $_SETUP;

    // TODO change this path to somewhere outside repo
    $this->folderPath = $_SETUP['HOME_DIR']."/altinn_xml/".$_SETUP['DB_NAME'].'/'.$date.'/';
  }

  function save($content, $id) {
    global $_lib;
    // make folder to work in
    self::makeFolder($this->folderPath);

    // write file to disk
    return self::writeFile('req_'.$id.'.xml', $content);
  }

  function extract($zipContent, $id) {
    global $_lib;
    // make folder to work in
    self::makeFolder($this->folderPath);

    // Save the ziped file to disk
    $file = self::writeFile("tilbakemelding".$id.".zip", $zipContent);

    // unzip the file. Need -d(esitination folder) otherwise it will
    // will create them in in current folder. I this case '~/'.
    exec("unzip ".$this->folderPath. "tilbakemelding".$id.".zip -d ".$this->folderPath);
    exec("mv ".$this->folderPath. "tilbakemelding.xml ". $this->folderPath . 'tilbakemelding'.$id.".xml");

    // Read the xml from file
    $xmlContent = self::readFile("tilbakemelding".$id.".xml");
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
