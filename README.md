# Preseem API

Couldn't find what I was looking for, so I copied some code from Amir Mehrabi and hacked it up enough to work without all the Laravel crap.  Not that Laravel is crap, I simply haven't taken the time to wrap my head around it, and I'm not using this to build a web app, so... yeah, needed it gone.

You can find his work at https://github.com/AmirMehrabi/Preseem.

Looks like he based his work off of some work done by Jim Lucas, which is functional, but a tad messy for what I need.  You cna find his repo at https://github.com/jimlucas/Preseem_API.

You can find the Preseem API documentation at https://apidocs.preseem.com/model/v1.html

I may or may not update this to test all the various things you can do with the API, but if you check out the examples in the two aforementioned repos, you should get the hang of it pretty quick.

If anything, I'll probably refactor some of this code if it gets too annoying.  The core functions are all pretty self explanatory:
 - list($noun,$start=1,$max=500)
 - get($noun,$id)
 - create($noun,$id)
 - delete($noun,$id)

The possible nouns are easy to figure out if you consider these helper functions which have rather verbose names.  Check out those examples I mentioned to see how to use these.
 - api_access_points_create
 - api_accounts_create
 - api_packages_create
 - api_services_create
 - api_sites_create

There are a few more things in the API that don't seem to be covered.  I don't use them at the moment, so I really don't care.
 - Routers
 - OLTs
 - AP Configs
 - CPE Radios

I'm willing to accept a PR, but you'll probably have to show me how.  I know just enough git to be dangerous.

# Usage

Copy config.php-dist to a convenient place as config.php and edit to add your API key.  You can also use it to put other configuration things and all that.  There's also a function that's to tell Amir's code where to write logs to.  Probably useful for debugging, so I left it in there, but pointed it to /dev/null until/unless it's needed.

Copy src/list-packages.php to your working directory and edit the require_once lines so it can find your autoload and config files.

Run it and it should dump a list of packages in your Preseem instance.

# License
It looks like Amir has the LGPL, so I have to use the LGPL. One of the least open open source licenses there is, but I digress... I'm sure you're not here to discuss the politics of FOSS licensing.

The license file is in here somewhere.  If there's anything else I need to do to stroke RMS's ego, let me know.