Vulnerable PHPGurukul UMS Image
===============================

Demo image for CVE-2020-25952.

## Info

This repository contains the setup to create a docker image running PHPGurukul User Management System 2.1.
The image contains an `/exploit.py` to trigger the vulnerability.

For further info have a look at the corresponding [blog post](https://infosecwriteups.com/cve-2020-25952-f60fff8ffac).

## Run

You can just pull the image from docker hub:

```
docker run -it fab1ano/cve-2020-25952
```

## Setup

This section only contains the required procedure to create the docker image.
If you want to build it on your own, replace my handle (`fab1ano`) with yours.

```
docker build . -t fab1ano/cve-2020-25952
docker run -it fab1ano/cve-2020-25952
docker push fab1ano/cve-2020-25952
```
