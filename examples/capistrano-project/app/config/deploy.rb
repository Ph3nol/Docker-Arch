lock "3.9.1"

set :application, "docker-arch-app"
set :repo_url, 'https://github.com/Ph3nol/Docker-Arch.git'
set :keep_releases, 1

set :ssh_options, { :forward_agent => true }
