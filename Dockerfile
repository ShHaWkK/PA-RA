# Direction ; PA-RA/Dockerfile
FROM mysql:latest

ENV MYSQL_ROOT_PASSWORD=rootpassword
ENV MYSQL_DATABASE=no_more_waste
ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=password

EXPOSE 3306
