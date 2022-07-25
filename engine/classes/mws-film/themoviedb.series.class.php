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
require_once ENGINE_DIR . '/classes/mws-film/tmdb/tmdb-api.php';

class FilmReader {

	public function get( $url ) {

/*$html = str_replace(array('.','-',"/",":","=","_",),array('','','','','','',),$url);

$movie_id = preg_replace('/[a-z]/i', '',$html);*/

$apikey = "23e89da030a0ee8b25aaed20950a0c25";

                $serie_id = preg_split('#([0-9]+)#', $url, null, PREG_SPLIT_DELIM_CAPTURE)[1];

$appendToResponse  = array('account_states', 'credits', 'images', 'release_dates', 'trailers', 'videos', 'lists', 'rating', 'content_ratings');

$cm = curl_init();
curl_setopt($cm, CURLOPT_URL, "http://api.themoviedb.org/3/tv/".$serie_id."?api_key=".$apikey."&language=fr-FR&append_to_response=". implode(',', (array) $appendToResponse));
curl_setopt($cm, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($cm, CURLOPT_HEADER, FALSE);
curl_setopt($cm, CURLOPT_HTTPHEADER, array("Accept: application/json"));
$result = curl_exec($cm);
curl_close($cm);
$seriedata = json_decode($result,true);

            if (isset($seriedata['vote_average'])) {
              $ret['rating'] = $seriedata['vote_average'] == 0 ? '' : $seriedata['vote_average'];
            }
	 
            $runtime     = $seriedata['episode_run_time'][0]. " min"; 
            $ltitle = $seriedata['name'];
            $namelocal = $seriedata['original_name'];
            $images_small = 'http://image.tmdb.org/t/p/w500' . $seriedata['poster_path'];
            $big_images = 'http://image.tmdb.org/t/p/original' . $seriedata['backdrop_path'];
            $film['url'] = $url;
            $year = substr($seriedata['first_air_date'], 0, 4);
            $releasen    = strftime('%d-%m-%Y',strtotime($seriedata['first_air_date'] ));
            $tagline = $seriedata['tagline'];
            $episode = number_format($seriedata['number_of_episodes']);
            $season = number_format($seriedata['number_of_seasons']);
            $story = $seriedata['overview'];

            // TMDB vote average
            $ratinga = implode(', ', $ret);

            // TMDB vote count
            $ratingc = (int) $seriedata['vote_count'];


            for ($i = 0; $i < count($seriedata['created_by']); $i++) {
                $created_array[$i] = $seriedata['created_by'][$i]['name'];
            }
            $created_by = implode(', ', $created_array);			
            $director = implode(', ', $created_array);

            for ($i = 0; $i < count($seriedata['production_countries']); $i++) {
                $coun_array[$i] = $seriedata['production_countries'][$i]['name'];
            }
            $country = implode(', ', $coun_array);
         
            for ($i = 0; $i < count($seriedata['production_companies']); $i++) {
                $compan_array[$i] = $seriedata['production_companies'][$i]['name'];
            }
            $companies = implode(', ', $compan_array);
           
            for ($i = 0; $i < count($seriedata['genres']); $i++) {
                $genre_array[$i] = $seriedata['genres'][$i]['name'];
            }
            $genre = implode(', ', $genre_array);

            for ($i = 0; $i < count($seriedata['spoken_languages']); $i++) {
                $lang_array[$i] = $seriedata['spoken_languages'][$i]['name'];
            }
            $languages = implode(', ', $lang_array);

            for ($i = 0; $i < count($seriedata['videos']['results']); $i++) {
                $youtube_array[$i] = $seriedata['videos']['results'][$i]['key'];
            }
            $youtube = implode(', ', $youtube_array);

             $cast = $seriedata['credits']['cast'];
            $actors = array();
            $count = 0;
            foreach ($cast as $cast_member) {
                $actors[] = $cast_member['name'];
                $count++;
                if ($count == 8)
                    break;
            }
            $actors = implode(", ", $actors);
		
		
		$film = array(
			'url'			  => $url,
		    'img'			  => $images_small,
			'cover'		      => $big_images,
			'name'			  => $ltitle,
            'story'		      => $story,
            'namelocal'		  => $namelocal,
            'genres'		  => $genre,
            'runtime'	      => $runtime,
			'season'			  => $season,
            'episode'		  => $episode,
			'year'			  => $year,
            'datelocal'		  => $releasen,
			'created_by'		  => $created_by,
            'writers'		  => $writer,
            'director'		  => $director,
			'productionfirm'  => $companies,
            'actors'		  => $actors,
            'synopsis'		  => $story,
            'country'	      => $country,
			'orgimg'		  => $images_small,
			'screens'		  => '',
		    'trailer'		  => "https://www.youtube.com/embed/".$youtube,
			'ratinga'         => $ratinga,
            'ratingc'         => $ratingc,
            'language'		  => $languages,
            'revenue'		      => $revenue,
			//'certification'	  => $serie['certification'],
            'tagline'		=> $tagline,			
			
                     );

		return $film;
	}
	
}

@header( "Content-type: text/html; charset=utf-8" );


?>