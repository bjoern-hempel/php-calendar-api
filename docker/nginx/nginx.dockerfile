# Use latest nginx image
FROM nginx:latest

RUN openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout /etc/ssl/private/nginx-selfsigned.key -out /etc/ssl/certs/nginx-selfsigned.crt -subj "/C=DE/ST=Saxony/L=Dresden/O=Ixnode/OU=IT/CN=localhost"

RUN openssl dhparam -out /etc/nginx/dhparam.pem 4096

RUN mkdir /etc/nginx/snippets

COPY snippets/self-signed.conf /etc/nginx/snippets/self-signed.conf
COPY snippets/ssl-params.conf /etc/nginx/snippets/ssl-params.conf
