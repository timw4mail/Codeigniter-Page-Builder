<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * Class for building pages
 *
 * All methods are chainable, with the exception of the constructor,
 * build_header(), build_footer(), build_page() and _headers() methods.
 */
class Page
{
	private static $meta, $head_js, $foot_js, $css, $title, $head_tags, $body_class, $body_id, $base;
	private $CI;
	
	public function __construct()
	{
		$this->meta       = "";
		$this->head_js    = "";
		$this->foot_js    = "";
		$this->css        = "";
		$this->title      = "";
		$this->head_tags  = "";
		$this->body_class = "";
		$this->body_id    = "";
		$this->base       = "";
		$this->CI =& get_instance();
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets server headers and doctype
	 *
	 * Also sets page mime type, based on if sent as
	 * html or xhtml, and what the target browser
	 * supports
	 *
	 * @param bool $xhtml
	 * @param bool $html5
	 * @return Page
	 */
	private function _headers($xhtml, $html5)
	{
		$this->CI->output->set_header("Cache-Control: must-revalidate, public");
		
		$this->CI->output->set_header("Vary: Accept");
		$mime = "";
		
		//Variable for accept keyword
		$accept = (!empty($_SERVER['HTTP_ACCEPT'])) ? $_SERVER['HTTP_ACCEPT'] : "";
		
		//Predefine doctype
		$doctype_string = ($html5 == TRUE) ? doctype('html5') : doctype('xhtml11');
		
		//Predefine charset
		$charset = "UTF-8";
		
		//If xhtml flag is false, set html5 header
		if ($xhtml == TRUE)
		{
			//Check that the user agent accepts application/xhtml+xml, or if it's the W3C Validator
			if (stristr($accept, "application/xhtml+xml") || stristr($_SERVER["HTTP_USER_AGENT"], "W3C_Validator"))
			{
				$mime = "application/xhtml+xml";
			}
			//Or if it supports application/xml
			else if (stristr($accept, "application/xml"))
			{
				$mime = "application/xml";
			}
			//Or if it supports text/xml
			else if (stristr($accept, "text/xml"))
			{
				$mime = "text/xml";
			}
			else //Otherwise, it's tag soup
			{
				$mime = "text/html";
				
				if ($html5 == FALSE) //If it's not HTML5, it's HTML4
				{
					$doctype_string = doctype('html4-strict');
				}
			}
		}
		else
		{
			$mime = "text/html";
			
			if ($html5 == FALSE)
			{
				$doctype_string = doctype('html4-strict');
			}
		}
		
		// set the doctype according to the mime type which was determined
		if ($mime == "application/xhtml+xml" || $mime == "text/xml" || $mime == "application/xml")
		{
			if ($html5 == TRUE)
			{
				$doctype_string = '';
			}
			
			$doctype_string = "<?xml version='1.0' encoding='$charset' ?>\n" 
				. $doctype_string
				. "\n<html xmlns='http://www.w3.org/1999/xhtml'" . " xml:lang='en'>";
		}
		else
		{
			$doctype_string .= "\n<html lang='en'>";
		}
		
		// finally, output the mime type and prolog type
		$this->CI->output->set_header("Content-Type: $mime;charset=$charset");
		$this->CI->output->set_header("X-UA-Compatible: chrome=1, IE=edge");
		$this->CI->output->set_output($doctype_string);
		
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Set Meta
	 *
	 * Sets meta tags, with codeigniter native meta tag helper
	 * 
	 * @param array $meta
	 * @return Page
	 */
	public function set_meta($meta)
	{
		$this->meta .= meta($meta);
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets minified javascript group in header
	 * @param string $group
	 * @param bool $debug
	 * @return Page
	 */
	public function set_head_js_group($group, $debug = FALSE)
	{
		if ($group === FALSE)
		{
			return $this;
		}
		
		$file = $this->CI->config->item('group_js_path') . $group;
		$file .= ($debug == TRUE) ? "/debug/1" : "";
		$this->head_js .= $this->script_tag($file, FALSE);
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Set an individual js file in header
	 * @param string $js
	 * @param bool $domain
	 * @return Page 
	 */
	public function set_head_js($js, $domain = TRUE)
	{
		$this->head_js .= $this->script_tag($js, $domain);
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets a minified css group
	 * @param string $group
	 * @return Page
	 */
	public function set_css_group($group)
	{
		$link = array(
			'href' => $this->CI->config->item('group_style_path') . $group,
			'rel' => 'stylesheet',
			'type' => 'text/css'
		);
		$this->css .= link_tag($link);
		
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets a minified javascript group for the page footer
	 * @param string $group
	 * @return Page
	 */
	public function set_foot_js_group($group, $debug = FALSE)
	{
		$file = $this->CI->config->item('group_js_path') . $group;
		$file .= ($debug == TRUE) ? "/debug/1" : "";
		$this->foot_js .= $this->script_tag($file, FALSE);
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets js in footer; multiple files are combined and minified.
	 * @param array $args
	 * @return Page
	 */
	public function set_foot_js($js, $domain)
	{
		$this->foot_js .= $this->script_tag($js, $domain);
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets html title string
	 * @param string $title
	 * @return Page
	 */
	public function set_title($title = "")
	{
		$title = ($title == "") ? $this->CI->config->item('default_title') : $title;
		
		$this->title = $title;
		
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets custom body class
	 * @param string $class
	 * @return Page
	 */
	public function set_body_class($class = "")
	{
		$this->body_class = $class;
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets custom body id
	 * @param string $id
	 * @return Page
	 */
	public function set_body_id($id = "")
	{
		$this->body_id = $id;
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets custom base href
	 * @param string href
	 * @return Page
	 */
	public function set_base($href)
	{
		$this->base = $href;
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets custom css tags
	 * @param string $name
	 * @param string $media
	 * @return Page
	 */
	public function set_css_tag($name, $domain = TRUE, $media = "all")
	{
		$path     = $this->CI->config->item('content_domain');
		$css_file = $path . "/css/" . $name . ".css";
		
		if ($domain == FALSE)
			$css_file = $name;
		
		$this->css_tags .= link_tag($name, "stylesheet", "text/css", "", $media);
		
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets uncompressed js file in footer
	 * @param string $name
	 * @param bool $domain
	 * @return Page
	 */
	public function set_foot_js_tag($name, $domain = TRUE)
	{
		$this->foot_js .= $this->script_tag($name, $domain);
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets a custom tag in the header
	 * @param string $tag
	 * @return Page
	 */
	public function set_head_tag($tag)
	{
		$this->head_tags .= $tag . "\n";
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Sets custom page header
	 * @param mixed $xhtml
	 * @param bool $html5
	 * @param bool $fbml
	 * @return $this
	 */
	public function build_header($xhtml = FALSE, $html5 = TRUE)
	{
		$data = array();
		
		//Set Meta Tags
		$this->meta   = ($html5 == TRUE) 
			? '<meta charset="utf-8" />'. $this->meta 
			: meta('content-type', 'text/html; charset=utf-8', 'equiv') . $this->meta;
		
		$data['meta'] = $this->meta;
		
		//Set CSS
		if ($this->css != "")
		{
			$data['css'] = $this->css;
		}
		else
		{
			//Set default CSS group
			$this->set_css_group($this->CI->config->item('default_css_group'));
			$data['css'] = $this->css;
		}
		
		//Set head javascript
		if ($this->head_js != "")
		{
			$data['head_js'] = $this->head_js;
		}
		else
		{
			$this->set_head_js_group($this->CI->config->item('default_head_js_group'));
			$data['head_js'] = $this->head_js;
		}
		
		//Set Page Title
		$data['title'] = ($this->title != '') ? $this->title : $this->CI->config->item('default_title');
		
		//Set Body Class
		$data['body_class'] = $this->body_class;
		
		//Set Body Id
		$data['body_id'] = $this->body_id;
		
		//Set Base HREF
		$data['base'] = $this->base;
		
		//Set individual head tags
		$data['head_tags'] = $this->head_tags;
		
		//Set Server Headers and Doctype
		$this->_headers($xhtml, $html5);
		
		//Output Header
		$this->CI->load->view('header', $data);
		
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Builds common footer with any additional js
	 */
	public function build_footer()
	{
		$data = array();
		
		$data['foot_js'] = ($this->foot_js != "") ? $this->foot_js : '';
		
		$this->CI->load->view('footer', $data);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Script Tag
	 *
	 * Helper function for making script tags
	 *
	 * @param string $js
	 * @param bool $domain
	 * @return string
	 */
	private function script_tag($js, $domain = TRUE)
	{
		$path    = $this->CI->config->item('content_domain');
		$js_file = $path . "/js/" . $js . ".js";
		
		if ($domain == FALSE)
			$js_file = $js;
		
		$tag = '<script src="' . $js_file . '"></script>';
		
		return $tag;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Quick Build
	 *
	 * A function to make building pages faster
	 * @param mixed $view
	 * @param mixed $data
	 * @param bool $xhtml
	 * @param bool $html5
	 */
	public function quick_build($view, $data, $xhtml = TRUE, $html5 = TRUE)
	{
		//Set up header
		if ($title != '')
		{
			$this->set_title($title);
		}
		else
		{
			$this->set_title($this->CI->config->item('default_title'));
		}
		
		$this->build_header($xhtml, $html5);
		
		//Load view(s)
		if (is_array($view))
		{
			foreach ($view as $v)
			{
				$this->CI->load->view($v, $data);
			}
		}
		else
		{
			$this->CI->load->view($view, $data);
		}
		
		//Create footer
		$this->build_footer();
	}
	
	
	// --------------------------------------------------------------------------
	
	/**
	 * Num Queries
	 * 
	 * Returns number of queries run on a page
	 * 
	 * @return int
	 */
	public function num_queries()
	{
		return (isset($this->CI->db)) ? count($this->CI->db->queries) : 0;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Set Message
	 *
	 * Adds a message to the page
	 * @param string $type
	 * @param string $message
	 * @return void
	 */
	public function set_message($type, $message)
	{
		$data['stat_class'] = $type;
		$data['message']    = $message;
		
		$this->CI->load->view('message', $data);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Redirect 303
	 *
	 * Shortcut function for 303 redirect
	 * @param string $url
	 */
	function redirect_303($url)
	{
		$this->CI->output->set_header("HTTP/1.1 303 See Other");
		$this->CI->output->set_header("Location:" . $url);
	}
}