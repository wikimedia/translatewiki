# file managed by puppet

server {
	listen 80;
	listen [::]:80;
	server_name .kitano.nl;
	return 301 https://$host$request_uri;
}

server {
	listen 443;
	listen [::]:443 ssl;
	http2 on;

	server_name .kitano.nl;
	root /www/kitano.nl;
	index index.html;
}
