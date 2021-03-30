Vulnerable PHPGurukul OMRS Image
================================

Demo image for CVE-2020-35151.

## Info

This repository contains the setup to create a docker image running PHPGurukul OMRS 1.0.
The image contains an `/exploit.py` to trigger the vulnerability.

For further info have a look at the corresponding [entry on exploit-db](https://www.exploit-db.com/exploits/49307).

## Run

You can just pull the image from docker hub:

```
docker run -it fab1ano/cve-2020-35151
```

## Setup

This section only contains the required procedure to create the docker image.
If you want to build it on your own, replace my handle (`fab1ano`) with yours.

```
docker build . -t fab1ano/cve-2020-35151
docker run -it fab1ano/cve-2020-35151
docker push fab1ano/cve-2020-35151
```
