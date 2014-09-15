<?php
require_once "Mail.php";

class Mailer {

  public $host = "ssl://smtp.googlemail.com";
  public $port = "465";
  public $username = "peacemaker.994@gmail.com";
  public $password = "h5gmailH8O463";

  public function send($from, $to, $subject, $message, $boundary) {
    $headers = array ('From' => $from,
      'MIME-Version' => '1.0',
      'Content-type' => 'multipart/mixed; boundary=' . $boundary,
      'To' => $to,
      'Subject' => $subject);
    $smtp = Mail::factory('smtp',
      array ('host' => $this->host,
        'port' => $this->port,
        'auth' => true,
        'username' => $this->username,
        'password' => $this->password));

    $mail = $smtp->send($to, $headers, $message);

    if (PEAR::isError($mail)) {
      echo("<p>" . $mail->getMessage() . "</p>");
    } else {
      echo("<p>Message successfully sent!</p>");
    }
  }
}
