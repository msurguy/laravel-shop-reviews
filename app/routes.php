<?php

// Route for Homepage - displays all products from the shop
Route::get('/', function()
{
	$products = Product::all();
	return View::make('index', array('products'=>$products));
});

// Route that shows an individual product by its ID
Route::get('products/{id}', function($id)
{
	$product = Product::find($id);
	// Get all reviews that are not spam for the product and paginate them
	$reviews = $product->reviews()->with('user')->approved()->notSpam()->orderBy('created_at','desc')->paginate(100);

	return View::make('products.single', array('product'=>$product,'reviews'=>$reviews));
});

// Route that handles submission of review - rating/comment
Route::post('products/{id}', array('before'=>'csrf', function($id)
{
	$input = array(
		'comment' => Input::get('comment'),
		'rating'  => Input::get('rating')
	);
	// instantiate Rating model
	$review = new Review;

	// Validate that the user's input corresponds to the rules specified in the review model
	$validator = Validator::make( $input, $review->getCreateRules());

	// If input passes validation - store the review in DB, otherwise return to product page with error message 
	if ($validator->passes()) {
		$review->storeReviewForProduct($id, $input['comment'], $input['rating']);
		return Redirect::to('products/'.$id.'#reviews-anchor')->with('review_posted',true);
	}
	
	return Redirect::to('products/'.$id.'#reviews-anchor')->withErrors($validator)->withInput();
}));