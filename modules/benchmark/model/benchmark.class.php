<?

// Bencmarking class
//
// Usage:
//
// includelogic('benchmark/benchmark')
//
// $bm = new benchmark("benchmark title"); // create new benchmark object
// $bm->start("benchmark start message");  // start the bencmark
// $bm->add("checkpoint message");         // add as many checkpoints as needed
// $bm->add("checkpoint message", "group");// add checkpoints with a group
// $bm->stop("checkpoint message");        // stop the benchmark
// $bm->var_dump_benchmark();              // print the results

class benchmark {

  public $_title = '';
  public $_timings = array();
  public $_start_time = 0.0;
  public $_last_time = 0.0;
  public $_stop_time = 0.0;
  public $_in_groups = array();

  function __construct($title = '') {
    $this->_title = $title;
    $this->_timings = array();
    $this->_start_time = 0.0;
    $this->_last_time = 0.0;
    $this->_stop_time = 0.0;
  }

  function start($message = '', $group = 'ungrouped') {
    $this->_start_time = microtime(true);
    $this->_last_time = $this->_start_time;
    $this->_timings[] = array(
                        'time_from_start' => 0,
                        'time_from_last' => 0,
                        'message' => $message,
                        'group' => $group
                       );
  }

  function add($message, $group = 'ungrouped') {
    $time_now = microtime(true);
    $time_from_start = self::time_from_start($time_now);
    $time_from_last = self::time_from_last($time_now);
    $this->_last_time = $time_now;
    $this->add_to_group($group, $time_from_last);
    $this->_timings[] = array(
                        'time_from_start' => $time_from_start,
                        'time_from_last' => $time_from_last,
                        'message' => $message,
                        'group' => $group
                       );
  }

  function add_to_group($group, $time) {
    if (!isset($this->_in_groups[$group]))
      $this->_in_groups[$group] = 0;

    $this->_in_groups[$group] += $time;
  }

  function time_from_start($timestamp) {
    return $timestamp - $this->_start_time;
  }

  function time_from_last($timestamp) {
    return $timestamp - $this->_last_time;
  }

  function stop($message = '', $group = 'ungrouped') {
    self::add($message, $group);
    $this->_stop_time = $this->_last_time;
  }

  function var_dump_benchmark() {
    echo("BENCHMARK DUMP FOR: ". $this->_title);
    echo "<br />";
    echo("STARTED: ". sprintf('%012.23f', $this->_start_time));
    echo "<br />";
    $total_time = $this->_stop_time - $this->_start_time;
    foreach ($this->_timings as $timing) {
      $percent = ($timing['time_from_last'] / $total_time) * 100.0;
      echo("STARTED: +". sprintf('%012.23f', $timing['time_from_start']) ." LASTED: ". sprintf('%012.23f', $timing['time_from_last']) ." PERCENTAGE OF TOTAL: ". sprintf('%02.4f', $percent) ."% MESSAGE: ". $timing['message'] ." GROUP: ". $timing['group']);
      echo "<br />";
    }
    echo("STOPPED: ". sprintf('%012.23f', $this->_stop_time));
    echo "<br />";
    echo("BENCHMARK LASTED: ". sprintf('%04.23f', $total_time));
    echo "<br />";

    // print for group
    $total_group_time = 0.0;
    foreach ($this->_in_groups as $group => $time ) {
      $total_group_time += $time;
    }
    echo "<br />";
    echo("BENCHMARK groups: total time". sprintf('%04.23f', $total_group_time));
    echo "<br />";
    foreach ($this->_in_groups as $group => $time ) {
      // var_dump($time);
      $percent = ($time / $total_group_time) * 100.0;
      echo("STARTED: ". $group ." LASTED: ". sprintf('%012.23f', $time) ." PERCENTAGE OF TOTAL: ". sprintf('%02.4f', $percent) ."%");
      echo "<br />";
    }
  }
}

?>
