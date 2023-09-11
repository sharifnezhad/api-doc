<?php

use Illuminate\Support\Facades\Route;

Route::get('/'. config('apidoc.url'), function (){
   return view('apidoc::index');
});
