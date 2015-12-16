# README for Developers

1. [Data Modal](#data-model)
* [Data Structure](#data-structure)
	1. [mySQL](#mysql)
	* [mongoDB](#mongodb)
* [Full Stack Application Design](#full-stack-application-design)
	1. [Demand And UI](#demand-and-ui)
	* [Demand and Code Binding](#demand-and-code-binding)
	* [Inputs](#inputs)
* [Client-Side Developing](#client-side-developing)
	1. [JS Local Variables](#js-local-variables)
	* [JS Functions](#js-functions)
* [Server-Side Developing](#server-side-developing)
* [Server-Database Developing](#server-database-developing)
	1. [mySQL](#mysql-1)
	* [mongoDB](#mongodb-1)

## Data Model
Pseudocode in a mixed code fashion.

```yaml
user:
	_id: <int null="false">,
	password: <string null="false">,
	name: <string null="false">,
	tel: <string>,
	email: <string>,
	address: <string>,
	sellList: 
		[
			<int ref="product_id">,
			...
		],
	buyList: 
		[
			{
				product_id: <int null="false">,
				offering_price: <float null="false" prec="0.01">,
				transaction_status:
					<enum null="false" values="'pending', 'complete', 'closed'">
			},
			...
		]

product:
	_id: <int null="false">,
	name: <string null="false">,
	category: <string null="false">,
	demanding_price:
		<float null="false" prec="0.01">,
	image: <string>,
	seller_id: <int>,
	product_status:
		<enum null="false"
			values="'forSale', 'sold', 'deleted'">
	buyList: 
		[
			{
				buyer_id: <int null="false">,
				offering_price: <float null="false" prec="0.01">,
				transaction_status: 
					<enum null="false" values="'pending', 'complete', 'closed'">
			}
			...
		]
```

## Database Structure
### mySQL
#### Table users

```SQL
`uid` int(10) NOT NULL,
`password` varchar(20) NOT NULL,
`name` varchar(50) NOT NULL,
`tel` varchar(12) DEFAULT NULL,
`email` varchar(50) DEFAULT NULL,
`address` text,
PRIMARY KEY (`uid`)
INDEX ON (`uid`)
```

#### Table products

```SQL
`pid` int(10) NOT NULL,
`name` varchar(50) NOT NULL,
`category` varchar(50) NOT NULL,
`price` float DEFAULT NULL,
`image` varchar(100) DEFAULT NULL,
`sid` int(10) DEFAULT NULL,
`status` varchar(10) NOT NULL,
PRIMARY KEY (`pid`),
FOREIGN KEY (`sid`) REFERENCES `users`(`uid`)
ON DELETE SET NULL ON UPDATE CASCADE,
INDEX ON (`pid`),
INDEX ON (`sid`)
```

#### Table transactions

```SQL
`buyer_id` int(10) NOT NULL,
`product_id` int(10) NOT NULL,
`price` float NOT NULL,
`status` varchar(10) NOT NULL,
PRIMARY KEY (`buyer_id`,`product_id`),
FOREIGN KEY (`buyer_id`) REFERENCES `users`(`uid`)
ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`product_id`) REFERENCES `products`(`pid`)
ON DELETE CASCADE ON UPDATE CASCADE,
INDEX ON (`buyer_id`, `product_id`),
INDEX ON (`product_id`)
```

### mongoDB
#### Collection users

```json
{
	"_id":,
	"password":,
	"name":,
	"tel":,
	"email":,
	"address":,
	"selllist": [, ],
	"buyList": 
		[
			{
				"product_id":,
				"offering_price":,
				"transaction_status":
			},
		
		]
}
```

#### Collection products

```json
{
	"_id":,
	"name":,
	"category":,
	"demanding_price":,
	"image":,
	"seller_id":,
	"buylist":
		[
			{
				"buyer_id":,
				"offering_price":,
				"transaction_status":
			},

		]
}
```

## Full Stack Application Design
### Demand And UI
|User Demand|User Interface|
|---|---|
|User Login|`Login` -> `sign in`|
|User Logout|`Logout`|
|User Signup|`Sign Up` -> `submit`|
|User View Profile|`Profile`|
|User Update Profile|`Profile`: `Edit` -> `submit`|
|User Refresh All His/Her Information|`Refresh UserInfo`|
|User Nav Between Pages|`Previous` / `Next`|
|**Seller** Post New Product for Sale|`Post` -> `submit`|
|**Seller** View Sale History|`Sell`:`Refresh`|
|**Seller** View a 'forSale' Product|`Sell`: `forSale`|
|**Seller** Comfirm a Willing Buyer to Sell|`Sell`: `forSale` -> `Comfirm`|
|**Seller** Close a Sale|`Sell`: `forSale` -> `Delete Sale`|
|**Buyer** Search Product on `name` & `category`|`Shopping`: `Search`|
|**Buyer** Find in Result by `name`, `category` & `demanding_price` range|Type Constraints|
|**Buyer** View a Shopping Product|`Shopping`: `Purchase`|
|**Buyer** Order a Product|`Shopping`: `Purchase` -> `Submit`|
|**Buyer** View Order History|`Order`: `Refresh`|
|**Buyer** View a 'pending' Order|`Order`: `pending`|
|**Buyer** Promote a New Offering Price for an Existing Order|`Order`: `pending` -> `Submit`|
|**Buyer** Close an Existing Order|`Order`: `pending` -> `close`|
|**Seller/Buyer** View Contact Information of a 'sold' Product|`Sell`: `sold` / `Order`: `complete`|
|**Developer** View Binding Data|`Worcester`, `checkbox`|

### Demand and Code Binding
|User Demand|Client JS Function|Server|
|---|---|---|
|User Login|login()|login.php|
|User Logout|logout()|-|
|User Signup|signup()|signup.php|
|User View Profile|angular ng-show|-|
|User Update Profile|updateUser()|updateUser.php|
|User Refresh All His/Her Information|refreshUser(): login()|login.php|
|User Nav Between Pages|anular filter|-|
|**Seller** Post New Product for Sale|post()|post.php|
|**Seller** View Sale History|loadSell()|loadSell.php|
|**Seller** View a 'forSale' Product|setModal(sell_product, '#quoteModal')|-|
|**Seller** Comfirm a Willing Buyer to Sell|updateSale(modal_product, sale)|updateSale.php|
|**Seller** Close a Sale|updateSale(modal_product, sale)|updateSale.php|
|**Buyer** Search Product on `name` & `category`|loadshopping(shop_filter)|loadshopping.php|
|**Buyer** Find in Result by `name`, `category` & `demanding_price` range|angular filter|-|
|**Buyer** View a Shopping Product|setModal(product, '#orderModal')|-|
|**Buyer** Order a Product|updateOrder(modal_product, order)|updateOrder.php|
|**Buyer** View Order History|loadOrder()|loadOrder.php|
|**Buyer** View a 'pending' Order|setModal(product, '#orderModal')|-|
|**Buyer** Promote a New Offering Price for an Existing Order|updateOrder(modal_product, order)|updateOrder.php|
|**Buyer** Close an Existing Order|updateOrder(modal_product, order)|updateOrder.php|
|**Seller/Buyer** View Contact Information of a 'sold' Product|setModal(sell_product / buy_product, '#detailModal'): fetchUser($scope.modal_seller, product.seller_id), fetchUser($scope.modal_buyer, product.buyList[0].buyer_id)|fetchUser.php|
|**Developer** View Binding Data|angular ng-show|-|

### Inputs
#### Shopping panel (shopping-panel)
empty on load.

|Clickable&Input|Discription|
|---|---|
|keyword (type=text)|input keyword for search by name, or filter local searched result.|
|category (type=text)|input keyword for search by category, or filter local searched result.|
|price (type=number)|filter local searched result by price range.|

#### Login modal (login-modal)
sellProduct and buyProduct details not loaded. 

|Clickable&Input|Discription|
|---|---|
|ID (type=number, required)|input user ID number.|
|password (type=password, required)|input user password.|

#### Sign up modal (signup-modal)
|Clickable&Input|Discription|
|---|---|
|name (type=text, required)|name of user "firstname lastname".|
|phone # (type=text)|phone number.|
|email (type=email)|email address.|
|address (type=text)|address of user.|
|password (type=password, required)|password.|

#### Edit modal (edit-modal)
load current user information and allow user to modify or clear to empty.

|Clickable&Input|Discription|
|---|---|
|name (type=text, required)|name of user "firstname lastname".|
|phone # (type=text)|phone number.|
|email (type=email)|email address.|
|address (type=text)|address of user.|
|password (type=password, required)|password.|

#### Post Modal (post-modal)
|Clickable&Input|Discription|
|---|---|
|title (type=text, required)|product name.|
|category (type=text, required)|product category.|
|price (type=number)|seller demanding price.|
|url (type=url)|product picture url.|

#### Quote Modal (quote-modal)
|Clickable&Input|Discription|
|---|---|
|ID&Offering Price (type=radio)|select the buyer.|

#### Order Modal (order-modal)
display product details and allow buyers to purchase new, or update specific order.

|Clickable&Input|Discription|
|---|---|
|My New Offering Price (type=number, required if click Confirm)|input a new quote price|

## Client-Side Developing
### JS Local Variables
binding data display in index.html excluded from USE column expect for variables devMode and show.
#### For Global Use
|Var|Discription|INIT|USE|
|---|---|---|---|
|tab|control tab switch.|index.html|index.html|
|user|binding the current working user, null `_id` without login.|angular.app.js|index.html, nav-div.html, shopping-panel.html, profile-panel.html, order-panel.html, sell-panel.html, edit-modal, order-modal; fetchUser(), logout(), signup(), refreshUser(), loadSell(), loadOrder(), updateOrder(), post()|
|startIndices|control page start indices in ngRepeat filters.|angular.app.js|order-panel.html, sell-panel.html, shopping-panel.html|
|user_cache|binding cache for user input.|angular.app.js.|login-modal.html, signup-modal.html; login(), logout(), signup(), refreshUser()|
|product_cache|binding cache for product input. *FUTURE: Update Product*|angular.app.js|post-modal.html; post()|
|sell_products|list of sell history product details for sell panel.|angular.app.js|sell-panel.html; logout(), loadSell(), post()|
|buy_products|list of order history product details for order panel.|angular.app.js|order-panel.html; logout(), loadOrder(), updateOrder()|
|modal_product|the current working product in popup modal.|angular.app.js|detail-modal.html, order-modal.html, quote-modal.html; setModal()|

#### For Shopping Panel
|Var|Discription|INIT|USE|
|---|---|---|---|
|shop_products|list of product details for shopping panel.|angular.app.js|shopping-panel.html; loadShopping()|
|shop_filter|name and category keyword filter parameters in shopping panel.|angular.app.js|shopping-panel.html|
|shop_filter_price|price range filter parameters in shopping panel.|angular.app.js|shopping-panel.html|

#### For Quote Modal
|Var|Discription|INIT|USE|
|---|---|---|---|
|sale|binding sale information.|quote-modal.html|quote-modal.html|

#### For Order Modal
|Var|Discription|INIT|USE|
|---|---|---|---|
|order|binding order information.|-|order-modal.html|

#### For Detail Modal
|Var|Discription|INIT|USE|
|---|---|---|---|
|modal_seller|seller of current working product if product is sold.|angular.app.js|detail-modal.html; setModal()|
|modal_buyer|buyer of current working product if product is sold.|angular.app.js|detail-modal.html; setModal()|

#### For Developer Use
|Var|Discription|INIT|USE|
|---|---|---|---|
|devMode|control binding data display container.|header-div.html|header-div.html, index.html|
|show|control binding data display parts.|-|index.html|

### JS Functions
#### Function Description
|Funciton|Description|
|---|---|
|globalController .logout()|clear local login variables.|
|globalController .refreshUser()|refreash variable user.|
|globalController .loadShopping(filter)|load proper shopping products details.|
|globalController .fetchUser(target, _id)|fetch user information into `target` by `_id`|
|globalController .setModal(product, targetModal)|set information for target modal and show.|
|globalController .loadSell()|load proper sell history product details.|
|globalController .loadOrder()|load proper order history product details.|
|globalController .login()|check password and query only user information back from database, sellProduct and buyProduct details not loaded.|
|globalController .signup()|generate an id and insert to database with profile information.|
|globalController .updateUser()|update user information.|
|globalController .post()|generate an id and insert to databse with product information.|
|globalController .updateSale(product, sale)|confirm a buyer, set the confirmed transaction to complete while others are cancelled; Or, delete the sale, set product to deleted while all transactions on it are closed.|
|globalController .updateOrder(product, order)|update price and status if transaction exists, or insert a transaction to database; Or, set transaction status to 'closed'.|

#### Function Calling Stack
|Funciton|Called By|Calling|
|---|---|---|
|globalController .refreshUser()|updateOrder()|login()|
|globalController .fetchUser(target, _id)|setModal()|-|
|globalController .setModal(product, targetModal)|-|fetchUser($scope.modal_seller, product.seller_id), fetchUser($scope.modal_buyer, product.buyList[0].buyer_id)|
|globalController .loadOrder()|updateOrder()|-|
|globalController .login()|refreshUser()|-|
|globalController .updateOrder(product, order)|-|refreshUser(), loadOrder()|

## Server-Side Developing
|Request Target|Request Data|Response Data|
|---|---|---|
|login.php|user._id and password|a user object if success, null _id if failed|
|signup.php|a user object without _id|a user object if success, null _id if failed|
|updateUser.php|a user object|a user object if success, null _id if failed|
|post.php|a product object without _id|a product object (only _id) if success, null _id if failed|
|loadSell.php|array of pid, provided by user.sellList|array of products with specific pid plus full buyList(forSale), one item buyList(sold) or empty buyList(deleted) if success, empty array if failed|
|updateSale.php|product_id, user_id, and product_status|not null _id if success, null _id if failed|
|loadShopping.php|keyword to search by name and category|array of products with specific name and category, forSale status and empty buyList if success, empty array if failed|
|updateOrder.php|product_id, user_id, offereing_price and transaction_status|1 if success, 0 if failed|
|loadOrder.php|array of pid, provided by user.buyList|array of products with specific pid and one item buyList (only transaction by current user) if success, empty array if failed|
|fetchUser.php|_id|a user object if success, null _id if failed|

## Server-Database Developing
### mySQL
|Task|Join|`users`|`products`|`transactions`|
|---|---|---|---|---|
|User Login|-|query by `uid` AND `password`|query by `sid`|query by `buyer_id`|
|User Logout|-|-|-|-|
|User Signup|-|query(max/count) and insert|-|-|
|User View Profile|-|-|-|-|
|User Update Profile|-|update|-|-|
|User Refresh All His/Her Information (= User Login)|-|-|-|-|
|User Nav Between Pages|-|-|-|-|
|**Seller** Post New Product for Sale|-|-|query(max/count) and insert|-|
|**Seller** View Sale History|-|-|query by `sid`|query by `product_id`|
|**Seller** View a 'forSale' Product|-|-|-|-|
|**Seller** Comfirm a Willing Buyer to Sell|-|-|update by `pid`|update by `product_id` AND `buyer_id(=&!=)`|
|**Seller** Close a Sale|-|-|update by `pid`|update by `product_id`|
|**Buyer** Search Product on `name` & `category`|-|-|query by `status(='forSale')`, `name(LIKE)`, and `category(LIKE)`|-|
|**Buyer** Find in Result by `name`, `category` & `demanding_price` range|-|-|-|-|
|**Buyer** View a Shopping Product|-|-|-|-|
|**Buyer** Order a Product|-|-|-|query by `product_id` and `buyer_id`, (if exists) update by `product_id` and `buyer_id`, (else) insert|
|**Buyer** View Order History|`products` and `transactions` on `pid` = `product_id`|-|query by `pid`|query by `product_id` and `buyer_id`|
|**Buyer** View a 'pending' Order|-|-|-|-|
|**Buyer** Offer a New Offering Price for Existing Order (= **Buyer** Order a Product)|-|-|-|-|
|**Buyer** Close an Existing Order|-|-|-|update by `product_id` and `buyer_id`|
|**Seller or Buyer** View Contact Information of a Sold Product|-|query by `uid`|-|-|
|**Developer** View Binding Data|-|-|-|-|

### mongoDB
|Task|`products`|`users`|
|---|---|---|
