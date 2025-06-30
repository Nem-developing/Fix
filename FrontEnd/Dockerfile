# FIX-FRONT:2.0
FROM node:22.16.0-bullseye

WORKDIR /usr/src/app

COPY ./FrontEnd/ ./

RUN npm i -y 

EXPOSE 5173
CMD ["npm", "run","dev"]
