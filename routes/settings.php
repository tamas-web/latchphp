<?php

use App\Routing\Route;
use App\Auth\{ MustBeLoggedIn, Can, CanAnyGroup, VerifyNonce };
use App\Controllers\Settings\{ SettingsController, RolesController, PluginsController };

// Role / permission management is a delegatable capability: any role granted the
// matching roles.* permission may use it (the super-admin bit bypasses can() as usual).

// Administration landing - any user with access to at least one admin area.
Route::get( '/admin', [ SettingsController::class, 'index' ] )
	->middleware( [ MustBeLoggedIn::class, [ CanAnyGroup::class, 'settings', 'roles', 'plugins', 'system' ] ] );

// Application settings - gated by settings.manage.
Route::get( '/admin/settings/application', [ SettingsController::class, 'appForm' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'settings.manage' ] ] );

Route::post( '/api/settings/app', [ SettingsController::class, 'appUpdate' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'settings.manage' ] ] );

Route::get( '/admin/settings/roles-permissions', [ RolesController::class, 'rolesAndPermissions' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'roles.view' ] ] );

Route::get( '/admin/settings/roles-permissions/roles', [ RolesController::class, 'roles' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'roles.view' ] ] );

Route::get( '/admin/settings/roles-permissions/roles/add', [ RolesController::class, 'roleAddForm' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'roles.add' ] ] );

Route::post( '/api/settings/roles/add', [ RolesController::class, 'roleAddApi' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'roles.add' ] ] );

Route::get( '/admin/settings/roles-permissions/roles/{id}/delete', [ RolesController::class, 'roleDeleteForm' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'roles.delete' ] ] );

Route::post( '/api/settings/roles/{id}/delete', [ RolesController::class, 'roleDeleteApi' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'roles.delete' ] ] );

Route::get( '/admin/settings/roles-permissions/roles/{id}/edit', [ RolesController::class, 'roleEditForm' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'roles.edit' ] ] );

Route::post( '/api/settings/roles/{id}', [ RolesController::class, 'roleEditApi' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'roles.edit' ] ] );

Route::get( '/admin/settings/roles-permissions/permissions', [ RolesController::class, 'permissions' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'roles.view' ] ] );

// Plugin management - each operation gated by its own plugins.* leaf.

Route::get( '/admin/settings/plugins', [ PluginsController::class, 'index' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'plugins.view' ] ] );

Route::get( '/admin/settings/plugins/upload', [ PluginsController::class, 'uploadForm' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'plugins.add' ] ] );

Route::post( '/api/settings/plugins/upload', [ PluginsController::class, 'upload' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'plugins.add' ] ] );

Route::post( '/api/settings/plugins/install', [ PluginsController::class, 'install' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'plugins.add' ] ] );

Route::post( '/api/settings/plugins/uninstall', [ PluginsController::class, 'uninstall' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'plugins.delete' ] ] );

Route::post( '/api/settings/plugins/activate', [ PluginsController::class, 'activate' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'plugins.activate' ] ] );

Route::post( '/api/settings/plugins/deactivate', [ PluginsController::class, 'deactivate' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class, [ Can::class, 'plugins.disable' ] ] );
