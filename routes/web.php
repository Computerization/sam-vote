<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('vote');
});

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home', function () {
    return redirect()->route('vote');
});

// The vote pages are for logged in users only
Route::middleware(['auth'])->group(function () {
    // List all vote_group and vote enries inside a vote_group
    Route::get('vote/', 'VoteGroupController@index')->name('vote');
    Route::get('vote/group/{id}', 'VoteGroupController@show');

    // Create new vote group
    Route::get('group/create', 'VoteGroupController@create');
    Route::post('group', 'VoteGroupController@store');

    // Create vote objects for a specific vote group
    Route::get('vote/create/{gid}', 'VoteController@create');
    Route::post('vote/create', 'VoteController@store');
    // Adding vote objects with known criterias
    Route::get('vote/add/{vid}', 'VoteController@addcriteria');
    Route::post('vote/add/{vid}', 'VoteController@addvote');

    Route::get('vote/all', 'VoteController@index');

    // View and submitting reponses to a single vote
    Route::get('vote/{id}', 'VoteController@show');
    Route::post('vote/{id}/submit', 'VoteController@submit');

    // Export all responses for a single vote
    Route::get('vote/{vote}/export', 'VoteController@export');

    Route::get('vote/{id}/stat', 'VoteController@stat');
    // Route::get('vote/{id}/edit', 'VoteController@edit');
    // Route::post('vote/{id}/edit', 'VoteController@update');
    // Route::delete('vote/{id}/delete', 'VoteController@destroy');
    // Route::delete('vote/{id}/clear', 'VoteController@clearResponse');
    // Route::delete('question/{id}', 'QuestionController@destroy');

    // Route::put('group/{id}', 'VoteGroupController@update');
    // Route::delete('group/{id}', 'VoteGroupController@destroy');
    // Route::get('group/{id}/addvote', 'VoteGroupController@selectvote');
    // Route::post('group/{id}/addvote', 'VoteGroupController@addvote');
    // Route::post('group/{id}/rmvote', 'VoteGroupController@rmvote');

});
