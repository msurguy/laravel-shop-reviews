<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	$products = Product::all();
	return View::make('index', array('products'=>$products));
});

Route::get('products/{id}', function($id)
{
	$product = Product::find($id);
	$ratings = $product->ratings()->with('user')->approved()->notSpam()->orderBy('created_at','desc')->get();

	return View::make('products.single', array('product'=>$product,'ratings'=>$ratings));
});

Route::post('products/{id}', function($id)
{
	$input = array(
		'comment' => Input::get('comment'),
		'rating'  => Input::get('rating')
	);
	$rating = new Rating;

	$validator = Validator::make( $input, $rating->getCreateRules());

	if ($validator->passes()) {
		$rating->storeForProduct($id, $input['comment'], $input['rating']);
		return Redirect::to('products/'.$id.'#reviews-anchor')->with('review_posted',true);
	}
	return Redirect::to('products/'.$id.'#reviews-anchor')->withErrors($validator)->withInput();
});