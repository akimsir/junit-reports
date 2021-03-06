FROM php:7.1.14-alpine

RUN apk update && apk add git

RUN git clone https://github.com/akimsir/junit-reports /project

WORKDIR /project

# для разработки
#COPY . /project

ENTRYPOINT ["sh", "-c"]
