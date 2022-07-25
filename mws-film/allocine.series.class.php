<?php
/*
=====================================================
 Author : Mehmet Hanoğlu <dle.net.tr>
-----------------------------------------------------
 License : MIT License
-----------------------------------------------------
 Copyright (c)
-----------------------------------------------------
 Date : 08.02.2018 [1.3]
=====================================================
*/

if ( !defined( 'E_DEPRECATED' ) ) {
	@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
	@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );
} else {
	@error_reporting ( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
	@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
}

require_once (ENGINE_DIR . '/classes/mws-film/class_curl.php');

class FilmReader {

	public function get( $url ) {
		$html = str_replace( array( "\r", "\n" ), "", $this->getURLContent( $url ) );
		
		$film = array();

		$film['url'] = $url;
		
		if( $contenuUrl = $html )
		{
			// on récupere l'url de l'image
			if( $imageTmp = $this->getEpression($contenuUrl,'#<meta property="og:image" content="(.*?)" />#is') )
			{
				$film['cover'] = $imageTmp[0];
			}

			if( $titreTmp = $this->getEpression($contenuUrl,'#<meta property="og:title" content="(.*?)" />#is') )
			{
				$film['name'] = strip_tags($titreTmp[0]);
			}
			
			if( $resumeTmp = $this->getEpression($contenuUrl,'#<div class="content-txt ">(.*?)</div>#is') )
			{				
				$film['story'] = strip_tags(trim($resumeTmp[0]));
			}
			
			if( $languageTmp = $this->getEpression($contenuUrl,'#<span class="what light">Langues</span>(.*?)</span>#is') )
			{
				$film['language'] = trim(strip_tags($languageTmp[0]));
			}
			
			if( $dureeTmp = $this->getEpression($contenuUrl,'#<span class="spacer">/</span>(.*?)<span class="spacer">#is') )
			{
				
				$film['runtime'] = trim($dureeTmp[0]);		
			}
			
			if( $genreTmp = $this->getEpression($contenuUrl,'#<div class="meta-body-item meta-body-info">[^"]+<span class="spacer">/</span>[^"]+<span class="spacer">/</span>(.*?)</div>#is') )
			{
				$film['genres'] = strip_tags(trim($genreTmp[0]));		
			}
			
			if( $yearTmp = $this->getEpression($contenuUrl,"#<div class=\"meta-body-item meta-body-info\">(.*?)<span class=\"spacer\">/</span>#is") )
			{
				$film['year'] = strip_tags(trim($yearTmp[0]));
			}
			
			if( $budgetTmp = $this->getEpression($contenuUrl,'#<p><strong><bdi>Budget</bdi></strong>(.*?)</p>#is') )
			{
				$film['budget'] = strip_tags(trim($budgetTmp[0]));
			}
			
			if( $directorTmp = $this->getEpression($contenuUrl,'#<li class="profile">(.*?)<p class="character">Director[^"]+</p>#is') )
			{
				$film['director'] = trim(strip_tags($directorTmp[0]));
			}
			
			if( $writersTmp = $this->getEpression($contenuUrl,'#Par</span>(.*?)</div>#is') )
			{
				$film['writers'] = strip_tags($writersTmp[0]);
			}
			
			if( $authorTmp = $this->getEpression($contenuUrl,'#<span class="light">Cr[^"]+e par</span>(.*?)</div>#is') )
			{
				$film['author'] = strip_tags($authorTmp[0]);
			}
	
			if( $localnameTmp = $this->getEpression($contenuUrl,'#<span class="light">Titre original :</span>(.*?)</div>#is') )
			{
				$film['localname'] = strip_tags(trim($localnameTmp[0]));	
			}
			
			if( $productionfirmTmp = $this->getEpression($contenuUrl,'#<span class="what light">Distributeur</span>(.*?)</span>#is') )
			{
				$film['productionfirm'] = strip_tags(trim($productionfirmTmp[0]));	
			}
			
			if( $actorsTmp = $this->getEpression($contenuUrl,'#<span class="light">Avec</span>(.*?)</div>#is') )
			{
				$actors = preg_replace('#<p class="character">(.*?)</p>#is',',',$actorsTmp[0]);
				$actors = preg_replace('/\s+/', ' ', $actors);
				$film['stars'] = strip_tags(trim($actors));		
			}
			
			if( $countryTmp = $this->getEpression($contenuUrl,'#Nationalit[^"]+</span>(.*?)</div>#is') )
			{
				$film['country'] = trim(strip_tags($countryTmp[0]));
			}
			
			if( $notesTmp = $this->getEpression($contenuUrl,'#<span class="[^"]+ rating-title"> Spectateurs </span>(.*?)<span class="stareval-review light">#is') )
        	{   
				$film['ratinga'] = strip_tags($notesTmp[0]);
			}
			
			if( $trailerTmp = $this->getEpression($contenuUrl,'#standard&quot;:&quot;(.*?)&quot;#is') )
			{
				//$trailer = preg_replace('\\\\', '', $trailerTmp[0]);
				$film['trailer'] = stripslashes($trailerTmp[0]);
				/*$serie['trailer'] = "<br /><br /><b>Bande annonce:</b><br /><iframe src='https://seriesaddict.fr/videos/embed/".$mediaTmp[0]."' style='width:480px; height:270px' frameborder='0'></iframe>";				
				$serie['resultat_media_'] = "[b]Lien de la bande annonce:[/b]". "\n"."[url]https://seriesaddict.fr/videos/embed/".$mediaTmp[0]."[/url]"."\n\n";		
			*/}
			
		}

		return $film;
	}
	
	
	private function getEpression($chaine,$expression) {
	   preg_match_all($expression,$chaine, $trouve);
	
	   if( count($trouve[1])!=0 )
	   {
		return($trouve[1]);
	   }
	    else return(false);
	}

	private function cleanWords( $text ) {
		$text = str_replace(array("\t","\n","\r","  "),array('','','',''),$text);
		$text = str_replace('	', '', $text);
		$text = preg_replace(array("/^\s+/","/\s+$/"),array('',''),$text);
		return trim( utf8_decode($text) );
	}

			private function getURLContent($url) {
		if ( function_exists('curl_exec') ) {
		
			$options = array(
			"url" => $url.'?language=fr-FR',
			"type" => "GET",
			"return_transfer" => "1"
			);
			$obj = new wArLeY_cURL($options);
			$output = $obj->Execute();
		} else {
			//$output = file_get_contents($url);
			$opts = array('http'=>array('method'=>"GET",'header'=>"Accept-language: fr-FR\r\n" ."Cookie: foo=bar\r\n"));
			$context = stream_context_create($opts);
			// Acces a un fichier HTTP avec les entetes HTTP indiqués ci-dessus
			$output = file_get_contents($url.'?language=fr-FR', false, $context);
		}
		
		return $output;
	}
}

@header( "Content-type: text/html; charset=utf-8" );


?>