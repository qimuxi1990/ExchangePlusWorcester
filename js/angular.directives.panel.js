(function(){
	var app = angular.module('panel-directives', []);

	// Directive Elements
	app.directive('shoppingPanel', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/panel/shopping-panel.html'
		};
	});

	app.directive('profilePanel', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/panel/profile-panel.html'
		};
	});

	app.directive('sellPanel', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/panel/sell-panel.html'
		};
	});

	app.directive('orderPanel', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/panel/order-panel.html'
		};
	});

	app.directive('aboutusPanel', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/panel/aboutus-panel.html'
		};
	});
})();