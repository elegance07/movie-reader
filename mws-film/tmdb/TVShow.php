<?php
/**
 * 	This class handles all the data you can get from a TVShow
 *
 *	@package TMDB-V3-PHP-API
 * 	@author Alvaro Octal | <a href="https://twitter.com/Alvaro_Octal">Twitter</a>
 * 	@version 0.1
 * 	@date 11/01/2015
 * 	@link https://github.com/Alvaroctal/TMDB-PHP-API
 * 	@copyright Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 */

class TVShow  extends Option{

    //------------------------------------------------------------------------------
    // Get Variables
    //------------------------------------------------------------------------------

    /**
     * 	Get the TVShow's name
     *
     * 	@return string
     */
    public function getID() {
        return $this->_data['id'];
    }

    /**
     * 	Get the TVShow's name
     *
     * 	@return string
     */
    public function getName() {
        return $this->_data['name'];
    }

    /**
     * 	Get the TVShow's original name
     *
     * 	@return string
     */
    public function getOriginalName() {
        return $this->_data['original_name'];
    }

    /**
     * 	Get the TVShow's number of seasons
     *
     * 	@return int
     */
    public function getNumSeasons() {
        return $this->_data['number_of_seasons'];
    }

    /**
     *  Get the TVShow's number of episodes
     *
     * 	@return int
     */
    public function getNumEpisodes() {
        return $this->_data['number_of_episodes'];
    }

    /**
     *  Get a TVShow's season
     *
     *  @param int $numSeason The season number
     * 	@return int
     */
    public function getSeason($numSeason) {
        foreach($this->_data['seasons'] as $season){
            if ($season['season_number'] == $numSeason){
                $data = $season;
                break;
            }
        }
        return new Season($data);
    }

    /**
     *  Get the TvShow's seasons
     *
     * 	@return Season[]
     */
    public function getSeasons() {
        $seasons = array();

        foreach($this->_data['seasons'] as $data){
            $seasons[] = new Season($data, $this->getID());
        }

        return $seasons;
    }

    /**
     * 	Get the TVShow's Poster
     *
     * 	@return string
     */
    public function getPoster() {
        return $this->_data['poster_path'];
    }

	/**
	 * 	Get TV Genres
	 *
	 * 	@return Genre[]
	 */
	public function getTVGenres() {

		$genres = array();

		foreach ($this->_data['genres'] as $data) {
			$genres[] = new Option($data);
		}
		return  $genres;
	}
	/**
	 * 	Get TV Creator
	 *
	 * 	@return Creator[]
	 */
	public function getTVCreator() {

		$creators = array();

		foreach ($this->_data['created_by'] as $data) {
			$creators[] = new Option($data);
		}
		return  $creators;
	}

    /**
     * 	Get the TVShow's Backdrop
     *
     * 	@return string
     */
    public function getBackdrop() {
        return $this->_data['backdrop_path'];
    }

    /**
     * 	Get the TVShow's Overview
     *
     * 	@return string
     */
    public function getOverview() {
        return $this->_data['overview'];
    }

    /**
     * 	Get the TVShow's vote average
     *
     * 	@return int
     */
    public function getVoteAverage() {
        return $this->_data['vote_average'];
    }

    /**
     * 	Get the TVShow's Production date
     *
     * 	@return int
     */
    public function getProductionDate() {
        return $this->_data['first_air_date'];
    }

    /**
     * 	Get the TVShow's Date last episode
     *
     * 	@return int
     */
    public function getDateLastEpisode() {
		return empty($this->_data['last_episode_to_air']['air_date']) ? null : $this->_data['last_episode_to_air']['air_date'];
    }

    /**
     * 	Get the TVShow's Episode runtime
     *
     * 	@return int
     */
    public function getEpisodeRuntime() {
        return $this->_data['episode_run_time'];
    }

	/** 
	 * 	Get the TVShow's Runtime Episode
	 *
	 * 	@return string
	 */
	public function getRuntimeEpisode() {
		return empty($this->getEpisodeRuntime()[0]) ? null : $this->getEpisodeRuntime()[0];
	}

	/**
	 * 	Get the TVShow's companies
	 *
	 * 	@return Company[]
	 */
		public function getProduction() {		
		$production = array();

		foreach ($this->_data['production_companies'] as $data) {
			$production[] = new Option($data);
		}
		
		$productions = array_slice($production, 0, 3);
		return empty($productions) ? null : $productions;
	}

    /**
     * 	Get the TVShow's vote count
     *
     * 	@return int
     */
    public function getVoteCount() {
        return $this->_data['vote_count'];
    }

    /**
     * 	Get if the TVShow is in production
     *
     * 	@return boolean
     */
    public function getInProduction() {
        return $this->_data['in_production'];
    }
	
	    /**
     *  Get the Person's TVShow'sRoles
     *
     *  @return TVShowRole[]
     */
    public function getTVShowRoles() {
		  
        $TVShowRoles = array();

        foreach($this->_data['credits']['cast'] as $data){
            $TVShowRoles[] = new Option($data);
        }

		
		$TVShowRole = array_slice($TVShowRoles, 0, 9);
		
        return $TVShowRole;
    }

    //------------------------------------------------------------------------------
    // Export
    //------------------------------------------------------------------------------

    /**
     * 	Get the JSON representation of the TVShow
     *
     * 	@return string
     */
    public function getJSON() {
        return json_encode($this->_data, JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    public function getMediaType(){
        return self::MEDIA_TYPE_TV;
    }
}