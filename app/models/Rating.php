<?php

class Rating extends Eloquent
{

    public function getCreateRules()
    {
        return array(
            'comment'=>'required|min:10',
            'rating'=>'required'
        );
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function product()
    {
        return $this->belongsTo('Product');
    }

    public function scopeApproved($query)
    {
       	return $query->where('approved', true);
    }

    public function scopeSpam($query)
    {
       	return $query->where('spam', true);
    }

    public function scopeNotSpam($query)
    {
       	return $query->where('spam', false);
    }

    public function getTimeagoAttribute()
    {
    	$date = \Carbon\Carbon::createFromTimeStamp(strtotime($this->updated_at))->diffForHumans();
    	return $date;
    }

    public function storeForProduct($productID, $comment, $rating)
    {
        $product = Product::find($productID);

        //$this->user_id = Auth::user()->id;
        $this->comment = $comment;
        $this->rating = $rating;
        $product->ratings()->save($this);

        // recalculate ratings for the specified product
        $product->recalculateRating($rating);
    }
}