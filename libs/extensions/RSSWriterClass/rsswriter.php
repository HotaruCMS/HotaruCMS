<?php
/* Publishes content as an RSS feed
  http://snipplr.com/view/23/rss-writer-class/

  E X A M P L E -----------------------------------------------
  $feed = new RSS();
  $feed->title       = "RSS Feed Title";
  $feed->link        = "http://website.com";
  $feed->description = "Recent articles on your website.";

  $db->query($query);
  $result = $db->result;
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
  $item = new RSSItem();
  $item->title = $title;
  $item->link  = $link;
  $item->setPubDate($create_date);
  $item->description = "<![CDATA[ $html ]]>";
  $feed->addItem($item);
  }
  echo $feed->serve();
  ---------------------------------------------------------------- */

class RSS {

	public $title;
	public $rss_link;
	public $link;
	public $description;
	public $language = "en-us";
	public $pubDate;
	public $items = array();
	public $tags = array();

	public function __construct($rss_link) {
		$this->rss_link = (empty($rss_link))? SITEURL.'index.php?page=rss' : $rss_link ;
	}

	public function addItem($item) {
		if (is_array($item)) {
			foreach ($item as $i) {
				array_push($this->items, new RSSItem($i));
			}
		}
		array_push($this->items, $item);
	}

	public function setPubDate($when) {
		$this->pubDate = date("D, d M Y H:i:s O", ((strtotime($when)) ? strtotime($when) : $when));
	}

	public function addTag($tag, $value) {
		$this->tags[$tag] = $value;
	}

	public function out($serve_contentType = FALSE) {

		if (is_string($serve_contentType)) {
			header("Content-type: $serve_contentType");
		} elseif (is_bool($serve_contentType)) {
			header("Content-type: application/xml");
		}

		$out = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$out .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
		$out .= "<channel>\n";
		$out .= "<title>".$this->title."</title>\n";
		$out .= '<atom:link href="'.$this->rss_link.'" rel="self" type="application/rss+xml" />'."\n";
		$out .= "<link>".$this->link."</link>\n";
		$out .= "<description>".$this->description."</description>\n";
		$out .= "<language>".$this->language."</language>\n";
		$out .= "<pubDate>".((empty($this->pubDate)) ? date("D, d M Y H:i:s O") : $this->pubDate)."</pubDate>\n";

                if ($this->tags) {
                    foreach ($this->tags as $key => $val) {
                            $out .= "<$key>$val</$key>\n";
                    }
                }

                if ($this->items) {
                    foreach ($this->items as $item) {
                            if ($item instanceof RSSItem) {
                                    $out .= $item->out();
                            }
                    }
                }

		$out .= "</channel>\n";

		$out .= "</rss>";

		return str_replace("&", "&amp;", $out);
	}

}

class RSSItem {

	public $title;
	public $link;
	public $description;
	public $pubDate;
	public $guid;
	public $tags = array();
	public $attachment;
	public $length;
	public $mimetype;

	public function __construct($options) {
		if (isset($options['title'])) {
			$this->title = stripslashes(html_entity_decode(urldecode($options['title']), ENT_QUOTES, 'UTF-8'));
		}

		if (isset($options['link'])) {
			$this->link = html_entity_decode($options['link'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($options['date'])) {
			$this->pubDate = date("D, d M Y H:i:s O", ((strtotime($options['date'])) ? strtotime($options['date']) : $options['date']));
		}

		if (isset($options['description'])) {
			$this->description = "<![CDATA[ ".stripslashes(urldecode($options['description']))." ]]>";
		}

		if (isset($options['enclosure'])) {
			$this->attachment = $options['enclosure']['url'];
			$this->mimetype = $options['enclosure']['type'];
			$this->length = $options['enclosure']['length'];
		}

		if (isset($options['author'])) {
			$this->tags['author'] = $options['author'];
		}
	}

	public function out() {
		$out = "<item>\n";
		$out .= "<title>".$this->title."</title>\n";
		$out .= "<link>".$this->link."</link>\n";
		$out .= "<description>".$this->description."</description>\n";
		$out .= "<pubDate>".((empty($this->pubDate)) ? date("D, d M Y H:i:s O") : $this->pubDate)."</pubDate>\n";

		if (!empty($this->attachment)) {
			$out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";
		}

		$this->guid = (empty($this->guid)) ?  $this->link : $this->guid;

		$out .= "<guid>".$this->guid."</guid>\n";

		foreach ($this->tags as $key => $val) {
			$out .= "<$key>$val</$key\n>";
		}

		return $out."</item>\n";
	}

}

?>
