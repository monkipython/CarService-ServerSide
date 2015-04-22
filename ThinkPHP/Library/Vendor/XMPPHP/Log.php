<?php
class XMPPHP_Log {
	
	const LEVEL_ERROR   = 0;
	const LEVEL_WARNING = 1;
	const LEVEL_INFO	= 2;
	const LEVEL_DEBUG   = 3;
	const LEVEL_VERBOSE = 4;
	
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var array
	 */
	protected $names = array('ERROR', 'WARNING', 'INFO', 'DEBUG', 'VERBOSE');

	/**
	 * @var integer
	 */
	protected $runlevel;

	/**
	 * @var boolean
	 */
	protected $printout;

	/**
	 * Constructor
	 *
	 * @param boolean $printout
	 * @param string  $runlevel
	 */
	public function __construct($printout = false, $runlevel = self::LEVEL_INFO) {
		$this->printout = (boolean)$printout;
		$this->runlevel = (int)$runlevel;
	}

	/**
	 * @param string  $msg
	 * @param integer $runlevel
	 */
	public function log($msg, $runlevel = self::LEVEL_INFO) {
		$time = time();
		//$this->data[] = array($this->runlevel, $msg, $time);
		if($this->printout and $runlevel <= $this->runlevel) {
			$this->writeLine($msg, $runlevel, $time);
		}
	}

	/**
	 * @param boolean $clear
	 * @param integer $runlevel
	 */
	public function printout($clear = true, $runlevel = null) {
		if($runlevel === null) {
			$runlevel = $this->runlevel;
		}
		foreach($this->data as $data) {
			if($runlevel <= $data[0]) {
				$this->writeLine($data[1], $runlevel, $data[2]);
			}
		}
		if($clear) {
			$this->data = array();
		}
	}
	
	protected function writeLine($msg, $runlevel, $time) {
		//echo date('Y-m-d H:i:s', $time)." [".$this->names[$runlevel]."]: ".$msg."\n";
// 		echo $time." [".$this->names[$runlevel]."]: ".$msg."\n</br>";
		flush();
	}
}
