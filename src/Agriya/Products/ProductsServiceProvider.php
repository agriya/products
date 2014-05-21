<?php namespace Agriya\Products;

use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('agriya/products');
		$this->app['validator']->resolver(function($translator, $data, $rules, $messages)
	    {
	        return new UserAccountValidator($translator, $data, $rules, $messages);
	    });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['products'] = $this->app->share(function($app)
	    {
	    	return new Products;
	    });
	    $this->app->booting(function()
		{
		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
		  $loader->alias('Products', 'Agriya\Products\Facades\Products');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('products');
	}

}
