#########################################
# PHPLiveX - PHP & AJAX Library		#
# (C) Copyright 2006 Arda Beyazoğlu	#
# Version: 2.6				#
# Home Page: www.phplivex.com		#
# Contact: arda@beyazoglu.com		#
# Release Date: 05.02.2010		#
# License: LGPL				#
#########################################

PHPLiveX 2.6
--------------

    This release brings many new features, bug fixes, performance & security optimizations.  Many changes were done, according to the user feedbacks taken during about 1.5 years since previous version. The most important features of this release are Ajax File Upload feature and Ajax History Plugin. Here's the list of all new features:

- New Features

    * Ajax History Upload beta
    * Ajax File Upload

    * All JS operations are now handled by a single PLX object easierly
    * New parameters added: id, caching, history, timeout, onTimeout, onPreload
    * New methods added: LoadJS, LoadCSS, AjaxifyUpload
    * File uploads can now be used with ajax form submission
    * Added Stop method to stop functions repeatedly called in a defined time interval
    * Added AjaxifyClasses and AjaxifyClassMethods methods
    * Class variables can now be ajaxified

- Changes

    * Removed PHP Encoding variable, use it with constructor method
    * Added a second argument to event handlers. (thus, xmlhttp object and phplivex parameters may be reached inside these functions)
    * Changed the use of PHP Run method.
    * SESSION is not needed by PHP AjaxifyObjects and AjaxifyObjectMethods methods anymore
    * target and onUpdate parameters are now ignored when onFinish function returns false
   
- Bug Fixes

    * Fixed bugs about interval parameter and added Stop method to stop the repeated execution
    * Fixed bug when response text includes <style> tags in Internet Explorer
    * Functions which are not user defined can not be ajaxified anymore
    * Fixed bug when using multiple select boxes with form submission
    * Fixed bug when using multiple select boxes as target dom element


# AJAX File Upload (IMPORTANT)

To upload a file asynchronously, we need a cgi script. You have to move "upload.cgi" found in your download, 
to a directory where cgi scripts are allowed. For example; in most servers, there is a "cgi-bin" folder to do it. 
For detailed information, see "http://www.ricocheting.com/server/cgi.html" 

*** Give attention to the first line (path of perl) of upload.cgi. (#!C:\perl\bin\perl.exe), (#!/usr/bin/perl) gibi

If you work in your local, you need "PERL" installed. You can download and install it from http://www.activestate.com/activeperl 
Then you need to some configuration for apache web server:

*** Open apache configuration file "httpd.conf", 

1) See if cgi module is active or not (if not, remove # at the beginning of line). (LoadModule cgi_module modules/mod_cgi.so gibi)
	

2) You must add ExecCGI option to cgi directories

	For example; if you want to run cgi inside your document root directory,

	DocumentRoot "C:/Program Files/Apache Software Foundation/Apache2.2/htdocs"
	<Directory "C:/Program Files/Apache Software Foundation/Apache2.2/htdocs">
	    Options Indexes FollowSymLinks // Add ExecCGI to this line

	    AllowOverride None
	    Order allow,deny
	    Allow from all

	</Directory>

3) Remove # from the line #AddHandler cgi-script .cgi