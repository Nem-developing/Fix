# API:1.0
#
# Created by : Néhémie Barkia
# Last Update : 26/05/2023
#
# PRÉREQUITS
FROM debian:latest
RUN apt-get update -y 
RUN apt-get install python -y
RUN apt-get clean
# INSTALLATION WEB 
RUN mkdir /fix-api/
COPY ./* /fix-api/
# Insertion des informations de connexion dans les fichiers de Fix
#RUN echo "<?php" >> /var/www/html/config/config.php
#RUN echo "\$hote_de_connexion = getenv('hote_de_connexion');" >> /var/www/html/config/config.php
#RUN echo "\$base_de_donnees = getenv('base_de_donnees');" >> /var/www/html/config/config.php
#RUN echo "\$utilisateur = getenv('utilisateur');" >> /var/www/html/config/config.php
#RUN echo "\$mot_de_passe = getenv('mot_de_passe');" >> /var/www/html/config/config.php
#RUN echo "\$hotedeconnexion = \"\$hote_de_connexion\";" >> /var/www/html/config/config.php
#RUN echo "\$basededonnee = \"\$base_de_donnees\";" >> /var/www/html/config/config.php
#RUN echo "\$utilisateur = \"\$utilisateur\";" >> /var/www/html/config/config.php
#RUN echo "\$motdepasse  = \"\$mot_de_passe\";" >> /var/www/html/config/config.php
#RUN echo "?>" >> /var/www/html/config/config.php
#
WORKDIR /fix-api/
EXPOSE 8080
CMD ["python3 XXXX"]
    