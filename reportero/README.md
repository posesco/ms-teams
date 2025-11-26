## How to use this image

**Reportero** has minimal requirements to run, it was built on an alpine distribution. You don't have to install or build anything. You just run it from the extracted/cloned directory. Still, if you don't want to bring the GitLab repo to your directory of choice, you can bring a container from dockerhub and run it:

This CGI is enabled for sending emails and notifications in applications that receive JSON payloads with Slack or Microsoft Teams.

```
git clone https://github.com/posesco/ms-teams.git .
cd reportero
docker build . -t reportero 
```

```
docker run --rm 
    -v $PWD/document.html:/data/document.html 
    -e ARTIFACT='document.html' 
    -e ENV='develop' 
    -e DOMAIN=https://myproject.com 
    -e PROJECT='My Project' 
    -e NAME_REPORT="Name Report"
    reportero
```

