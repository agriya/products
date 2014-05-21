<?php namespace Agriya\Products\Facades;

use Illuminate\Support\Facades\Facade;

class Products extends Facade {

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() { return 'products'; }

}