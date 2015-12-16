# ExchangePlusWorcester

## Promotion

Tired of purchasing and refunding on Amazon? Annoyed by spam messages and emails after selling via Craigslist? Now we have a better solution:

Exchange Plus Worcester, Exchange & MORE!

By combining data from Craigslist with mechanism from Amazon, we come up with this improved solution, Exchage+ - Worcester. The contact information is enclosed by a Purchase system and can only be viewed by sellers and buyers of the item.

This applicaiton is a beta/research version by Jing Yang and Muxi Qi for our Database Management Course. Only Worcester Market and limited utilities are supported.
## Contributor

* Jing Yang
* Muxi Qi

## Copyright

* Worcester Polytechnic Institute
* Craigslist

## Application User Manual
### Quick Start
|User Demand|User Operation|
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