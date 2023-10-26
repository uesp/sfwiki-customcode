<?php


class SFWikiCustomCode
{
	public static $isMobile = false;
	
	
	public static function onParserInit( &$parser)
	{
		if (class_exists("MobileContext"))
		{
			if (MobileContext::singleton()->isMobileDevice()) self::$isMobile = true;
		}
		
		return true;
	}
	
	
	public static function onOutputPageBeforeHTML( &$out, &$text )
	{
		static $hasAddedDiv = false;
		
		if ($hasAddedDiv) return true;
		if (!self::$isMobile) return true;
		
		$js = "var contentDiv = document.querySelector('#content'); 
				var adDiv = document.querySelector('#sfwiki_M_1');
				contentDiv.before(adDiv);";
		
		$text = "<div id='sfwiki_M_1'></div>" . $text;
		$out->addInlineScript($js);
		
		$hasAddedDiv = true;
		return true;
	}
	
	
	public static function SetupUespFavIcons(&$out) 
	{
		$out->addLink(array('rel' => 'icon', 'type' => 'image/png', 'href' => 'https://images.starfieldwiki.net/favicon-16.png',  'sizes' => '16x16'));
		$out->addLink(array('rel' => 'icon', 'type' => 'image/png', 'href' => 'https://images.starfieldwiki.net/favicon-32.png',  'sizes' => '32x32'));
		$out->addLink(array('rel' => 'icon', 'type' => 'image/png', 'href' => 'https://images.starfieldwiki.net/favicon-48.png',  'sizes' => '48x48'));
		$out->addLink(array('rel' => 'icon', 'type' => 'image/png', 'href' => 'https://images.starfieldwiki.net/favicon-64.png',  'sizes' => '64x64'));
		$out->addLink(array('rel' => 'icon', 'type' => 'image/png', 'href' => 'https://images.starfieldwiki.net/favicon-96.png',  'sizes' => '96x96'));
		$out->addLink(array('rel' => 'icon', 'type' => 'image/png', 'href' => 'https://images.starfieldwiki.net/favicon-128.png', 'sizes' => '128x128'));
		$out->addLink(array('rel' => 'icon', 'type' => 'image/png', 'href' => 'https://images.starfieldwiki.net/favicon-256.png', 'sizes' => '256x256'));
	}
	
	
	public static function onBeforePageDisplay( &$out )
	{
		self::SetupLongitudeAds($out);
		self::SetupTwitchEmbed($out);
		self::SetupUespFavIcons($out);
	}
	
	
	public static function SetupLongitudeAds( &$out )
	{
		//$out->addInlineScript("var sfWikiTopAd = document.getElementById('sfwikiTopAd'); if (sfWikiTopAd) sfWikiTopAd.style = 'height:90px;'; ");
		$out->addScriptFile('https://lngtd.com/starfield-wiki.js');
	}
	
	
	public static function SetupTwitchEmbed( &$out )
	{
		$out->addScriptFile('https://player.twitch.tv/js/embed/v1.js');
	}
	
	
	public static function onUserMailerTransformMessage(array $to, MailAddress $from, &$subject, &$headers, &$body, &$error ) 
	{
			// Fix issue with body hash changing which breaks DKIM verification
			// Original 8bit encoding is changed to quoted-printable at some point in the mail chain.
		$headers['Content-transfer-encoding'] = 'quoted-printable';
		
		$body = quoted_printable_encode($body);
		
		return true;
	}
	
	
	public static function onPreSaveTransformCheckUploadWizard(Parser $parser, string &$text)
	{
		$result = preg_match( '/=={{int:filedesc}}==
{{Information
\|description=(.*)
\|date=(.*)
\|source=(.*)
\|author=(.*)
\|permission=(.*)
\|other versions=(.*)
}}

=={{int:license-header}}==
{{(.*)}}
*(.*)/', $text, $matches );
		if (!$result) return;
		
		$description = $matches[1];
		
		if (preg_match('/{{([a-zA-Z0-9_-])+\|1=(.*)}}/', $description, $descMatches))
		{
			$description = $descMatches[2];
		}
		
		$date = $matches[2];
		$source = $matches[3];
		$author = $matches[4];
		$permission = $matches[5];
		$otherVersions = $matches[6];
		$license = $matches[7];
		$license = str_replace("self|", "", $license);
		$extra = $matches[8];
		
		$text = "== Summary ==
$description
		
== Licensing ==
{{{$license}}}

$extra";
	}
	
};