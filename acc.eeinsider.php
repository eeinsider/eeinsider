<?php 
/**
 * An accessory for EE Insider News & Tips
 *
 * @package EE Insider
 * @author Kenny Meyers <kenny.meyers@gmail.com>
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
		$this->sections["Latest News"] = $this->get_rss("http://feeds.feedburner.com/eeinsider?format=xml");
		$this->sections["Latest Tips"] = $this->get_rss("http://eeinsider.com/tips/rss");
		$this->sections["Buy The Book"] = $this->EE->load->view('ad.html', array(), TRUE);
	}

	/**
		* Gets the RSS Feed for EE Insider's Posts
		*
		* @param     string the feed url
		* @return    string unordered list of entries with links
		*
		*/
	private function get_rss($url)
	{
		$xml = $this->get_feed($url);
		$vars = $this->get_list($xml);
		$stories = $this->EE->load->view('list.html', $vars, TRUE);
		return $stories;
	}
	
	/**
	 * Takes an xml file and just builds a simple array of title
	 * and link
	 *
	 * @param 	string [xml] CURL XML
	 * @return	array list for view
	 *
	*/
	private function get_list($xml)
	{
		$rss = new SimpleXMLElement($xml);
		$vars["list"] = array(); // Creates the list array
		$counter = 0; 
		foreach ($rss->channel->item as $entry)
		{
			$vars["list"][] = array(
				"link" => (string) $entry->link,
				"title" => (string) $entry->title,
			);
			$counter += 1; 
			if($counter > 5)
			{
				break;
			}
		}
		return $vars;
	}

	/**
	 * A really simple curl function for getting an RSS feed
	 *
	 * @param 	string [url] the feed url
	 * @return	string the xml
	 *
	*/
	private function get_feed($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}
// END CLASS