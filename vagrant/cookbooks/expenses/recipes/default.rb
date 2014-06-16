include_recipe "apt"
include_recipe "apache2"
include_recipe "apache2::mod_php5"
include_recipe "php"
include_recipe "php::module_mysql"
include_recipe "php::module_curl"
include_recipe "mysql::client"
include_recipe "mysql::server"
include_recipe "database::mysql"

package "libapache2-mod-suphp"
package "php5-xdebug"
package "php5-mysqlnd"

mysql_connection_info = {
  :host     => 'localhost',
  :username => 'root',
  :password => node['mysql']['server_root_password']
}

mysql_database 'expense_db' do
  connection mysql_connection_info
  action :create
end

web_app "#{node['project_name']}" do
  server_name "#{node['server_name']}"
  server_aliases ["#{node['server_name']}"]
  docroot "/home/vagrant/workspace/#{node['project_name']}/web"
  allow_override "All"
end

file "xdebug-config.ini" do
  path "/etc/php5/apache2/conf.d"
end

file "php-config.ini" do
  path "/etc/php5/apache2/conf.d"
end

# configure mysql service to start on boot
execute "update-rc.d mysql enable"

execute "initialize-schema" do
  command "mysql -u root -p#{node['mysql']['server_root_password']} expense_db < /home/vagrant/workspace/#{node['project_name']}/schema.sql"
end

