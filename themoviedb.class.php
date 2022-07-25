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

        $backdrop    = "http://image.tmdb.org/t/p/original" . $tmdb['backdrop_path'];
        $poster      = "http://image.tmdb.org/t/p/w500" . $tmdb['poster_path'];
        $ltitle      = $tmdb['title'];
        $year        = substr($tmdb['release_date'], 0, 4);
        $title       = $tmdb['original_title'];
        $description = $tmdb['overview'];
        $status      = $tmdb['status'];
        $homepage    = $tmdb['homepage'];
        $releasen    = date("d.m.Y",strtotime($tmdb['release_date']));
        $runtime     = $tmdb['runtime']. "min";
        $ltitle      = $tmdb['title'];
        $tagline     = $tmdb['tagline'];
        $status      = $tmdb['status'];
        $budget      = number_format($tmdb['budget']) . " \$";
        $revenue     = number_format($tmdb['revenue']) . " \$";
		
        if ($tmdb['poster_path']!=null){
                $images_small = 'http://image.tmdb.org/t/p/w185' . $tmdb['poster_path'];
        } elseif ($tmdb['backdrop_path']!=null){
                $images_small = 'http://image.tmdb.org/t/p/w185' . $tmdb['poster_path'];
        } else {
                $images_small = '/img/no-backdrop.png';
        }
        
        if (is_array($tmdb['genres'])){
                foreach($tmdb['genres'] as $result) {$genre .= $result['name']. ', ';}
        }
        if (is_array($tmdb['spoken_languages'])){
                foreach($tmdb['spoken_languages'] as $result) {$languages .= $result['name'].' ';}
        }
        if (is_array($tmdb['production_companies'])){
                foreach($tmdb['production_companies'] as $result) {$companies .= $result['name'].', ';}
        }
        if (is_array($tmdb['production_countries'])){
                foreach($tmdb['production_countries'] as $result) {$country .= $result['name'].', ';}
        }
        if (is_array($tmdb['videos']['results'])){
                foreach($tmdb['videos']['results'] as $result) {$youtube = "https://www.youtube.com/embed/".$result['key'];}
		}
		
        if(is_array($tmdb['credits']['crew'])) {
                    foreach($tmdb['credits']['crew'] as $crew) {
                        if ($crew['job'] == 'Director'){

                        $crewMember = $crew['name'];
                    }
                }
              
	      }
		  
        foreach ($data['credits']['crew'] as $crew) {
            if ($crew['job'] == 'Screenplay') {
               $writer = $crew['name'];
            }
        }

        $cast = $tmdb['credits']['cast'];
            $actors = array();
            $count = 0;
            foreach ($cast as $cast_member) {
                $actors[] = $cast_member['name'];
                $count++;
                if ($count == 8)
                    break;
            }
            $actors = implode(", ", $actors);		
		
            $mpaa_rating = '';
            $age_rating = '';
            $releases = $tmdb['release_dates'];
            foreach ($releases as $release_item) {
                /*if ($release_item['iso_3166_1'] === 'US')
                    $mpaa_rating = $release_item['certification'];
                if ($release_item['iso_3166_1'] === 'DE')
                    $age_rating = $release_item['certification'];*/
                if ($release_item['iso_639_1'] === 'FR')
                    $age_rating = $release_item['certification'];
            }		
		
		$film = array(
		    'img'			=> $images_small,
			'namelong'		=> $title,
			'name'			=> $ltitle,
            'country'	    => $country,
			'year'			=> $year,
            'genres'		=> $genre,
            'actors'		=> $actors,
            'writers'		=> $writer,
            'director'		=> $crewMember,
            'story'		    => $description,
            'runtime'	    => $runtime,
            'datelocal'		=> $releasen,
            'namelocal'		=> $title,
            'tagline'		=> $tagline,
            'color'		    => $revenue,
            'budget'		=> $budget,
            'language'		=> $languages,
            'tagline'		=> $tagline,
            'productionfirm'=> $companies,
		    'type'			=> $youtube,
        );
		return $film;
	}
}
?>