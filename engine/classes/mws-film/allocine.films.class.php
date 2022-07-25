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

$html = str_replace(array('.','-',"/",":","=","_",),array('','','','','','',),$url);

$id = preg_replace('/[a-z]/i', '',$html);

$apikey = "23e89da030a0ee8b25aaed20950a0c25";

$appendToResponse  = array('account_states', 'alternative_titles', 'credits', 'images','keywords', 'release_dates', 'trailers', 'videos', 'translations', 'similar', 'reviews', 'lists', 'changes', 'rating');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://api.themoviedb.org/3/movie/".$id."?api_key={$apikey}&language=fr-FR&append_to_response=". implode(',', (array) $appendToResponse));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
$result = curl_exec($ch);
curl_close($ch);
$movie = json_decode($result,true);

     if (isset($movie['vote_average'])) {
         $ret['rating'] = $movie['vote_average'] == 0 ? '' : $movie['vote_average'];
     }

        $backdrop    = "http://image.tmdb.org/t/p/original" . $movie['backdrop_path'];
        $poster      = "http://image.tmdb.org/t/p/w500" . $movie['poster_path'];
        $ltitle      = $movie['title'];
        $year        = substr($movie['release_date'], 0, 4);
        $homepage    = $movie['homepage'];
        $releasen    = date("d.m.Y",strtotime($movie['release_date']));
        $runtime     = $movie['runtime'] . " min.";
        $ltitle      = $movie['title'];
        $vote        = implode(', ', $ret);
        $tagline     = $movie['tagline'];
        $status      = $movie['status'];
        $budget      = number_format($movie['budget']) . " \$";
        $revenue     = number_format($movie['revenue']) . " \$";
		

        if ($movie['poster_path']!=null){
                $images_small = 'http://image.tmdb.org/t/p/w185' . $movie['poster_path'];
        } elseif ($movie['backdrop_path']!=null){
                $images_small = 'http://image.tmdb.org/t/p/w185' . $movie['poster_path'];
        } else {
                $images_small = '/img/no-backdrop.png';
        }

        if ($movie['backdrop_path']!=null){
                $big_images = 'http://image.tmdb.org/t/p/original' . $movie['backdrop_path'];
        } elseif ($movie['backdrop_path']!=null){
                $big_images = 'http://image.tmdb.org/t/p/original' . $movie['backdrop_path'];
        } else {
                $big_images = '/img/no-backdrop.png';
        }
		

        
        if (is_array($movie['genres'])){
                foreach($movie['genres'] as $result) {$genre .= $result['name']. ', ';}
        }
        if (is_array($movie['spoken_languages'])){
                foreach($movie['spoken_languages'] as $result) {$languages .= $result['name'].' ';}
        }
        if (is_array($movie['production_companies'])){
                foreach($movie['production_companies'] as $result) {$companies .= $result['name'].', ';}
        }
        if (is_array($movie['production_countries'])){
                foreach($movie['production_countries'] as $result) {$country .= $result['name'].', ';}
        }
        if (is_array($movie['trailers']['youtube'])){
                foreach($movie['trailers']['youtube'] as $result) {$youtube = "https://www.youtube.com/embed/".$result['source'];}
        }

        $cast = $movie['credits']['cast'];
            $actors = array();
            $count = 0;
        foreach ($cast as $cast_member) {
                $actors[] = $cast_member['name'];
                $count++;
                if ($count == 8)
            break;
        }
          $actors = implode(", ", $actors);

           foreach ($movie['credits']['crew'] as $crew) {
            if ($crew['job'] == 'Screenplay') {
              $writer = $crew['name'];
            }
           }
        
        if(is_array($movie['credits']['crew'])) {
                    foreach($movie['credits']['crew'] as $crew) {
                        if ($crew['job'] == 'Director'){
                        $crewMember = $crew['name'];
                    }
                }
              
	      }
		  
            $age_rating = '';
            $releases = $movie['release_dates']['results'];
            foreach ($releases as $release_item) {
                if ($release_item['iso_639_1'] === 'FR')
                    $age_rating = $release_item['certification'];
            }

		$film = array(
			'url'			  => $url,
		    'img'			  => $images_small,
			'cover'		      => $big_images,
			'name'			  => $ltitle,
            'story'		      => $movie['overview'],
            'namelocal'		  => $movie['original_title'],
            'genres'		  => $genre,
            'runtime'	      => $runtime,
			'year'			  => $year,
            'datelocal'		  => $releasen,
            'writers'		  => $writer,
            'director'		  => $crewMember,
			'author'		  => $crewMember,
			'productionfirm'  => $companies,
            'actors'		  => $actors,
            'synopsis'		  => $movie['overview'],
            'country'	      => $country,
			'orgimg'		  => $images_small,
			'screens'		  => '',
		    'trailer'		  => $youtube,
			'ratinga'         => '',
            'ratingc'         => $vote,
            'language'		  => $languages,
            'revenue'		      => $revenue,
			'certification'	  => $movie['certification'],
            'tagline'		=> $tagline,			
			
                     );

		return $film;
	}
	
}

@header( "Content-type: text/html; charset=utf-8" );


?>