# file managed by puppet

server {
	listen 80;
	listen [::]:80;
	server_name .kitano.nl;
	return 301 https://$host$request_uri;
}

server {
	listen 443 ssl;
	listen [::]:443 ssl;
	http2 on;

	include includes/ssl-certbot.conf;

	server_name .kitano.nl;
	root /www/kitano.nl;
	index index.html;
}
