# Facebook-SimplePHP
Simple, yet useful implementation of Facebook's JavaScript and PHP SDKs for obtaining user data.

Example page used for obtaining Graph User data from Facebook and using it on a web page. Useful for personalizing the web experience.

Data that is obtained in this example:
* User name, birthday, age, sex, email.
* Current profile picture from the user.
* IP for location purposes (using freegeoip API).

The application cosists of a client (any web page with the JavaScript SDK), and a PHP Server which obtains the data from Facebook and responds to the requests as a JSON Object.

**NOTE:** in order to use this example, you should register it as web application on the http://developers.facebook.com site and change the AppID and AppSecret accordingly.
