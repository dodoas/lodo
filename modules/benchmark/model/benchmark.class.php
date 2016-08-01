<?

// Bencmarking class
//
// Usage:
//
// $bm = new benchmark("benchmark title"); // create new benchmark object
// $bm->start("benchmark start message");  // start the bencmark
// $bm->add("checkpoint message");         // add as many checkpoints as needed
// $bm->stop("checkpoint message");        // stop the benchmark
// $bm->var_dump_benchmark();              // print the results

class benchmark {

  public $_title = '';
  public $_timings = array();
  public $_start_time = 0.0;
  public $_last_time = 0.0;
  public $_stop_time = 0.0;

  function __construct($title = '') {
    $this->_title = $title;
    $this->_timings = array();
    $this->_start_time = 0.0;
    $this->_last_time = 0.0;
    $this->_stop_time = 0.0;
  }

  function start($message = '') {
    $this->_start_time = microtime(true);
    $this->_last_time = $this->_start_time;
    $this->_timings[] = array(
                        'time_from_start' => 0,
                        'time_from_last' => 0,
                        'message' => $message
                       );
  }

  function add($message) {
    $time_now = microtime(true);
    $time_from_start = self::time_from_start($time_now);
    $time_from_last = self::time_from_last($time_now);
    $this->_last_time = $time_now;
    $this->_timings[] = array(
                        'time_from_start' => $time_from_start,
                        'time_from_last' => $time_from_last,
                        'message' => $message
                       );
  }

  function time_from_start($timestamp) {
    return $timestamp - $this->_start_time;
  }

  function time_from_last($timestamp) {
    return $timestamp - $this->_last_time;
  }

  function stop($message = '') {
    self::add($message);
    $this->_stop_time = $this->_last_time;
  }

  function var_dump_benchmark() {
    var_dump("BENCHMARK DUMP FOR: ". $this->_title);
    var_dump("STARTED: ". sprintf('%012.23f', $this->_start_time));
    $total_time = $this->_stop_time - $this->_start_time;
    foreach ($this->_timings as $timing) {
      $percent = ($timing['time_from_last'] / $total_time) * 100.0;
      var_dump("STARTED: +". sprintf('%012.23f', $timing['time_from_start']) ." LASTED: ". sprintf('%012.23f', $timing['time_from_last']) ." PERCENTAGE OF TOTAL: ". sprintf('%02.4f', $percent) ."% MESSAGE: ". $timing['message']);
    }
    var_dump("STOPPED: ". sprintf('%012.23f', $this->_stop_time));
    var_dump("BENCHMARK LASTED: ". sprintf('%04.23f', $total_time));
  }
}

?>
