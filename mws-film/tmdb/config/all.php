<?php

//------------------------------------------------------------------------------
// Configuration to get all data
//------------------------------------------------------------------------------

// Global Configuration
$cnf['apikey'] = '6bed8eb6ed4a8714208fe7d8e0d9a4fc';
$cnf['lang'] = 'fr';
$cnf['timezone'] = 'Europe/Paris';
$cnf['adult'] = false;
$cnf['debug'] = true;

// Data Return Configuration - Manipulate if you want to tune your results
$cnf['appender']['movie'] = array('account_states', 'alternative_titles', 'credits', 'images','keywords', 'release_dates', 'trailers', 'videos', 'translations', 'similar', 'reviews', 'lists', 'changes', 'rating');
$cnf['appender']['tvshow'] = array('account_states', 'alternative_titles', 'changes', 'content_rating', 'credits', 'external_ids', 'images', 'keywords', 'rating', 'similar', 'translations', 'trailers', 'videos');
$cnf['appender']['season'] = array('changes', 'account_states', 'credits', 'external_ids', 'images', 'videos');
$cnf['appender']['episode'] = array('changes', 'account_states', 'credits', 'external_ids', 'images', 'rating', 'videos');
$cnf['appender']['person'] = array('movie_credits', 'tv_credits', 'combined_credits', 'external_ids', 'images', 'tagged_images', 'changes');
$cnf['appender']['collection'] = array('images');
$cnf['appender']['company'] = array('movies');

?>
