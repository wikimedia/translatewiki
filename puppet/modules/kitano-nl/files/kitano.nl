# file managed by puppet

server {
	listen 80;
	listen [2a03:4000:6:55::1]:80;

	server_name .kitano.nl;
	root /www/kitano.nl;
	index index.html;
}
