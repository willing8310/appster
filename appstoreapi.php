<?php

class appstore {

    const ITUNES_LOOKUP_URL  = "http://itunes.apple.com/us/lookup/id";
    protected $appId;
    public $averageRating;
    public $description;
    public $releasenotes;
    public $genre;
    public $released;            
    public $sellerurl;            
    public $developerUrl;
    public $isFree; 
    public $iTunesWebUrl;
    public $largeThumbnail;
    public $numberOfRatings;
    public $numberOfRatingsForCurrentVersion;
    public $price;
    public $size;
    public $seller;
    public $screenShots;
    public $smallThumbnail;
    public $version;
    
    public function __construct($appId)
    {
        $this->appId = $appId;
        $requestUrl = self::ITUNES_LOOKUP_URL . $this->appId;


	$cache_key = "itunes_store_{$this->appId}";

	if (false === ($parsedResponse = get_transient($cache_key))) {
       		$response = wp_remote_retrieve_body(wp_remote_get($requestUrl));
       		$parsedResponse = json_decode($response, true);
       		set_transient($cache_key,$parsedResponse,60*60*12);
	}

        if ($parsedResponse['resultCount'] == 1)
        {
            $parsedResponse = $parsedResponse['results'][0];
    
            $this->averageRating = $parsedResponse['averageUserRating'];
            $this->description =  nl2br($parsedResponse['description']);
            $this->releasenotes =  nl2br($parsedResponse['releaseNotes']);
	    $this->seller = $parsedResponse['artistName'];
	    $this->genre = $parsedResponse['primaryGenreName'];
	    $this->size = $parsedResponse['fileSizeBytes'];
	    $this->sellerurl = $parsedResponse['sellerUrl'];
	    $this->released = $parsedResponse['releaseDate'];
            $this->developerUrl = $parsedResponse['artistViewUrl'];
            $this->iTunesWebUrl = $parsedResponse['trackViewUrl'];
            $this->largeThumbnail = $parsedResponse['artworkUrl100'];
            $this->name = $parsedResponse['trackName'];
            $this->numberOfRatings = $parsedResponse['userRatingCount'];
            $this->numberOfRatingsForCurrentVersion = $parsedResponse['userRatingCountForCurrentVersion'];
            $this->price = $parsedResponse['price'];
            $this->isFree = $this->price == 0;
            $this->iPhoneScreenshots = $parsedResponse['screenshotUrls'];
            $this->iPadScreenshots = $parsedResponse['ipadScreenshotUrls'];
            $this->smallThumbnail = $parsedResponse['artworkUrl60'];
            $this->version = $parsedResponse['version'];            
        } 
    }

	function released( $format = false ) {
		if ( $format == false )
			return $this->released;
		
		$date    = $this->released;
		$year    = substr($date, 0,  4);
		$month   = substr($date, 5,  2);
		$day     = substr($date, 8,  2);
		$hour    = substr($date, 11, 2);
		$minute  = substr($date, 14, 2);
		$second  = substr($date, 17, 2);
		$search  = array( '/Y/', '/M/',  '/D/', '/H/', '/m/',   '/S/' );
		$replace = array( $year, $month, $day,  $hour, $minute, $second);
		return preg_replace($search, $replace, $format);
	}

	function size( $shorten = true, $decimal = 'point', $space = false ) {
		if ( $shorten == false )  
			return $this->size;
		
		$size = $this->size;
		if ($size > 1024) {  
			$size = round($size / 1024);
			$unit = 'KB';
			if ($size > 1024) {
				$size = round($size / 1024, 1);
				$unit = 'MB';
				if ($size > 1024) {
					$size = round($size / 1024, 1);
					$unit = 'GB';
				}
			}
		} else {
			$unit = 'Byte';
		}
		
		if ( 'comma' == $decimal )  
			$size = preg_replace('/\./', ',', $size);
			
		if ( $space == true )  
			return $size.' '.$unit;
			
		return $size.$unit;  
		
	}

}
