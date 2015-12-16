(function(){
	var app = angular.module('modal-directives', []);

	app.directive('loginModal', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/modal/login-modal.html'
		};
	});
	
	app.directive('signupModal', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/modal/signup-modal.html'
		};
	});
	
	app.directive('postModal', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/modal/post-modal.html'
		};
	});
	
	app.directive('orderModal', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/modal/order-modal.html'
		};
	});
	
	app.directive('editModal', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/modal/edit-modal.html'
		};
	});

	app.directive('quoteModal', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/modal/quote-modal.html'
		};
	});

	app.directive('detailModal', function(){
		return {
			restrict: 'E', 
			templateUrl: 'layout/modal/detail-modal.html'
		};
	});
})();