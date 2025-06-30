# FIX-FRONT:2.0
FROM node:22.16.0-bullseye

WORKDIR /usr/src/app

COPY ./FrontEnd/ ./

RUN npm i -y

EXPOSE 5173
CMD ["npm", "run","dev", "--host", "https://5173-cs-211488151727-default.cs-europe-west1-iuzs.cloudshell.dev/?authuser=0"]
