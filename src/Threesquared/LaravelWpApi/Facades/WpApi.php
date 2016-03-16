<?php namespace Threesquared\LaravelWpApi\Facades;

use Illuminate\Support\Facades\Facade;
use Threesquared\LaravelWpApi\WpApi;

class WpApi extends Facade {

    protected static function getFacadeAccessor() { return WpApi::class; }

}
