<?
if(!$_SESSION['css'])
{
  $_SESSION['css'] = "default";
}
$DEFAULT_ACCOUNT_TYPE = 'normal';
// $MIN_SESSION_LENGTH = 30000000; //Minimum session length is set to be 5 sec.
$_SETUP['SESS_LENGTH'] = 900;

$current_time = time();
$account_type = $_lib['sess']->get_companydef('account_type') or $DEFAULT_ACCOUNT_TYPE;
// $session_length = ( $current_time - $_SESSION['StartTS'] < $_SETUP['SESS_LENGTH'] ) ? $_SETUP['SESS_LENGTH'] - ( $current_time - $_SESSION['StartTS'] ) : $MIN_SESSION_LENGTH;
if (($_SETUP['SESS_LENGTH'] - ( $current_time - $_SESSION['StartTS'])) < 0) {
    session_start();
    $_SESSION['StartTS'] = $current_time;
}
$session_length = $_SETUP['SESS_LENGTH'] - ( $current_time - $_SESSION['StartTS'] );
if ( $account_type == $DEFAULT_ACCOUNT_TYPE ) {
    $protocol_string = 'http://';
    if ( $_SERVER['HTTPS'] ) {
        $protocol_string = 'https://';
    }
    echo '<meta http-equiv="refresh" content="' . $session_length . ';URL=' . $protocol_string . $_SERVER['SERVER_NAME'] . '/redirect.php" />';
}
?>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <meta name="author"             content="Thomas Ekdahl, thomas@ekdahl.no, http://www.ekdahl.no/" />
    <meta name="copyright"          content="Thomas Ekdahl, 1994-2004" />
    <meta name="technology"         content="Apache, PHP, MySQL, Perl" />
    <meta name="generator"          content="BBEdit 7.1"               />

    <link rel="stylesheet"              title="Default" href="/css/default_lodo.css" media="screen" type="text/css" />
    <link rel="stylesheet"              title="Default" href="/css/lodo_print.css" media="print" type="text/css" />

    <link rel="icon"                href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon"       href="favicon.ico" type="image/x-icon" />

    <script type="text/javascript"  src="/lib/tigra_calendar/calendar1.js"></script>
    <script type="text/javascript"  src='/lib/js/jquery.js'></script>
    <style  type="text/css">@import url(/lib/htmlarea3/htmlarea.css)</style>
<?php if($account_type == $DEFAULT_ACCOUNT_TYPE): ?>
    <script type="text/javascript">
        $(document).ready(function(){
            var seconds = parseInt($('meta[http-equiv="refresh"]').attr('content').split(';')[0]);
            document.getElementById('log_out_timer').innerHTML = timer(seconds);
            setInterval(function(){
                seconds = seconds - 1;
                document.getElementById('log_out_timer').innerHTML = timer(seconds);
            }, 1000);
        });

        function timer(time){
            var minutes = "0" + Math.floor(time / 60);
            var seconds = "0" + (time - minutes * 60);
            return minutes.substr(-2) + ":" + seconds.substr(-2);
        }
    </script>
<? endif ?>
    <script type="text/javascript">
      $(document).ready(function() {
        // On each window focus, check if db changed by comparing the value we
        // got on page load and the one saved to local storage on login are the same.
        // If they are not we redirect to main page for the currently logged in company.
        // NOTE! This happens on every page!
        $(window).focus(function() {
          // console.log('\n\n--\ncheck db_name');
          var site_home_url = '<?= $_lib['sess']->dispatchs; ?>t=lodo.main';
          var db_name_on_page_load = '<?= $_SESSION['DB_NAME']; ?>';
          var db_name_local = localStorage.getItem('lodo_db_name');
          // console.log('localStorage: ' + db_name_local);
          // console.log('onpageload:' + db_name_on_page_load);
          if (db_name_on_page_load != db_name_local) {
            // console.log('redirect to ' + site_home_url);
            window.location = site_home_url;
          }
        });
      });
    </script>
