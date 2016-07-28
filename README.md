#VCU Libraries Tech Stack
This is the primary skeleton for our library applications. We are using primarily a javascript client to give users a more desktop-like experience in the browser. 

*Prerequistes*
To install you'll need [Bower](http://www.bower.io), [Node >= 0.10.*](http://nodejs.org), and [Composer](http://www.getcomposer.org).

*Installation*
First you will have to clone the Github repo `git clone https://github.com/vculibraries/vculibraries-tech-stack.git` and then build out the dependencies, run the following command:

```
npm install
```

##More about the application skeleton

*/public/index.php*

This is where you will set up private routes, templates, and the session cookies. 

*/src/Techstack/Techstack.php*

This is the main configuration file. Here you will set up a base url for the application, enable the CAS server (if needed), the folder where your php templates will be served from. 

*/ember_templates*

This where any Ember JS templates will reside. This is setup so that you can have multiple Ember apps running (all on different pages of course), and controlled by one Gruntfile. 

*Gruntfile.js*

This is what controls the mechanism that compiles the  ember templates and minifies the javascript files. You have several commands that you can do: 

grunt compile -- packages up all of the Ember stuff into app.min.js

grunt -- watches all of the files for changes and recompiles everything to app.min.js

*/css*

By default, the project uses SASS to compile the the style.css file. In the "sass" folder are all of the neccessary sass modules. Not every project will have a sass component, so this may or may not be there. 

To compile SASS, you need to have Ruby installed, along with the Compass gem. When you want to compile a new css file, run the command "compass compile" while inside of the css folder.


##API routes

If you are creating an Ember application with Techstack, then all routes that have a regex match for 'api/' are going to be automatically run through an authentication filter. If the user has deauthenticated while running the javascript app -- the api, which should be via ajax -- will return a 401 error and the url to reauthenticate via CAS. 

##Layout variables

We are using [Twig](http://twig.sensiolabs.org/) as the templating enigne and have set up several default layout variables that you can override in routes. 

{{js_scripts}} & {{css_links}} -- these are set up in the index.php file and are the base for what will show up on all pages in the applications. 

{{route_js_scripts}} & {{route_css_links}} -- in the /php_templates/layout.php file these two are set to print after the js_scripts and css_links variables. This way you can override base css styles and load route-specific javascript files. 

{{title}} -- this is set up in the index.php file, but you can always override it. 

{{base_url}} -- this is the address to the root of your web app



