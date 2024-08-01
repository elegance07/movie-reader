<?php
/*
=====================================================
 Author : Mehmet Hanoğlu <dle.net.tr>
-----------------------------------------------------
 License : MIT License
-----------------------------------------------------
 Copyright (c)
-----------------------------------------------------
 Date : 28.09.2018 [2.5]
=====================================================
*/

if (!defined('E_DEPRECATED')) {
    @error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
    @ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);
} else {
    @error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
    @ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
}

class FilmReader
{
    private $config = [
        'screens' => true,  // Ekran görüntülerini çekme ayarı
        'screens_count' => 5, // Çekilecek ekran görüntüsü sayısı
    ];

    public function get($url)
    {
        include ENGINE_DIR . "/data/mws-film.conf.php";
        $apikey = $mws_film['tmdb_api_key']; // TMDB API anahtarını ayar dosyasından al

        if (preg_match('#themoviedb.org/movie/(\\d+)($|-)#i', $url, $aMatch)) {
            $id = $aMatch[1];
        }

        $cti = curl_init();
        curl_setopt($cti, CURLOPT_URL, "http://api.themoviedb.org/3/movie/" . $id . "?language=en-null&append_to_response=videos&api_key=" . $apikey);
        curl_setopt($cti, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($cti, CURLOPT_HEADER, FALSE);
        curl_setopt($cti, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response14 = curl_exec($cti);
        curl_close($cti);
        $movie = json_decode($response14, true);

        $cm = curl_init();
        curl_setopt($cm, CURLOPT_URL, "http://api.themoviedb.org/3/movie/" . $id . "?language=tr-TR&append_to_response=credits,releases,images&include_image_language=en,null&api_key=" . $apikey);
        curl_setopt($cm, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($cm, CURLOPT_HEADER, FALSE);
        curl_setopt($cm, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response7 = curl_exec($cm);
        curl_close($cm);
        $data = json_decode($response7, true);

        $ret['rating'] = isset($data['vote_average']) ? round($data['vote_average'], 1) : '';
        $imdb_url = "https://www.imdb.com/title/" . $data['imdb_id'];
        $year = substr($data['release_date'], 0, 4);
        $tmdbid = $data['id'];
        $title = $data['original_title'];
        $description = $data['overview'];
        $status = $data['status'];
        $homepage = $data['homepage'];
        $releasen = date("d.m.Y", strtotime($data['release_date']));
        $runtime = $movie['runtime'] . " dk.";
        $ltitle = $data['title'];
        $vote = implode(', ', $ret);
        $tagline = $data['tagline'];
        setlocale(LC_MONETARY, "en_US");
        $budget = number_format($data['budget']) . " \$";
        $revenue = number_format($data['revenue']) . " \$";

        if ($data['poster_path'] != null) {
            $images_small = 'https://image.tmdb.org/t/p/w185' . $data['poster_path'];
        } elseif ($data['backdrop_path'] != null) {
            $images_small = 'https://image.tmdb.org/t/p/w185' . $data['poster_path'];
        } else {
            $images_small = '/img/no-backdrop.png';
        }

        if ($data['backdrop_path'] != null) {
            $big_images = 'https://image.tmdb.org/t/p/original' . $data['backdrop_path'];
        } elseif ($data['backdrop_path'] != null) {
            $big_images = 'https://image.tmdb.org/t/p/original' . $data['backdrop_path'];
        } else {
            $big_images = '/img/no-backdrop.png';
        }

        $genre = '';
        if (is_array($data['genres'])) {
            foreach ($data['genres'] as $result) {
                $genre .= $result['name'] . ', ';
            }
            $genre = rtrim($genre, ', ');
        }

        $languages = '';
        if (is_array($data['spoken_languages'])) {
            foreach ($data['spoken_languages'] as $result) {
                $languages .= $result['name'] . ', ';
            }
            $languages = rtrim($languages, ', ');
        }

        $companies = '';
        if (is_array($data['production_companies'])) {
            foreach ($data['production_companies'] as $result) {
                $companies .= $result['name'] . ', ';
            }
            $companies = rtrim($companies, ', ');
        }

        $country = '';
        if (is_array($data['production_countries'])) {
            foreach ($data['production_countries'] as $result) {
                $country .= $result['name'] . ', ';
            }
            $country = rtrim($country, ', ');
        }

        $youtubes = '';
        if (is_array($data['videos']['results'])) {
            foreach ($data['videos']['results'] as $result) {
                $youtubes = "https://www.youtube.com/embed/" . $result['key'];
            }
        }

        $imgs = [];
        if (is_array($data['images']['backdrops'])) {
            foreach ($data['images']['backdrops'] as $result) {
                $imgs[] = '[img]https://image.tmdb.org/t/p/original' . $result['file_path'] . '[/img]';
            }
        }
        $filmm = array_slice($imgs, 0, $this->config['screens_count']);

        $imge = '';
        if (is_array($filmm)) {
            foreach ($filmm as $result) {
                $imge .= $result . '</br>';
            }
        }

        $youtube = [];
        if (is_array($movie['videos']['results'])) {
            foreach ($movie['videos']['results'] as $result) {
                $youtube[] = '<option value="https://www.youtube.com/embed/' . $result['key'] . '">' . $result['name'] . '</option>';
            }
        }
        $type = implode('', $youtube);

        $cast = $data['credits']['cast'];
        $actors = [];
        $count = 0;
        foreach ($cast as $cast_member) {
            $actors[] = $cast_member['name'];
            $count++;
            if ($count == 8)
                break;
        }
        $actors = implode(", ", $actors);

        $screenman = '';
        foreach ($data['credits']['crew'] as $crew) {
            if ($crew['job'] == 'Screenplay') {
                $screenman = $crew['name'];
            }
        }

        $writer = '';
        foreach ($data['credits']['crew'] as $crew) {
            if ($crew['job'] == 'Writer') {
                $writer = $crew['name'];
            }
        }

        $crewMember = '';
        if (is_array($data['credits']['crew'])) {
            foreach ($data['credits']['crew'] as $crew) {
                if ($crew['job'] == 'Director') {
                    $crewMember = $crew['name'];
                }
            }
        }

        $mpaa_rating = '';
        $age_rating = '';
        $releases = $data['releases']['countries'];
        foreach ($releases as $release_item) {
            if ($release_item['iso_3166_1'] === 'US')
                $mpaa_rating = $release_item['certification'];
            if ($release_item['iso_3166_1'] === 'DE')
                $age_rating = $release_item['certification'];
        }

        $film = array(
            'tmdb_id' => $data['id'],
            'poster' => $images_small,
            'namelong' => $title,
            'name' => $ltitle,
            'age' => $age_rating,
            'year' => $year,
            'url' => $url,
            'aspect' => $imdb_url,
            'type' => $type,
            'soundtracks' => $homepage,
            'sound' => $data['status'],
            'genres' => $genre,
            'runtime' => $runtime,
            'ratinga' => $vote,
            'ratingb' => $mpaa_rating,
            'ratingc' => $data['vote_count'],
            'actors' => $actors,
            'writers' => $writer,
            'screenman' => $screenman,
            'director' => $crewMember,
            'story' => $description,
            'country' => $country,
            'language' => $languages,
            'datelocal' => $releasen,
            'color' => $revenue,
            'budget' => $budget,
            'locations' => $big_images,
            'namelocal' => $title,
            'tagline' => $tagline,
            'productionfirm' => $companies,
            'backdrops' => $imge,
        );

        return $film;
    }
}
?>
