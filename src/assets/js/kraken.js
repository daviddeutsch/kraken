(function () {

angular
	.module('kraken', []);

function HomeCtrl () {

}

HomeCtrl.$inject = ['SomeService'];

angular
	.module('kraken')
	.controller('HomeCtrl', HomeCtrl);

function SomeService () {

}

angular
	.module('app')
	.service('SomeService', SomeService);

})();
