<?php

use App\Livewire\LinkPreview;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/link-preview', LinkPreview::class)->name('link.preview');
