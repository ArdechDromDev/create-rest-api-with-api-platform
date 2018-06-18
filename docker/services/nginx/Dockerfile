FROM nginx:alpine

RUN echo "UPDATE CONF 20180609 1559"
RUN addgroup -g 1000 winzana && \
    adduser -G winzana -g winzana -s /bin/sh -D winzana
#USER winzana
RUN rm /etc/nginx/conf.d/default.conf
COPY nginx.conf /etc/nginx
COPY default-symfony.conf /etc/nginx/conf.d/
