<?php

Route::redirect('/', '/item');

Auth::routes();

// Updates
Route::get('/update', 'UpdateController@index')->name('update.index');
Route::post('/update', 'UpdateController@update')->name('update.update');

// Items
Route::get('/item', 'ItemController@index')->name('item.index');
Route::post('/item', 'ItemController@refresh')->name('item.refresh');