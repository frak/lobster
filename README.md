# SportLobster Test #
## Deploy ##
**This entire section is dependant on having the correct SSH permissions and 
having [Capifony](http://capifony.org/) installed**

Modify app/config/deploy.rb to reflect the staging environment you wish to deploy to (or production)

Setup your vhost on the remote server. The only consideration you need to make for Capifony
is that the current site will be served at {your_root}/current/web. This is symlinked
automatically with each deploy.

Then run `capifony staging deploy::setup` to create the remote directory structure. Once this
is complete you will need to configure your parameters.yml as per your site configuration.

To deploy you will need to run `capifony staging deploy` - the first run of this will take 
some time and may require your github credentials. You will need to ensure that the database 
named in parameters.yml exists and your user credentials are valid as the Doctrine migrations
will be run.

If you deploy the `basic-requirements` then you will be able to see results immediately as it
runs from a local feed.xml.  If you deploy master, however, you will need to run 
`app/console lobster:sky:scraper` with a valid feed.url defined in parameters.yml

## Testing ##
`bin/phpunit -c app/ --testdox` 

Owing to the scope of the requirements I did not separate functional and unit tests, and it really
shows in the test execution times, but this could easily be refactored to be separate.  Test coverage
artifacts are in the build directory.

## Versions ##
The full version is at head, however, I tagged `basic-requirements` which is my strict
implementation of your requirements.  To deploy this, run `capifony staging deploy -S branch=basic-requirements`