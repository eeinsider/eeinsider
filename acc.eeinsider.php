<?php 
/**
 * An accessory for EE Insider News & Tips
 *
 * @package EE Insider
 * @author Kenny Meyers <kenny.meyers@gmail.com>, Ryan Irelan <ryan@mijingo.com
 * @copyright 2009 (C) EE Insider
 * @date_created Mon Dec 14 01:18:55 PST 2009
 *
*/
class EEInsider_acc {

	var $name		= 'EE Insider';
	var $id			= 'eeinsider';
	var $version		= '1.0';
	var $description	= 'Learn ExpressionEngine from Tips, Tutorials and Training Videos';
	var $sections		= array();

	/**
		* Constructor
		*/
	function __construct()
	{
		$this->EE =& get_instance();
	}

	/**
		* Set sections
		*
		* Sets the content for the accessory
		*
		* @access public
		* @return void
		*/
	public function set_sections()
	{
		$this->sections["Latest News"] = $this->get_rss("http://feeds.feedburner.com/eeinsider?format=xml", array(
			"url" => "http://eeinsider.com/index.php?utm_source=eeinsider_accessory&utm_medium=addon&utm_campaign=EE%2BInsider%20Accesory",
			"title" => "Visit EE Insider"
		));
		$this->sections["Latest Tips"] = $this->get_rss("http://eeinsider.com/tips/rss", array(
			"url" => "http://eeinsider.com/tips?utm_source=eeinsider_accessory&utm_medium=addon&utm_campaign=EE%2BInsider%20Accesory",
			"title" => "Add a tip",
		));
		$this->sections["Buy The Book"] = $this->EE->load->view('ad.html', array(), TRUE);
	}

	/**
		* Gets the RSS Feed for EE Insider's Posts
		*
		* @param     string the feed url
		* @return    string unordered list of entries with links
		*
		*/
	private function get_rss($url, $external_link = array())
	{
		$vars = $this->get_list($url);
		
		if(isset($external_link))
		{
			$vars['title'] = $external_link["title"];
			$vars['url'] = $external_link["url"] ;
		}
		
		$stories = $this->EE->load->view('list.html', $vars, TRUE);
		return $stories;
	}
	
	/**
	 * Takes an rss feed and just builds a simple array of title
	 * and link with Magpie
	 * 
	 * My thanks to the EE team for putting together their accessory with Magpie
	 * so that I can make mine. That accessory in question is news_and_stats
	 * and comes default with EE. Thanks guys! Except for, of course, Derek Allard,
	 * who as we all know, is a jerk.
	 *
	 * @param 	string [url] RSS Feed URL
	 * @return	array list for view
	 *
	*/
	private function get_list($url)
	{
		// Check to see if the Magpie plugin exists
		if ( ! file_exists(PATH_PI.'pi.magpie'.EXT))
		{
			return '';
		}
		
		// Sets the cache to 3 hours
		if ( ! defined('MAGPIE_CACHE_AGE'))
		{
			define('MAGPIE_CACHE_AGE', 60*60*3); // set cache to 3 hours			
		}
		
		// Sets the cache directory appropriately
		if ( ! defined('MAGPIE_CACHE_DIR'))
		{
			define('MAGPIE_CACHE_DIR', APPPATH.'cache/magpie_cache/');			
		}
		
		//Turns off Magpie debugging: for her pleasure
		if ( ! defined('MAGPIE_DEBUG'))
		{
			define('MAGPIE_DEBUG', 0);
		}
		
		// Checks the magpie file to make sure the class exists.
		if ( ! class_exists('Magpie'))
		{
			require PATH_PI.'pi.magpie'.EXT;
		}
		$feed = fetch_rss($url); // Uses magpie plugins RSS function
		$vars["list"] = array(); // Creates the list array
		
		$counter = 0;
		
		foreach ($feed->items as $entry)
		{
			$vars["list"][] = array(
				"link" => (string) $entry["link"],
				"title" => (string) $entry["title"],
			);
			$counter += 1; 
			// Limits it to 5
			if($counter > 5)
			{
				break;
			}
		}
		return $vars;
	}

}
// END CLASS