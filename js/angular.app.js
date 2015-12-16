(function(){
	var app = angular.module('EPW', ['div-directives', 'panel-directives', 'modal-directives']);

	// globalController
	app.controller('globalController', ['$scope', '$http', '$filter', function($scope, $http, $filter){
		$scope.user = {};
		$scope.user._id = null;
		$scope.startIndices = {'Shopping': 0, 'Sell' : 0, 'Order' : 0};
		$scope.shop_products = [];
		$scope.shop_filter = {'name' : '', 'category' : '', 'seller_id' : ''}; // not null filter
		$scope.shop_filter_price = {'lower' : 0, 'upper' : 10000};
		$scope.sell_products = [];
		$scope.buy_products = [];
		$scope.modal_product = {};
		$scope.modal_seller = {};
		$scope.modal_buyer = {};

		$scope.user_cache = {};
		$scope.product_cache = {};
		// TODO may improve by adding failure alerts
		this.fetchUser = function(target, _id) {
			// Send POST request to check user-password, if correct fetch back into user
			$http({
				method: 'POST', // if using method 'GET', nothing will be back
				url: 'php/sql/fetchUser.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: _id
			}).then(function successCallback(response) {
				// this callback will be called asynchronously
				// when the response is available
				if(response.data._id != null){
					target._id = response.data._id;
					target.name = response.data.name;
					target.tel = response.data.tel;
					target.email = response.data.email;
					target.address = response.data.address;
				}
			}, function errorCallback(response) {
				// called asynchronously if an error occurs
				// or server returns response with an error status.
			});
		}
		this.login = function(){
			// Send POST request to check user-password, if correct fetch back into user
			$http({
				method: 'POST', // if using method 'GET', nothing will be back
				url: 'php/sql/login.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: $scope.user_cache
			}).then(function successCallback(response) {
				// this callback will be called asynchronously
				// when the response is available
				if(response.data._id != null){
					$scope.user = response.data;
					$scope.user_cache = {};
					$('#loginModal').modal('hide');
				}
			}, function errorCallback(response) {
				// called asynchronously if an error occurs
				// or server returns response with an error status.
			});
		};
		this.logout = function(){
			$scope.user = {};
			$scope.user._id = null;
			$scope.pageNums = {'Shooping': 0, 'Sell' : 0, 'Order' : 0};
			$scope.sell_products = [];
			$scope.buy_products = [];
			$scope.user_cache = {};
		};
		this.signup = function(){
			// Send POST request to create user, if success fetch back into user
			$http({
				method: 'POST', // if using method 'GET', nothing will be back
				url: 'php/sql/signup.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: $scope.user_cache
			}).then(function successCallback(response) {
				// this callback will be called asynchronously
				// when the response is available
				if(response.data._id != null){
					$scope.user = response.data;
					$scope.user_cache = {};
					$('#signupModal').modal('hide');
				}
			}, function errorCallback(response) {
				// called asynchronously if an error occurs
				// or server returns response with an error status.
			});
		};
		this.updateUser = function(){
			$http({
				method: 'POST', // if using method 'GET', nothing will be back
				url: 'php/sql/updateUser.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: $scope.user
			}).then(function successCallback(response) {
				// this callback will be called asynchronously
				// when the response is available
				if(response.data._id != null){
					$scope.user = response.data;
					$('#editModal').modal('hide');
				}
			}, function errorCallback(response) {
				// called asynchronously if an error occurs
				// or server returns response with an error status.
			});
		};
		this.refreshUser = function(){
			$scope.user._id = Number($scope.user._id);
			$scope.user_cache = $scope.user;
			this.login();
			$scope.user_cache = {};
		};
		this.loadSell = function() {
			var sellList = $scope.user.sellList;
			var loadList = sellList;
			if(loadList.length != 0) {
				$http({
					method: 'POST', // if using method 'GET', nothing will be back
					url: 'php/sql/loadSell.php',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					data: loadList
				}).then(function successCallback(response) {
					// this callback will be called asynchronously
					// when the response is available
					if(response.data.length != 0){
						$scope.sell_products = response.data;
					}
				}, function errorCallback(response) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
				});
			}
			else
				$scope.sell_products = [];
		};
		this.loadOrder = function(){
			var buyList = $scope.user.buyList;
			var loadList = [];
			for (var i = 0; i < buyList.length; i++){
				loadList[i] = buyList[i]['product_id'];
			}
			if(loadList.length != 0) {
				$http({
					method: 'POST', // if using method 'GET', nothing will be back
					url: 'php/sql/loadOrder.php',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					data: [$scope.user._id, loadList]
				}).then(function successCallback(response) {
					// this callback will be called asynchronously
					// when the response is available
					if(response.data.length != 0){
						$scope.buy_products = response.data;
					}
				}, function errorCallback(response) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
				});
			}
			else
				$scope.buy_products = [];
		};
		this.loadShopping = function(filter) {
			$http({
				method: 'POST', // if using method 'GET', nothing will be back
				url: 'php/sql/loadShopping.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: filter
			}).then(function successCallback(response) {
				// this callback will be called asynchronously
				// when the response is available
				if(response.data.length != 0){
					$scope.shop_products = response.data;
				}
			}, function errorCallback(response) {
				// called asynchronously if an error occurs
				// or server returns response with an error status.
			});
		};
		this.setModal= function(product, targetModal) {
			$scope.modal_product = product;
			if(product.product_status == 'sold') {
				this.fetchUser($scope.modal_seller, product.seller_id);
				this.fetchUser($scope.modal_buyer, product.buyList[0].buyer_id);
			}
			$(targetModal).modal('show');
		};
		this.updateOrder = function(product, order) {
			if($scope.user._id != null && (order.offering_price != null || order.transaction_status == 'closed')){
				if(order.offering_price == null)
					order.offering_price = 0;
				var data = {
					"product_id": product._id, 
					"buyer_id": $scope.user._id, 
					"offering_price": order.offering_price, 
					"transaction_status": order.transaction_status
				};
				$http({
					method: 'POST', // if using method 'GET', nothing will be back
					url: 'php/sql/updateOrder.php',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					data: data
				}).then(function successCallback(response) {
					// this callback will be called asynchronously
					// when the response is available
					if(response.data == 1){
						var exists_buyList = false;
						for (var i = 0; i < $scope.user.buyList.length && !exists_buyList; i++){
							if($scope.user.buyList[i]['product_id'] == data.product_id){
								if(data.transaction_status == 'pending'){
									$scope.user.buyList[i]['offering_price'] = data.offering_price;
								}
								$scope.user.buyList[i]['transaction_status'] = data.transaction_status;
								exists_buyList = true;
							}
						}
						if(!exists_buyList){
							var user_buy = {
								'product_id': data.product_id,
								'offering_price': data.offering_price,
								'transaction_status': data.transaction_status
							};
							$scope.user.buyList.push(user_buy);
						}
						var exists_buyProducts = false;
						for (var j = 0; j < $scope.buy_products.length && !exists_buyProducts; j++){
							if($scope.buy_products[j]._id == data.product_id){
								if(data.transaction_status == 'pending'){
									$scope.buy_products[j].buyList[0].offering_price = data.offering_price;
								}
								$scope.buy_products[j].buyList[0].transaction_status = data.transaction_status;
								exists_buyProducts = true;
							}
						}
						if(!exists_buyProducts){
							var product_buy = {
								'buyer_id': data.buyer_id,
								'offering_price': data.offering_price,
								'transaction_status': data.transaction_status
							};
							product.buyList.push(product_buy);
							$scope.buy_products.push(product);
						}
						order.offering_price = null;
						$('#orderModal').modal('hide');
					}
				}, function errorCallback(response) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
				});
				// auto indent marker
			}
		};
		this.updateSale = function(product, sale) {
			var data = {
				"product_id": product._id,
				"buyer_id": sale.buyer_id,
				"product_status": sale.product_status
			};
			$http({
				method: 'POST', // if using method 'GET', nothing will be back
				url: 'php/sql/updateSale.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: data
			}).then(function successCallback(response) {
				// this callback will be called asynchronously
				// when the response is available
				if(response.data._id != null){
					product.product_status = data.product_status;
					product.buyList = $filter('filter')(product.buyList, {buyer_id: data.buyer_id});
					sale.buyer_id = -1;
					$('#quoteModal').modal('hide');
				}
			}, function errorCallback(response) {
				// called asynchronously if an error occurs
				// or server returns response with an error status.
			});
		};
		this.post = function() {
			$scope.product_cache.seller_id = $scope.user._id;
			$http({
				method: 'POST', // if using method 'GET', nothing will be back
				url: 'php/sql/post.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: $scope.product_cache
			}).then(function successCallback(response) {
				// this callback will be called asynchronously
				// when the response is available
				if(response.data._id != null){
					$scope.user.sellList.push(response.data._id);
					var product = {
						'_id': response.data._id,
						'name': $scope.product_cache.name,
						'category': $scope.product_cache.category,
						'demanding_price': $scope.product_cache.demanding_price,
						'image': $scope.product_cache.image,
						'seller_id': $scope.product_cache.seller_id,
						'product_status': 'forSale',
						'buyList': []
					};
					$scope.sell_products.push(product);
					$scope.product_cache = {};
					$('#postModal').modal('hide');
				}
			}, function errorCallback(response) {
				// called asynchronously if an error occurs
				// or server returns response with an error status.
			});
			// auto indent marker
		}
	}]);

	//  filter price
	app.filter('price', function() {
		return function( items, filter ) {
			var filtered = [];
			var min = parseInt(filter.lower);
			var max = parseInt(filter.upper);
			// If demanding_price is with the range, considering nulls
			angular.forEach(items, function(item) {
				if( item.demanding_price == null || ( (item.demanding_price >= min || filter.lower == null) && (item.demanding_price <= max || filter.upper == null) )) {
					filtered.push(item);
				}
			});
			return filtered;
		};
	});
	//  filter notBought
	app.filter('notBought', ['$filter', function($filter) {
		return function( items, filter ) {
			var filtered = [];
			// If time is with the range
			angular.forEach(items, function(item) {
				if((filter == null) || ($filter('filter')(filter, {product_id: item._id, transaction_status: 'pending'}).length == 0)) {
					filtered.push(item);
				}
			});
			return filtered;
		};
	}]);
})();
