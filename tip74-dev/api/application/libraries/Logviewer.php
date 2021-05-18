<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CI Logviewer - CodeIgniter Log Viewer Library
 *
 * A simple Log Viewer Library for CodeIgniter 3
 */
class Logviewer {

	/**
	 * CI instance
	 */
	protected $CI;

	/**
	 * Constructor
	 *
	 * Get the CI instance
	 * Set the default show/hide notices option
	 * Set the default date format
	 * Set the log folder path
	 */
	public function __construct()
	{
		// assign the CodeIgniter object
		$this->CI =& get_instance();

		// load the directory helper
		$this->CI->load->helper('directory');

		// hide notices logs
		$this->show_notices = true;

		// date format on select (PHP date)
		$this->date_format = 'd F Y';

		// set the log folder path
		$this->log_path = ( !empty($this->config->config['log_path']) ) ? $this->config->config['log_path'] : APPPATH.'logs/';
	}

	public function get_logs()
	{
		// get the list of log files
		$this->logs = $this->get_logs_list();

		$data = array();

		// the list of log files
		$data['logs'] = $this->logs;

		// selected log data
		$data['log'] = $this->get_log_data();

		// get the log threshold from the config file
		$data['log_threshold'] = $this->CI->config->config['log_threshold'];

		// the selected log
		if ( !empty($this->selected_log) ) $data['selected'] = $this->selected_log;

		// the controller url
		$data['url'] = $this->CI->config->config['base_url'].'index.php/'.$this->CI->uri->uri_string();

		return $data;
	}

	/**
	 * Get log data
	 *
	 * Check if there is $_GET['log_date'] otherwise load the last log
	 *
	 * Return an array containing:
	 * - the last selected log
	 *
	 * @return array $data
	 */
	public function get_log_data()
	{
		$data = array();

		// get the log from $_GET['log_date'] or the last log in the folder
		if ( !empty($this->CI->input->get('log_date')) ) {

			if ( array_key_exists($this->CI->input->get('log_date'), $this->logs) ) {

				$log_file = 'log-'.$this->CI->input->get('log_date').'.php';
				$log_date = $this->CI->input->get('log_date');

			} else {

				if ( !empty($this->last_log_file) && !empty($this->last_log_date) ) {

					$log_file = $this->last_log_file;
					$log_date = $this->last_log_date;

				}

			}

		} else {

			if ( !empty($this->last_log_file) && !empty($this->last_log_date) ) {

				$log_file = $this->last_log_file;
				$log_date = $this->last_log_date;

			}

		}

		// check if the log file is empty
		if ( !empty($log_file) && !empty($log_file) && !empty($log_date) ) {

			// set the selected log date
			$this->selected_log = $log_date;

			// load the log file content
			$log_content = file_get_contents($this->log_path.$log_file);

			// exlplode log lines
			$rows = explode("\n", $log_content);

			// remove the first line
			array_shift($rows);

			// sort the log by date DESC
			krsort($rows);

			// read all the log rows
			foreach($rows as $r => $row) {

				// checks if the row is not empty and if the string starts with error
				if ( !empty($row) && substr($row, 0, 5) === 'ERROR' ) {

					// parse the log row
					$log = $this->parse_log_row($row);

					if ( !empty($log) ) {

						// add the parsed log to the array
						array_push($data, $log);

					}

				}

			}

		}

		return $data;

	}

	/**
	 * Parse log row
	 *
	 * parse a logfile row and return an array with date, severity and error
	 *
	 * @param string $row
	 * @return array $data
	 */
	private function parse_log_row($row)
	{
		$data = array();

		// explode the log row
		$log_exp = explode(' --> ', $row);

		// The first part contains the log severity
		$severity = $log_exp[1];

		// The second part contains the error (not always)
		$error = ( !empty($log_exp[2]) ) ? $log_exp[2] : null;

		// sets the severity error type and the error
		if ( !empty($severity) ) {

			// Error
			if (strpos($severity, 'Error') !== false) {

				$data['severity'] = 'Error';
				$data['error'] = $error;

			// Query error
			} elseif (strpos($severity, 'Query error') !== false) {

				$data['severity'] = 'Error';
				$data['error'] = $log_exp[1];

			// Warning
			} elseif (strpos($severity, 'Warning') !== false) {

				$data['severity'] = 'Warning';
				$data['error'] = $error;

			// Notice
			} elseif (strpos($severity, 'Notice') !== false ) {

				if ( $this->show_notices == true ) {

					$data['severity'] = 'Notice';
					$data['error'] = $error;

				}

			// Unknown error
			} else {

				$data['severity'] = 'Error';
				$data['error'] = $log_exp[1];

				if ( !empty($log_exp[2]) ) {

					$data['error'].= ' - '.$log_exp[2];

				}

			}

			// set the log time
			if ( !empty($data['error']) && !empty($data['severity']) ) $data['time'] = substr($log_exp[0], 19);

		}

		if ( !empty($data) ) return $data;

	}

	/**
	 * Parse log directory
	 *
	 * get the list of CI log files
	 *
	 * @return array $logs
	 */
	private function parse_log_directory()
	{

		// get the log folder map array
		$dir = directory_map($this->log_path);

		if ( !empty($dir) ) {

			$logs = array();

			// order the array by date DESC
			krsort($dir);

			// extract all CI logs files
			foreach ($dir as $file) {

				// it might be some other log file
				// this make sure that we are getting CI generated logs only
				if (strpos($file, 'log-') !== false) {

					array_push($logs, $file);

				}

			}

		}

		return $logs;

	}

	/**
	 * Parse logs list
	 *
	 * get the list of logs, the last log filename and last log date
	 *
	 * @return array $data
	 */
	private function get_logs_list()
	{
		$data = array();

		// Get an array list of CI logs
		$log_files = $this->parse_log_directory();

		if (!empty($log_files)) {

			$i = 1;

			foreach ($log_files as $file) {

				// explode the log file name to get the date
				$file_exp = explode('-', $file);

				// remove the extension from the last value
				$file_exp[3] = str_replace('.php', '', $file_exp[3]);

				// the select key
				$key = $file_exp[1].'-'.$file_exp[2].'-'.$file_exp[3];

				// the select value
				$value = date($this->date_format, mktime(10, 0, 0, $file_exp[2], $file_exp[3], $file_exp[1]));

				// add the log file to the select
				$data[$key] = $value;

				// get the last log file and date
				if ($i == 1) {

					// last log file
					$this->last_log_file = $file;

					// last log date
					$this->last_log_date = $key;

				}

				$i = ($i + 1);

			}

		}

		return $data;

	}


}
