# Preseem API

Couldn't find what I was looking for, so I copied some code from Amir Mehrabi and hacked it up enough to work without all the Laravel crap.  Not that Laravel is crap, I simply haven't taken the time to wrap my head around it, and I'm not using this to build a web app, so... yeah, needed it gone.

You can find his work at https://github.com/AmirMehrabi/Preseem.

Looks like he based his work off of some work done by Jim Lucas, which is functional, but a tad messy for what I need.  You can find his repo at https://github.com/jimlucas/Preseem_API.

You can find the Preseem API documentation at https://apidocs.preseem.com/model/v1.html

I may or may not update this to test all the various things you can do with the API, but if you check out the examples in the two aforementioned repos, you should get the hang of it pretty quick.

If anything, I'll probably refactor some of this code if it gets too annoying.  The core functions are all pretty self explanatory:
 - function list($object, $page = 1, $limit = 500)
 - public function create($object, $params)
 - public function delete($object, $id)
 - public function get($object, $id)

I don't know why three of them are explicitly public while the 4th is implicitly public, but that's the code as I received it.  I may clean it up, I may not.

Note that $object is not an object, but a string, which is weird... I may clean that up too.

There are 5 helper functions that seem to validate $params, then pass them to the create() function without adulteration.  Basically just create() with extra steps and a really weird naming scheme.
 - api_access_points_create
 - api_accounts_create
 - api_packages_create
 - api_services_create
 - api_sites_create

There are a few more things in the API that don't seem to be covered by this class, but I don't use them at the moment, so I really don't care.  Ask and ye may receive.  Best bet would be to send a PR if you need these, but maybe not, as I wouldn't know what to do with a PR if I got one.

 - Routers
 - OLTs
 - AP Configs
 - CPE Radios

# Usage & testing

Copy config.php-dist to a convenient place as config.php and edit to add your API key.  You can also use it to put other configuration things and all that.  There's also a function that's to tell Amir's code where to write logs to.  Probably useful for debugging, so I left it in there, but pointed it to `/dev/null` until/unless it's needed.

Copy `src/list-packages.php` to your working directory and edit the require_once lines so it can find your autoload and config files.

Run it and it should dump a list of packages in your Preseem instance.  It should look something like this, showing you the first 500 packages.  If you need to look at package 501-1000, you'll need to grab page 2, but I would suggest that you look at the confusing mess you have going on in your billing system first, as no self respecting ISP should have that many different packages.


```
troy@myfantasticmachine:~/src/preseem-api$ php src/list-packages.php
stdClass Object
(
    [data] => Array
        (
            [0] => stdClass Object
                (
                    [id] => TestPackage50x10
                    [name] => Test Package 50x10
                    [up_speed] => 10000
                    [down_speed] => 50000
                )

        )

    [paginator] => stdClass Object
        (
            [page] => 1
            [page_count] => 1
            [limit] => 500
            [total_count] => 1
        )

)
troy@myfantasticmachine:~/src/preseem-api$
```


# Installation

I'm probably not going to publish this anywhere but github, so you'll need to learn how to tell composer to pull source from it.  I tried reading the docs, and it seems there are about 38 ways to do this with less json, but none of them seem to work, so I found a working example and went from there.  If you can do this better, please let me know, otherwise, copy this and paste it into `composer.json`.

```
{
    "name": "myself/myproject",
    "description": "My awesome project to manage Preseem",
    "license": "private",
    "repositories": {
      "preseem-api": {
        "type": "package",
        "package": {
          "name": "troy/preseem-api",
          "version": "1.0",
          "source": {
            "url": "https://github.com/tsettle/preseem-api.git",
            "type": "git",
            "reference": "origin/main"
          }
        }
      }
    },
    "require": {
      "troy/preseem-api":"1.0"
    }
}
```

Oh, if you're reading this, you probably don't need me to tell you that once you've put all that into composer.json, you need to run `composer install` or something.

# License
It looks like Amir has the LGPL, so I have to use the LGPL. One of the least open open source licenses there is, but I digress... I'm sure you're not here to discuss the politics of FOSS licensing.

The license file is in here somewhere.  If there's anything else I need to do to stroke RMS's ego, let me know.
