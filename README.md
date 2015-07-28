# Facebook-SimplePHP
Simple, yet useful implementation of Facebook's JavaScript and PHP SDKs for obtaining user data.

Example page used for obtaining Graph User data from Facebook and using it on a web page. Useful for personalizing the web experience.

Data that is obtained in this example:
* User name, birthday, age, sex, and email.
* Current profile picture from the user.
* IP for location purposes (using freegeoip API for longitude and latitude).

The application cosists of a client (any web page with the JavaScript SDK), and a PHP Server which obtains the data from Facebook and responds to the requests as a JSON Object.

Files and folders included:
* **Facebook-SDK:** includes all the files from the official Facebook PHP SDK.
* **Index.html:** test web page for displaying the user data.
* **Readme.md:** this file.
* **Server.php:** server for providing the information to the webpage, can be modified to request different data to Facebook.

**NOTE:** in order to use this example, you should register it as web application on the http://developers.facebook.com site and change the AppID and AppSecret accordingly.
