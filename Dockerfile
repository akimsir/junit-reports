FROM php:7.1.14-alpine

RUN apk update && apk add git

WORKDIR /project

RUN git clone https://github.com/akimsir/junit-reports

# для разработки
#COPY . /project

ENTRYPOINT ["sh", "-c"]
