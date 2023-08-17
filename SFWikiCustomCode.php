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
	
	
	public static function onBeforePageDisplay( &$out )
	{
		//self::SetupFavIcons($out);
		//self::SetMapSessionData();
		self::SetupLongitudeAds($out);
		self::SetupTwitchEmbed($out);
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
	
};