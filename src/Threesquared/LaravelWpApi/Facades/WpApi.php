<?php namespace Threesquared\LaravelWpApi\Facades;

use Illuminate\Support\Facades\Facade;
use Threesquared\LaravelWpApi\WpApi as WordpressApi;

class WpApi extends Facade {

    protected static function getFacadeAccessor() { return WordpressApi::class; }

}
