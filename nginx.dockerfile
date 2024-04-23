FROM nginx:stable-alpine

ADD nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80