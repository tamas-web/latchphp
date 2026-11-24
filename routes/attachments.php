<?php

use App\Routing\Route;

Route::get( '/attachments/{id}/{id}/{string}/{string}', [ \App\Controllers\AttachmentsController::class, 'get' ] );