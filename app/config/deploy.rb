set :application,    "sportlobster"
set :domain,         "#{application}.com"
set :deploy_to,      "/sites/#{domain}"
set :app_path,       "app"
set :repository,     "git@github.com:frak/lobster.git"
set :scm,            :git
set :model_manager,  "doctrine"
set :keep_releases,  5
set :ssh_options,    { :forward_agent => true } # Github needs to know who you are
set :use_composer,   true
set :update_vendors, false
set :vendors_mode,   "install"
set :use_sudo,       true # you may not need this

# If you hit the Github API limit, this lets you login (Composer can do that quite easily on first deploy)
default_run_options[:pty] = true

# logger.level = Logger::MAX_LEVEL
logger.level = Logger::DEBUG # The ticks alone are not informative enough ;o)

# Database migration
before "symfony:cache:warmup", "symfony:doctrine:migrations:migrate"

# Cleanup after you're done
after "deploy", "deploy:cleanup"

task :production do
  role :web,        domain # You can add as many servers as you like to each role
  role :app,        domain, :primary => true # This is also the server that Doctrine migrations are run

  set :clear_controllers, true # removes app_*.php
  set :symfony_debug,     false
  set :symfony_env_prod,  "prod"

  # This restricts production deploys to tags only
  set :branch do
    tags = `git for-each-ref refs/tags --sort=authordate --format='%(refname:short)'`.split("\n")

    ui = Capistrano::CLI.ui
    tag = ui.choose do |menu|
      menu.index = :number
      menu.index_suffix = ") "
      menu.header = "Tags"
      menu.hidden("")

      menu.prompt = "Tag to deploy:"
      tags.each {|t| menu.choice(t)}
    end
  end
end

task :staging do
  role :web,              "serv0r"
  role :app,              "serv0r", :primary => true
  set :clear_controllers, false
  set :symfony_debug,     true
  set :symfony_env_prod,  "dev"

  # This allows you to deploy arbitrary tags/branches/commits (cap staging deploy -S branch=your-value)
  set :branch, 'master' unless exists?(:branch)
end
