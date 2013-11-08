<?php

class Product extends Eloquent
{
	public function ratings()
	{
	    return $this->hasMany('Rating');
	}

    public function recalculateRating($rating)
    {
	    $rating = $this->ratings()->avg('rating');
		$this->rating_cache = round($rating,1);
		$this->rating_count = $this->rating_count + 1;
    	$this->save();
    }
}