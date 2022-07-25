<?php
/*=====================================================
 Name      : Allocine.FR (Series) v1.3 Séries
-----------------------------------------------------
 Author    : DarkLane
-----------------------------------------------------
 Site      : https://www.templatedlefr.fr
=====================================================
 Copyright (c) 2021,2022 TemplateDleFr
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

		if( $contenuUrl = $html )
		{
			if( $titleTmp = $this->getEpression($contenuUrl,'#<meta property="og:title" content="(.*?)" />#is') )
			{
				$serie['title'] = strip_tags($titleTmp[0]);
			}
			
			if( $poster_pathTmp = $this->getEpression($contenuUrl,'#<meta property="og:image" content="(.*?)" />#is') )
			{
				$serie['poster_path'] = $poster_pathTmp[0];
			}
			
			if( $backdrop_pathTmp = $this->getEpression($contenuUrl,'#<meta name="twitter:image" content="(.*?)" />#is') )
			{
				$serie['backdrop_path'] = '' .$backdrop_pathTmp[0] .'';
			}
			
			if( $coverTmp = $this->getEpression($contenuUrl,'#<img class="thumbnail-img" src="(.*?)" alt="[^"]+" width="310" height="420" />#is') )
			{
				$serie['cover'] = '' .$coverTmp[0] .'';
			}
			
			if( $orgimgTmp = $this->getEpression($contenuUrl,'#<meta property="og:image" content="(.*?)" />#is') )
			{
				$serie['orgimg'] = '' .$orgimgTmp[0] .'';
			}
			else
			{
			    $serie['no-backdrop'] = $config['http_home_url'].'images/no-affiche.png';
			}
			
			if( $overviewTmp = $this->getEpression($contenuUrl,'#<div class="content-txt ">(.*?)<div id="mobile-referrer-atf"></div>#is') )
			{		
                $overview = str_replace(array("\t","\n","\r"),array('','',''),$overviewTmp[0]);  		
				$serie['overview'] = strip_tags(trim($overview));
			}
			
			if( $namelocalTmp = $this->getEpression($contenuUrl,'#<span class="light">Titre original :</span>(.*?)</strong>#is') )
			{
				$serie['namelocal'] = strip_tags(trim($namelocalTmp[0]));
			}
			
			if( $genreTmp = $this->getEpression($contenuUrl,'#<span class="spacer">/</span>[^"]+<span class="spacer">/</span>(.*?)</div>#is') )
			{		
                $genre = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$genreTmp[0]); 
				$serie['genre'] = strip_tags($genre);	
			}
			
			if( $actorsTmp = $this->getEpression($contenuUrl,"#Avec</span>(.*?)</div>#is") )
			{
				$actors = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$actorsTmp[0]);
				$serie['actors'] = strip_tags($actors);		
			}
						
			if( $runtimeTmp = $this->getEpression($contenuUrl,'#<div class="meta-body-item meta-body-info">[^"]+<span class="spacer">/</span>(.*?)<span class="[^"]+=">#is') )
			{
				$runtime = str_replace(array("\t","\n","\r","/"),array(' ',' ',' ',''),$runtimeTmp[0]);
				$serie['runtime'] = strip_tags($runtime);		
			}
			
			if( $yearTmp = $this->getEpression($contenuUrl,'#Ann[^"]+e de production</span>(.*?)</span>#is') )
			{
				$serie['year'] = strip_tags(trim($yearTmp[0]));	
			}
			
			if( $languageTmp = $this->getEpression($contenuUrl,"#<span class=\"what light\">Langues</span>(.*?)</span>#is") )
			{
				$language = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$languageTmp[0]);
				$serie['language'] = strip_tags($language);		
			}
			
			if( $datelocalTmp = $this->getEpression($contenuUrl,'#<div class="meta-body-item meta-body-info">(.*?)sur#is') )
			{
				$serie['datelocal'] = strip_tags(trim($datelocalTmp[0]));
			}
			
			if( $certificationTmp = $this->getEpression($contenuUrl,'#<div class="label kids-label aged-default">(.*?)</div>#is') )
			{
				$serie['certification'] = strip_tags(trim($certificationTmp[0]));
			}
			
			if( $directorTmp = $this->getEpression($contenuUrl,'#De</span>(.*?)</div>#is') )
			{
				$director = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$directorTmp[0]); 
				$serie['director'] = strip_tags($director);
			}
			
			if( $authorTmp = $this->getEpression($contenuUrl,'#Par</span>(.*?)</div>#is') )
			{
				$author = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$authorTmp[0]); 
				$serie['author'] = strip_tags($author);
			}
			
			if( $productionfirmTmp = $this->getEpression($contenuUrl,'#<div class="meta-body-item meta-body-info">[^"]+sur(.*?)<span class="spacer">/</span>#is') )
			{
				$serie['productionfirm'] = strip_tags(trim($productionfirmTmp[0]));	
			}
			
			if( $countryTmp = $this->getEpression($contenuUrl,'#Nationalit[^"]+</span>(.*?)</div>#is') )
			{
				$country = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$countryTmp[0]);
				$serie['country'] = strip_tags(trim($country));
			}
			
			if( $colorTmp = $this->getEpression($contenuUrl,'#<span class="what light">Couleur</span>(.*?)</span>#is') )
			{
				$color = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$colorTmp[0]);
				$serie['color'] = strip_tags(trim($color));
			}
			
			if( $seasonTmp = $this->getEpression($contenuUrl,'#<div class="stats-numbers-row-item">(.*?)<div class="stats-numbers-row-item">#is') )
			{
				$serie['season'] = strip_tags($seasonTmp[0]);
			}
			
			if( $episodeTmp = $this->getEpression($contenuUrl,'#<div class="stats-info">[^"]+</div>(.*?)<a class="end-section-link "#is') )
			{
				$episode = str_replace(array("\t","\n","\r"),array(' ',' ',' '),$episodeTmp[0]);
				$serie['episode'] = strip_tags(trim($episode));
			}
			
			if( $trailerTmp = $this->getEpression($contenuUrl,'#&quot;high&quot;:&quot;(.*?)&quot;,&quot;low&quot;:&quot;#is') )
			{
				$trailer = str_replace('\/','/', $trailerTmp[0]);
				//$trailer = preg_replace('#1f763rie=(.*?).html#is','',$trailer);				
				$serie['trailer'] = $trailer ;
			}
			
			if( $ratingcTmp = $this->getEpression($contenuUrl,'#<span class="[^"]+ rating-title"> Spectateurs </span>(.*?)</span><span class="stareval-review light">#is') )
        	{   
				$serie['ratingc'] = $this->net_note_spectacteur($ratingcTmp[0]);
       		}				
			
			if( $ratingaTmp = $this->getEpression($contenuUrl,'#<span class="[^"]+ rating-title"> Presse </span>(.*?)</span><span class="stareval-review light">#is') )
			{
				$serie['ratinga'] = $ratingaTmp[0];
			}
			
		}

		$film = array(
		    'img'			  => $images_small,
			'cover'		      => $serie['cover'],
			'name'			  => $serie['title'],
            'story'		      => $serie['overview'],
            'namelocal'		  => $serie['namelocal'],
            'genres'		  => $serie['genre'],
            'runtime'	      => $serie['runtime'],
			'year'			  => $serie['year'],
            'datelocal'		  => $serie['datelocal'],
            'director'		  => '',
			'author'		  => $serie['author'],
			'productionfirm'  => $serie['productionfirm'],
            'actors'		  => $serie['actors'],
            'synopsis'		  => $serie['overview'],
            'country'	      => $serie['country'],
			'url'			  => $url,
			'orgimg'		  => $serie['poster_path'],
			'screens'		  => $screens,
		    'trailer'		  => $serie['trailer'],
			'namelong'		  => $serie['title'],
            'ratingc'         => $serie['ratingc'],
            'language'		  => $serie['language'],
            'season'		  => $serie['season'],
			'episode'	      => $serie['episode'],
                     );
	

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

private function net_note_spectacteur($text)
{
	$text = trim($text);
	$text = preg_replace('#<div class="stareval stareval-medium stareval-theme-default">(.*?)<span class="stareval-note">#is','',$text);
	$text = preg_replace('#Spectateurs(.*?)<div class="rating-mdl #is','',$text);
	$text = preg_replace('#Spectateurs(.*?)<div class="rating-mdl #is','',$text);
	$text = preg_replace('#stareval-stars(.*?)<div class="star icon</div></div>#is','',$text);
	$text = str_replace(array('<div class="stareval stareval-medium stareval-theme-default"><div class="rating-mdl ','</div></span>',' stareval-stars">'),array('','','',),$text);
	$text = str_replace(array("null","n00","n05","n10","n15","n20","n25","n30","n35","n40","n45","n50"),array("null.png","s_star_0_0.png","s_star_0_5.png","s_star_1_0.png","s_star_1_5.png","s_star_2_0.png","s_star_2_5.png","s_star_3_0.png","s_star_3_5.png","s_star_4_0.png","s_star_4_5.png","s_star_5_0.png"),$text);
	$text = str_replace("\n",'',$text);
	$text = str_replace("\r",'',$text);
	return $text;
	
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