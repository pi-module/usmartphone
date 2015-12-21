usmartphone
===========


## Usage
After install this module on your website , On your android app, you need send reuest to this links : 

#### Login
for do login , make login form on your app and get user `identity` and `credential` and send to : `http://www.YOURSITE.com/usmartphone/login`, result is json array by this parameters :
* `message` : website message
* `login` : 0 or 1 , if user login true , return 1
* `identity` : user identity ( username )
* `email` : user email
* `name` : display name
* `avatar` : avatar image url
* `uid` : user id
* `sessionid` : user session id, you need keep it on your app and use it on check method

#### Logout
Set user logout link on your app for destroy session and make user logout, just send empty resuest to this link : `http://www.YOURSITE.com/usmartphone/logout`

#### Check
For each action like make vote and ... you need check user is login or not , if login you can send request to website, for check login just do this URL : `http://www.YOURSITE.com/usmartphone/check/SessionID` , SessionID is saved SessionID after make login, if SessionID didn't save on you app, just use `http://www.YOURSITE.com/usmartphone/check` for check

, After check , result is JSON array by : 
* `check` : 0 or 1 , if user be login return 1
* `identity` : user identity ( username )
* `email` : user email
* `name` : display name
* `avatar` : avatar image url
* `uid` : user id
* `sessionid` : user session id, you need keep it on your app and use it on check method

On send request , you need set COOKIE on send request header by this strucher : `pisess=SessionID` , you can use code like this on your android app
```
request.setHeader("Cookie", "pisess=SessionID");
```

For example 
```
request.setHeader("Cookie", "pisess=91a474474432998b9073d11c6da0e86e");
```
And url is :
```
http://www.YOURSITE.com/usmartphone/check/91a474474432998b9073d11c6da0e86e
```

On this example , `91a474474432998b9073d11c6da0e86e` is user session id, than send from website result after login