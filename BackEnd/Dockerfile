# FIX-API:3.1
#
# PRÉREQUITS
FROM python:3.9

WORKDIR /usr/src/app

COPY ./BackEnd/ ./

RUN pip3 install -r /usr/src/app/requirements.txt


EXPOSE 8080
CMD ["python3" ,"main.py"]
