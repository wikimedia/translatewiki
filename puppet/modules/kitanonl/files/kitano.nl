# file managed by puppet

server {
	listen 80;
	listen [2a03:4000:39:55d:5400:a2ff:fe21:b3ea]:80;
	server_name .kitano.nl;
	return 301 https://$host$request_uri;
}

server {
	listen 443;
	listen [2a03:4000:39:55d:5400:a2ff:fe21:b3ea]:443 ssl http2;

	server_name .kitano.nl;
	root /www/kitano.nl;
	index index.html;
}
