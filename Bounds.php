<?php
class Bounds{
	private $southWest;
	private $northEast;

	public function __construct(LngLat $southWest, LngLat $northEast){
		$this->southWest = $southWest;
		$this->northEast = $northEast;
	}

	// public function __debugInfo(){
	public function __toString(){
		$toPrint = <<<STR
southwest [{$this->southWest->lng}, {$this->southWest->lat}]
northEast [{$this->northEast->lng}, {$this->northEast->lat}]

STR;
		return $toPrint;
	}

	public function getSouthWest(){
		return $this->southWest;
	}

	public function getNorthEast(){
		return $this->northEast;
	}

	public function contains(LngLat $lnglat){
		$minLng = $this->southWest->lng;
		$maxLng = $this->northEast->lng;
		$minLat = $this->southWest->lat;
		$maxLat = $this->northEast->lat;
		$lng = $lnglat->lng;
		$lat = $lnglat->lat;

		// var_dump($minLng);
		// var_dump($maxLng);
		// var_dump($minLat);
		// var_dump($maxLat);
		// var_dump($lng);
		// var_dump($lat);
		// echo PHP_EOL;

		$scopeLng = abs($minLng-$maxLng) % 180;
		$distoMinLng = abs($lng-$minLng) % 180;
		$distoMaxLng = abs($lng-$maxLng) % 180;

		// var_dump($scopeLng);
		// var_dump($distoMinLng);
		// var_dump($distoMaxLng);
		// echo PHP_EOL;

		$scopeLat = abs($minLat-$maxLat);
		$distoMinLat = abs($lat-$minLat);
		$distoMaxLat = abs($lat-$maxLat);

		// var_dump($scopeLat);
		// var_dump($distoMinLat);
		// var_dump($distoMaxLat);
		// echo PHP_EOL;

		// var_dump($scopeLng >= ($distoMinLng + $distoMaxLng));
		// $scopeLat <= ($distoMinLat + $distoMaxLat)));
		// exit;

		// !!!取余运算 且是float 不能用=== 需要用 不等运算
		// 或者 abs($scopeLng - ($distoMinLng + $distoMaxLng)) < 阈值
		return ($scopeLng >= ($distoMinLng + $distoMaxLng) 
			&&
			$scopeLat >= ($distoMinLat + $distoMaxLat));
	}

	public function extendMeters($meters){
		if (!is_numeric($meters)) {
			trigger_error("extended meters must be number.");
		}

		if ($meters > 0) {
			$metersPerLng = 85276;
			$metersPerLat = 110940;
			$lngs = $meters / $metersPerLng;
			$lats = $meters / $metersPerLat;

			$this->southWest->lng -= $lngs;
			// var_dump($this->southWest->lng);
			if ($this->southWest->lng > 180){
				$this->southWest->lng -= 360;
			}
			if ($this->southWest->lng < -180){
				$this->southWest->lng += 360;
			}

			$this->southWest->lat -= $lats;
			// var_dump($this->southWest->lat);
			if ($this->southWest->lat > BD_MAP_LAT_MAX){
				$this->southWest->lat = BD_MAP_LAT_MAX;
			}
			if ($this->southWest->lat < BD_MAP_LAT_MIN){
				$this->southWest->lat = BD_MAP_LAT_MIN;
			}

			$this->northEast->lng += $lngs;
			if ($this->northEast->lng > 180){
				$this->northEast->lng -= 360;
			}
			if ($this->northEast->lng < -180){
				$this->northEast->lng += 360;
			}
			
			$this->northEast->lat += $lats;
			if ($this->northEast->lat > BD_MAP_LAT_MAX) {
				$this->northEast->lat = BD_MAP_LAT_MAX;
			}
			if ($this->northEast->lat < BD_MAP_LAT_MIN){
				$this->northEast->lat = BD_MAP_LAT_MIN;
			}
		}
	}
}
?>