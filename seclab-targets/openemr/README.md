To build and run the container:
```
wget https://sourceforge.net/projects/openemr/files/OpenEMR%20Ubuntu_debian%20Package/5.0.1/openemr-php7_5.0.1-2_all.deb/download -O openemr-php7_5.0.1-2_all.deb
docker build . -t openemr
docker run -p 8080:80 openemr
```

The OpenEMR interface should be reachable at http://localhost:8080/openemr.

Find exploits in `exploits/` named by CVE.

## Credentials

service           | username | password | other
------------------|----------|----------|-------
mysql             | root     | root     |
`/openemr`        | admin    | pass     |
`/openemr/portal` | First1   | password | e@mail.com

Many opportunities for exploits that have been detailed here:
https://www.open-emr.org/wiki/images/1/11/Openemr_insecurity.pdf
