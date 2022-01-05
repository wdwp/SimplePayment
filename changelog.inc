<ul>
    <li>Version 1.0 December 2022</li>
	<ul>
	<li>Rewritten for cmsms 2.x</li>
	</ul>
	<li>Version 1.6 - 4 December 2012<br />
		<ul>
			<li>PayPal gateway shows in between page (via template to style) when visitor is transfered to PayPal;</li>
			<li>Version prepared for use as of CMSMS vs 1.11.x.</li>
		</ul>
	</li>
	<li>Version 1.5 - 12 October 2011<br />
		<ul>
			<li>Minor changes to make sure Postgress can be used as well (thanks go to Brett of Binarystudios.net);</li>
			<li>Renaming of files in back end;</li>
			<li>Including security checks in back end on templates and options;</li>
			<li>Version prepared for use as of CMSMS vs 1.9.4.x and 1.10.</li>
		</ul>
	</li>
	<li>Version 1.4 12 June 2011<br>
		<ul>
			<li>Changed the maximum version of CMSMS to make sure no problems with version 1.10 of CMSMS.</li>
		</ul>
	</li>
	<li>Version 1.3 21 November 2010<br>
		<ul>
			<li>Included new table to hold payment information retrieved from banks.</li>
		</ul>
	</li>
	<li>Version 1.2 12 November 2010<br />
		<ul>
			<li>Typo in PayPal gateway caused correct working of payment flow (<a href="http://dev.cmsmadesimple.org/bug/view/5470" target="_blank">bug#: 5470</a>);</li>
			<li>Typo in uninstall message;</li>
			<li>Change in 'refresh' process, now inserts parameters of gateways as well;</li>
			<li>Parameter currency was not correctly passed to PayPal;</li>
			<li>Get rid of public member variables such as 'global $gCms';</li>
			<li>Minimum version set to 1.8.1 due to removal public member variables.</li>
		</ul>
	</li>
	<li>Version 1.1 12 September 2010<br>
		<ul>
			<li>Language of PayPal screens for customer can now be set with new parameter: language_code;</li>
			<li>Allow module to run in 1.9 environment of CMSMS.</li>
		</ul>
	</li>
	<li>Version 1.0.5 Oct 2009<br>
		<ul>
			<li>No 'software' dependencies on Cart and Shop Made Simple anymore;</li>
			<li>Make sure that if Module Simple Cart is installed, the version should be at least 0.2.5 to work correctly with PayPal.</li>
			<li>Item and its amount for PayPal gateway moved to Simple Cart to open gateway for other modules;</li>
			<li>New PayPal attributes:
				<ul>
					<li>cartstyle: to handle one total amount or multiple detail lines in payment request;</li>
					<li>cancel_return: the page to return to when payment is canceled. During install/upgrade set to home page;</li>
					<li>return: the page to return to, when payment is done. During install/upgrade set to home page;</li>
					<li>debug: will show all prepared PayPal fields when value is yes or die.<br>
					If 'yes' processing continues. If 'die', all fields are shown and processing payment stops. Not set during install/upgrade.</li>
				</ul>
			</li>
			<li>Set maximum CMSMS version to 1.6.9 to avoid unforseen problems.</li>
		</ul>
	</li>
	<li>Version 1.0.4 17 Jun 2009<br>
		<ul>
			<li>Error solved on not being able to pay via PayPal (<a href="http://dev.cmsmadesimple.org/bug/view/3076" target="_blank">bug#: 3076</a>);</li>
			<li>Added new preference for refresh function (<a href="http://dev.cmsmadesimple.org/bug/view/3065" target="_blank">bug#: 3065</a>).</li>
		</ul>
	</li>
	<li>Version 1.0.3 02 Mar 2009<br>
		<ul>
			<li>Better handling of path and program in refresh of gateways (thanks to: comicbrute).</li>
		</ul>
	</li>
	<li>Version 1.0.2 14 Feb 2009<br>
		<ul>
			<li>Not all Payment Made Simple objects were correctly removed during uninstall;</li>
			<li>PayPal gateway has been altered for the submit payment part. It now receives orderheader info from CartMS;</li>
			<li>Refresh gateways functionallity has been improved.</li>
		</ul>
	</li>
	<li>Version 1.0.1 1 Feb 2009<br>
		<ul>
			<li>Templates available for developers to retrieve user data that can be prompted.</li>
		</ul>
	</li>
	<li>Version 1.0.0 11 Dec 2008<br />
		<ul>
			<li>Initial Release.</li>
		</ul>
	</li>
</ul>