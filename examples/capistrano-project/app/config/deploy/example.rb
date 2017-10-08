set :stage, :staging

server 'custom', user: 'root', roles: %w{web app}, port: 22

set :deploy_to, '/var/www/docker-arch'
