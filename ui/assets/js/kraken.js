(function () {

	angular
		.module('kraken', [
			'ngAnimate', 'ui.router', 'mgcrea.ngStrap', 'swamp'
		]);

	function AppCfg ($stateProvider, $urlRouterProvider, $sceProvider)
	{
		$sceProvider.enabled(false);

		$urlRouterProvider
			.otherwise('/');

		$stateProvider
			.state('home', {
				url: '/',
				templateUrl: '/partials/home.html'
			})

			.state('404', {
				url: '/404',
				templateUrl: '/partials/404.html'
			})
		;
	}

	AppCfg.$inject = ['$stateProvider', '$urlRouterProvider', '$sceProvider'];

	angular
		.module('kraken')
		.config(AppCfg);

	function HomeCtrl ()
	{

	}

	HomeCtrl.$inject = ['SomeService'];

	angular
		.module('kraken')
		.controller('HomeCtrl', HomeCtrl);

	function SomeService ()
	{

	}

	angular
		.module('app')
		.service('SomeService', SomeService);

})();
