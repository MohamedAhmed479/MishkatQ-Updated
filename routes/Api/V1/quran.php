<?php

use App\Http\Controllers\Api\V1\QuranController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Quran API Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Quran API endpoints
|
*/

Route::controller(QuranController::class)->prefix("quran")->group(function () {
    // Chapters
    Route::get('/chapters',  'chapters');
    Route::get('/chapters/{chapterId}',  'chapter');
    Route::get('/chapters/{chapterId}/verses',  'chapterVerses');

    // Verses
    Route::get('/verses/random',  'randomVerse');
    Route::get('/verses/{verseId}',  'verse');
    Route::get('/verses/random/chapter/{chapterId}',  'randomVerseFromChapter');
    Route::get('/verses/random/juz/{juzNumber}',  'randomVerseFromJuz');
    Route::get('/verses/random/page/{pageNumber}',  'randomVerseFromPage');


    // Search
    // Route::get('/search',  'search');

    // Juzs
    Route::get('/juzs',  'juzs');
    Route::get('/juzs/{juzNumber}',  'juz');

    // Pages
    Route::get('/pages/{pageNumber}/verses',  'pageVerses');

    // Hizbs
    Route::get('/hizbs/{hizbNumber}/verses',  'hizbVerses');

    // Rubs
    Route::get('/rubs/{rubNumber}/verses',  'rubVerses');

    // Sajda Verses
    Route::get('/sajda-verses',  'sajdaVerses');

    // Words
    Route::get('/words',  'words');

    // Tafsirs
    Route::get('/tafsirs',  'tafsirs');

    // Reciters
    Route::get('/reciters',  'reciters');

    // Recitations
    Route::get('/recitations',  'recitations');

    // Statistics
    Route::get('/statistics',  'statistics');

});
