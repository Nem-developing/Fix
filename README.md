# Informations : 
- Fix est une application web permetant la gestion de tickets. Ce logiciel peut convenir pour une utilisation privée comme professionnelle. Choississez Fix pour une gestion simple rapide et efficace de vos incidents tequniques !   

## Image d'illustration :
![Image d'illustration](https://github.com/Nem-developing/Fix/blob/master/photos/Fix-illustration.JPG?raw=true)

## Dépendance : 
- API GOOGLE : https://developers.google.com/chart


## Déployer l'application sous Docker : 
### docker-compose.yml
```YAML
version: '3.1'

services:

  fix:
    image: nemdeveloping/fix:1.18
    restart: always
    ports:
      - 80:80
    environment:
      utilisateur: exampleuser
      mot_de_passe: examplepass
      base_de_donnees: exampledb
      hote_de_connexion: db

    volumes:
      - fix:/var/www/html

  db:
    image: mysql:5.7
    restart: always
    environment:
      MYSQL_DATABASE: exampledb
      MYSQL_USER: exampleuser
      MYSQL_PASSWORD: examplepass
      MYSQL_RANDOM_ROOT_PASSWORD: '1'
    volumes:
      - db:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin
    restart: always
    ports:
      - 8081:80
    environment:
      - PMA_ARBITRARY=1


volumes:
  fix:
  db:
```
### Commande
```bash
docker-compose up
```
