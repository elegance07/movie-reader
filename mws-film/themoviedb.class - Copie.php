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

class FilmReader {

	public function get( $url ) {
		
		$apikey = "23e89da030a0ee8b25aaed20950a0c25";
		
        $id = str_replace(array('.','-',"/",":","=","_",),array('','','','','','',),$url);
        $movie_id = preg_replace('/[a-z]/i', '',$id);

$appendToResponse  = array('account_states', 'alternative_titles', 'credits', 'images','keywords', 'release_dates', 'trailers', 'videos', 'translations', 'similar', 'reviews', 'lists', 'changes', 'rating');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://api.themoviedb.org/3/movie/{$movie_id}?api_key={$apikey}&language=fr-FR&append_to_response=". implode(',', (array) $appendToResponse));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
$result = curl_exec($ch);
curl_close($ch);
$tmdb = json_decode($result,true);

        $ltitle      = $tmdb['title'];
        $year        = substr($tmdb['release_date'], 0, 4);
        
        if (is_array($tmdb['genres'])){
                foreach($tmdb['genres'] as $result) {$genre .= $result['name']. ', ';}
        }

		$film = array(
			'name'			=> $ltitle,
			'year'			=> $year,
                        'genres'		=> $genre,
        );
		return $film;
	}
}
?>