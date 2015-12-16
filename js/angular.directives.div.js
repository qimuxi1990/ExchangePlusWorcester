(function(){
	var app = angular.module('div-directives', []);

	// Directive Elements
	app.directive('navDiv', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/nav-div.html'
		};
	});

	app.directive('headerDiv', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/header-div.html'
		};
	});

	app.directive('footerDiv', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/footer-div.html'
		};
	});
})();