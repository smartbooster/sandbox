FROM nginx:latest

# Handle user permissions
ARG HOST_USER
ARG HOST_GROUP

RUN usermod -u $HOST_USER www-data
RUN groupmod -g $HOST_GROUP www-data
