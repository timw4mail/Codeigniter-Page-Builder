<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

define('MIN_SAFE', 1);
define('MIN_EXTREME', 2);
define('MIN_EXTREME_COMMENTS', 4);

// ------------------------------------------------------------------------

/**
 * Output Class
 *
 * Responsible for sending final output to browser
 *
 * @package   CodeIgniter
 * @subpackage  Libraries
 * @category  Output
 * @author    ExpressionEngine Dev Team
 * @link    http://codeigniter.com/user_guide/libraries/output.html
 */
class MY_Output extends CI_Output
{
	var $final_output;
	var $cache_expiration = 0;
	var $headers = array();
	var $enable_profiler = FALSE;
	var $min = 0;
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Output
	 *
	 * All "view" data is automatically put into this variable by the controller class:
	 *
	 * $this->final_output
	 *
	 * This function sends the finalized output data to the browser along
	 * with any server headers and profile data.  It also stops the
	 * benchmark timer so the page rendering speed and memory usage can be shown.
	 *
	 * @access  public
	 * @return  mixed
	 */
	function _display($output = '')
	{
		// Note:  We use globals because we can't use $CI =& get_instance()
		// since this function is sometimes called by the caching mechanism,
		// which happens before the CI super object is available.
		global $BM, $CFG;
		//$this->min = 0;
		
		// --------------------------------------------------------------------
		
		// Set the output data
		if ($output == '')
		{
			$output =& $this->final_output;
		}
		
		// --------------------------------------------------------------------
		
		// Do we need to write a cache file?
		if ($this->cache_expiration > 0)
		{
			$this->_write_cache($output);
		}
		
		// --------------------------------------------------------------------
		
		// Parse out the elapsed time and memory usage,
		// then swap the pseudo-variables with the data
		
		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');
		$output  = str_replace('{elapsed_time}', $elapsed, $output);
		
		$memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage() / 1024 / 1024, 2) . 'MB';
		$output = str_replace('{memory_usage}', $memory, $output);
		
		// --------------------------------------------------------------------
		
		// Is compression requested?
		if ($CFG->item('compress_output') === TRUE)
		{
			if (extension_loaded('zlib'))
			{
				if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
				{
					ob_start('ob_gzhandler');
				}
			}
		}
		
		// --------------------------------------------------------------------
		
		// Are there any server headers to send?
		if (count($this->headers) > 0)
		{
			foreach ($this->headers as $header)
			{
				header($header[0], $header[1]);
			}
		}
		
		// --------------------------------------------------------------------
		
		// Does the get_instance() function exist?
		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if (!function_exists('get_instance'))
		{
			echo $output;
			log_message('debug', "Final output sent to browser");
			log_message('debug', "Total execution time: " . $elapsed);
			return TRUE;
		}
		
		// --------------------------------------------------------------------
		
		// Grab the super object.  We'll need it in a moment…
		$CI =& get_instance();
		
		// Do we need to generate profile data?
		// If so, load the Profile class and run it.
		if ($this->enable_profiler == TRUE)
		{
			$CI->load->library('profiler');
			
			// If the output data contains closing </body> and </html> tags
			// we will remove them and add them back after we insert the profile data
			if (preg_match("|</body>.*?</html>|is", $output))
			{
				$output = preg_replace("|</body>.*?</html>|is", '', $output);
				$output .= $CI->profiler->run();
				$output .= '</body></html>';
			}
			else
			{
				$output .= $CI->profiler->run();
			}
		}
		
		//Let's minify!
		switch ((int) $this->min)
		{
			case 0:
				//Don't minify
				break;
			
			case 1: //Safe Minify
				$output = preg_replace("`>\s+<`", "> <", $output);
				break;
			
			case 2: //Extreme Minify
				$output = preg_replace('/<!--[^\[](.*)-->/Uis', '', $output);
				$output = preg_replace("`\s+`", " ", $output);
				$output = preg_replace("`> <`", "><", $output);
				$output = str_replace("</a><a", "</a> <a", $output);
				$output = preg_replace("`(<img(.*?)/>)`", " <img$2/> ", $output);
				break;
			
			case 4: //Extreme minify, save comments
				$output = preg_replace("`\s+`", " ", $output);
				$output = preg_replace("`> <`", "><", $output);
				$output = str_replace("</a><a", "</a> <a", $output);
				$output = preg_replace("`(<img(.*?)/>)`", " <img$2/> ", $output);
				break;
			
			default:
				//Don't minify
				break;
		}
		
		//Replace common entities with more compatible versions
		$replace = array(
			'"&"' => '"&amp;"',
			'{NL}' => " ",
			'&nbsp;' => '&#160;',
			'&copy;' => '&#169;',
			'&acirc;' => '&#226;',
			'&cent;' => '&#162;',
			'&raquo;' => '&#187;',
			'&laquo;' => '&#171;'
		);
		
		$output = strtr($output, $replace);
		
		// --------------------------------------------------------------------
		
		// Does the controller contain a function named _output()?
		// If so send the output there.  Otherwise, echo it.
		if (method_exists($CI, '_output'))
		{
			$CI->_output($output);
		}
		else
		{
			echo $output; // Send it to the browser!
		}
		
		log_message('debug', "Final output sent to browser");
		log_message('debug', "Total execution time: " . $elapsed);
	}
	
	/**
	 * Enable/disable Minified HTML
	 *
	 * @access  public
	 * @param int
	 * @return  void
	 */
	function enable_min($val = 0)
	{
		$this->min = $val;
	}
	
}
// END Output Class

/* End of file Output.php */
/* Location: ./system/libraries/Output.php */