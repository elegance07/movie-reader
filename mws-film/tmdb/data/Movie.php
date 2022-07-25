<?php
/**
 * 	This class handles all the data you can get from a Movie
 *
 *	@package TMDB-V3-PHP-API
 * 	@author Alvaro Octal | <a href="https://twitter.com/Alvaro_Octal">Twitter</a>
 * 	@version 0.2
 * 	@date 02/04/2016
 * 	@link https://github.com/Alvaroctal/TMDB-PHP-API
 * 	@copyright Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 */
 

 
class Movie extends Option{

	private $_tmdb;

	//------------------------------------------------------------------------------
	// Get Variables
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the Movie's title
	 *
	 * 	@return string
	 */
	public function getTitle() {
		return $this->_data['title'];
	}

	/** 
	 * 	Get the Movie's original title
	 *
	 * 	@return string
	 */
	public function getOriginalTitle() {
		return $this->_data['original_title'];
	}

    /**
     * 	Get the Movie Overview
     *
     * 	@return string
     */
    public function getOverview() {
        return $this->_data['overview'];
    }

	/** 
	 * 	Get the Movie's tagline
	 *
	 * 	@return string
	 */
	public function getTagline() {
		return $this->_data['tagline'];
	}

	/** 
	 * 	Get the Movie's tagline
	 *
	 * 	@return string
	 */
	public function getReleaseDate() {
		return $this->_data['release_date'];
	}

	/** 
	 * 	Get the Movie's Production
	 *
	 * 	@return string
	 */
	public function getProduction() {		
		$production = array();

		foreach ($this->_data['production_companies'] as $data) {
			$production[] = new Option($data);
		}
		
		$productions = array_slice($production, 0, 3);
		return  $productions;
	}

	/** 
	 * 	Get the Movie's countries
	 *
	 * 	@return string
	 */
	public function getCountries() {		
		$countries = array();

		foreach ($this->_data['production_countries'] as $data) {
			$countries[] = new Option($data);
		}
		return  $countries;
	}

	/** 
	 * 	Get the Movie Directors IDs
	 *
	 * 	@return array(int)
	 */
	public function getDirectorIds() {

		$director_ids = [];

		$crew = $this->getCrew();

		/** @var Person $crew_member */
        foreach ($crew as $crew_member) {

			if ($crew_member->getJob() === Person::JOB_DIRECTOR){
				$director_ids[] = $crew_member->getID();
			}
		}
		return $director_ids;
	}

    /**
     * 	Get the Movie Production date
     *
     * 	@return int
     */
    public function getProductionDate() {
        return $this->_data['release_date'];
    }

    /** 
     *  Get the Option Runtime
     *
     *  @return int
     */
    public function getRuntime() {
        return $this->_data['runtime'];
    }
	
	public function strCut($string, $max = 60, $end = '...') {
    if (strlen($string) > $max) {
        $string = substr($string, 0, $max - strlen($end)).$end;
    }
    return $string;
}

    /**
     *  Get the Person's MovieRoles
     *
     *  @return MovieRole[]
     */
    public function getMoviesRoles() {
		  
        $movieRoles = array();

        foreach($this->_data['credits']['cast'] as $data){
            $movieRoles[] = new Option($data);
        }

		
		$movieRole = array_slice($movieRoles, 0, 9);
		
        return $movieRole;
    }

	/** 
	 * 	Get the Movie's trailers
	 *
	 * 	@return array
	 */
	public function getTrailers() {
		return $this->_data['trailers'];
	}

	/** 
	 * 	Get the Movie's trailer
	 *
	 * 	@return string
	 */
	public function getTrailer() {
		return empty($this->getTrailers()['youtube'][0]['source']) ? null : $this->getTrailers()['youtube'][0]['source'];
	}

	/** 
	 * 	Get the Movie's genres
	 *
	 * 	@return Genre[]
	 */
	public function getGenres() {
		$genres = array();

		foreach ($this->_data['genres'] as $data) {
			$genres[] = new Option($data);
		}
		return  $genres;
	}

	/** 
	 * 	Get the Movie's reviews
	 *
	 * 	@return Review[]
	 */
	public function getReviews() {
		$reviews = array();

		foreach ($this->_data['review']['result'] as $data) {
			$reviews[] = new Review($data);
		}

		return $reviews;
	}

	/**
	 * 	Get the Movie's companies
	 *
	 * 	@return Company[]
	 */
	public function getCompanies() {
		$companies = array();
		
		foreach ($this->_data['production_companies'] as $data) {
			$companies[] = new Company($data);
		}
		//$companie = array_slice($companies, 0, 9);
		return $companies;
	}

	//------------------------------------------------------------------------------
	// Import an API instance
	//------------------------------------------------------------------------------

	/**
	 *	Set an instance of the API
	 *
	 *	@param TMDB $tmdb An instance of the api, necessary for the lazy load
	 */
	public function setAPI($tmdb){
		$this->_tmdb = $tmdb;
	}

	//------------------------------------------------------------------------------
	// Export
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the JSON representation of the Movie
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
		return self::MEDIA_TYPE_MOVIE;
	}
}
