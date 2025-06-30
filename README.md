# Informations :

- Fix est une application web permetant la gestion de tickets. Ce logiciel peut convenir pour une utilisation privée comme professionnelle. Choississez Fix pour une gestion simple rapide et efficace de vos incidents tequniques !

## Image d'illustration :

![Image d'illustration](https://github.com/Nem-developing/Fix/blob/master/photos/Fix-illustration.JPG?raw=true)

## Dépendance :

- API GOOGLE : https://developers.google.com/chart

## Déployer l'application sous Docker :

#### ⚠️ Supprime TOUS les conteneurs & images (en cas de besoin de mise au propre) ⚠️

⚠️ DANGER ⚠️
Sur linux :
```bash
docker stop $(docker ps -aq) && \
docker rm $(docker ps -aq) && \
docker volume rm $(docker volume ls -q) && \
docker rmi -f $(docker images -aq) && \
```

⚠️ DANGER ⚠️
Sur windows: 
```bash
docker stop $(docker ps -aq); docker rm $(docker ps -aq)
docker rmi $(docker images -aq); docker volume rm $(docker volume ls -q)
```

#### Initialisation des conteneur
```bash
docker compose up -d
```
