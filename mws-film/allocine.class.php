<?php
/*
=====================================================
 Author : Mehmet Hanoğlu <dle.net.tr>
-----------------------------------------------------
 License : MIT License
-----------------------------------------------------
 Copyright (c)
-----------------------------------------------------
 Date : 16.03.2019 [2.6]
=====================================================
*/

if ( ! defined( 'E_DEPRECATED' ) ) {
	@error_reporting( E_ALL ^ E_WARNING ^ E_NOTICE );
	@ini_set( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );
} else {
	@error_reporting( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
	@ini_set( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
}
		
require_once (ENGINE_DIR . '/classes/mws-film/class_curl.php');

class FilmReader {

	public function get( $url ) {
		$html = str_replace( array( "\r", "\n" ), "", $this->getURLContent( $url ) );

		preg_match( "#\=([0-9]+)\.#is", $url, $_tmp );
		$film_id = trim( $_tmp[1] );
		
		$film = array();

		$film['url'] = $url;
		
		$screenshot = 'https://www.allocine.fr/film/fichefilm-'.$film_id.'/photos/';
		
		$url_screenshot = str_replace( array( "\r", "\n" ), "", $this->getURLContent( $screenshot ) );
		
		if( $contenuUrl = $html ){
			
			if( $nameTmp = $this->getEpression($contenuUrl,'#<meta property="og:title" content="(.*?)" />#is') )
			{
				$film['name'] = strip_tags($nameTmp[0]);
			}
			
			/*if( $coverTmp = $this->getEpression($contenuUrl,'#<meta name="twitter:image" content="(.*?)" />#is') )
			{
				$film['cover'] = $coverTmp[0];
			}
			
			if( $imgTmp = $this->getEpression($contenuUrl,'#<meta name="twitter:image" content="(.*?)" />#is') )
			{
				$film['img'] = $imgTmp[0];
			}*/
			
			if( $orgimgTmp = $this->getEpression($contenuUrl,'#<meta name="twitter:image" content="(.*?)" />#is') )
			{
				$film['orgimg'] = $orgimgTmp[0];
			}
			
			if( $storyTmp = $this->getEpression($contenuUrl,'#<div class="content-txt ">(.*?)</div>#is') )
			{				
				$film['story'] = strip_tags($storyTmp[0]);
			}

			if( $starsTmp = $this->getEpression($contenuUrl,"#Avec</span>(.*?)</div>#is") )
			{
				$film['stars'] = strip_tags($starsTmp[0]);		
			}

			if( $countryTmp = $this->getEpression($contenuUrl,'#Nationalit[^"]+</span>(.*?)</div>#is') )
			{
				$film['country'] = trim(strip_tags($countryTmp[0]));
			}	

			if( $languageTmp = $this->getEpression($contenuUrl,'#<span class="what light">Langues</span>(.*?)</span>#is') )
			{
				$film['language'] = trim(strip_tags($languageTmp[0]));
			}
			
			if( $authorTmp = $this->getEpression($contenuUrl,'#De</span>(.*?)</div>#is') )
			{
				$film['author'] = strip_tags($authorTmp[0]);
			}
			
			if( $writersTmp = $this->getEpression($contenuUrl,'#Par</span>(.*?)</div>#is') )
			{
				$film['writers'] = strip_tags($writersTmp[0]);
			}
			
			if(preg_match_all('#[0-9]+h [0-9]+min#is',$contenuUrl,$dureeTmp))
			{
				
				$film['runtime'] = $dureeTmp[0][0];		
			}
			
			if( $genreTmp = $this->getEpression($contenuUrl,'#<span class="[^"]+==">(.*?)</div>#is') )
			{
				$genre = preg_replace('/\-?\d+/', '', $genreTmp[0]);
				$film['genres'] = strip_tags($genre);	
			}
			
			if( $pressratingTmp = $this->getEpression($contenuUrl,'#Presse </span>(.*?)<span class="stareval-review light">#is') )
			{
				$film['best'] = strip_tags(trim ($pressratingTmp[0]));
			}
			
			if( $userratingTmp = $this->getEpression($contenuUrl,'#Spectateurs </span>(.*?)<span class="stareval-review light">#is') )
			{
				$film['ratinga'] = strip_tags(trim ($userratingTmp[0]));
			}
			
			if( $productionfirmTmp = $this->getEpression($contenuUrl,'#<span class="what light">Distributeur</span>(.*?)</span>#is') )
			{
				$film['productionfirm'] = strip_tags(trim($productionfirmTmp[0]));	
			}
			
			if( $localnameTmp = $this->getEpression($contenuUrl,'#<span class="light">Titre original </span>(.*?)</div>#is') )
			{
				$film['localname'] = strip_tags(trim($localnameTmp[0]));	
			}
			
			if( $colorTmp = $this->getEpression($contenuUrl,'#Couleur</span>(.*?)</div>#is') )
			{
				$film['color'] = strip_tags(trim($colorTmp[0]));	
			}
			
			if( $yearTmp = $this->getEpression($contenuUrl,'#Ann[^"]+e de production</span>(.*?)</span>#is') )
			{
				$film['year'] = strip_tags(trim($yearTmp[0]));	
			}
			
			if( $visionTmp = $this->getEpression($contenuUrl,'#<span class="[^"]+== date blue-link">(.*?)<span class="spacer">/</span>#is') )
			{
				$vision = str_replace('<strong>',' ',$visionTmp[0]);
				$film['vision'] = strip_tags(trim($vision));	
			}
			
			if( $budgetTmp = $this->getEpression($contenuUrl,'#<span class="what light">Budget</span>(.*?)</span>#is') )
			{
				$film['budget'] = strip_tags(trim($budgetTmp[0]));	
			}
			
			if( $aspectTmp = $this->getEpression($contenuUrl,'#Type de film</span>(.*?)</span>#is') )
			{
				$film['aspect'] = strip_tags(trim($aspectTmp[0]));	
			}
			
			if( $trailerTmp = $this->getEpression($contenuUrl,'#player_gen_cmedia=(.*?)&amp;cfilm=#is') )
			{
				$film['trailers'] = trim(str_replace('<span class="acLnk ','',$trailerTmp[0]));
				$film['trailers'] = str_replace('/video/player_gen_cmedia=','', $film['trailers']);
				$film['trailers'] = preg_replace('#1f763rie=(.*?).html#is','',$film['trailers']);				
				$film['trailer'] = 'http://www.allocine.fr/_video/iblogvision.aspx?cmedia='. $film['trailers'];
			}
			
			
			
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
		$text = str_replace( array( "\t", "  ", " -", "\r\n", "\n" ), "", $text );
		return trim( utf8_decode($text) );
	}

	private function getURLContent($url) {
		if ( function_exists('curl_exec') ) {
		
			$options = array(
			"url" => $url,
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
			$output = file_get_contents($url, false, $context);
		}
		
		return $output;
	}
}

@header( "Content-type: text/html; charset=utf-8" );

?>