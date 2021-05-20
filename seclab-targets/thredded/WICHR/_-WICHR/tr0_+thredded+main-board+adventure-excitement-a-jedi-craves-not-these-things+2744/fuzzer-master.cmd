/afl/afl-fuzz -i /p/Witcher/seclab-targets/thredded/WICHR/work/initial_seeds -o /p/Witcher/seclab-targets/thredded/WICHR/work -m 8G -M fuzzer-master -x /p/Witcher/seclab-targets/thredded/WICHR/work/dict.txt -t 5000+ -- /httpreqr --url http://127.0.0.1:9292/thredded/main-board/adventure-excitement-a-jedi-craves-not-these-things/2744

AFL_BASE=/p/Witcher/seclab-targets/thredded/WICHR/work/fuzzer-master
AFL_HTTP_DICT=1
AFL_META_INFO_ID=9292
AFL_PATH=/afl
AFL_PRELOAD=/wclibs/libcgiwrapper.so
APP_HOME=/thredded
BUNDLE_GEMFILE=/thredded/Gemfile
BUNDLE_PATH=/bundle
BUNDLE_SILENCE_ROOT_WARNING=1
CONTAINER_NAME=witcher
DB=mysql2
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DEBIAN_FRONTEND=noninteractive
DOCKER=1
DOCUMENT_ROOT=/app
HOME=/root
HOSTNAME=3b4fb9f9bc30
LAST_IP=2
2
LD_LIBRARY_PATH=/wclibs
LESSCLOSE=/usr/bin/lesspipe %s %s
LESSOPEN=| /usr/bin/lesspipe %s
LOGIN_COOKIE=_dummy_session=d8OTthhXVRGmW2sjCVQmEDiOe9kPag3Emp1K5Fd5tAE5UD6UdWOcdVhsJe18RNXW%2BGvxmeEvnjebwb11lUJnAABaO9YsuVwVjDIuQZnX7WY2PWV3r71mYLY6kRre%2FFOW9hZtp9jUzuFzPPmEWswnf77JY%2B5hubEGXUKuXGSUJk2oHmthkzWuzVk9PAtFc%2FJLFaQFMog0dKYZ5sCXEtdsqHcnONSiFZIF9cswv4jnOfWAWVrIJxlsMBUEvF0ebR0NTNCu1RJCKYoX%2FJ2UUQFZruYDKwhShOFxZVpcx2HR%2BFZWmS9FHOgsAC76t%2FOiPbHsvqCQsGWpqc93Elv%2FbDkpTr%2FO2sLWIDAJAGKjUXkeVyy5p732Z5oY7PqZNIAM0f%2FbZSifpidMcX1u6O8H3dlBr32nPwczTPmkEV%2FEhra%2BIbksDI1tysZ6w2SS77anPXNDb%2FmYfVY3TwQik1ZQk9H%2Fy%2FX824SyZNEtSpJ98z551COm8LOX4v7tIIlbMPhKnVN0on4%3D--E0YefB9Z2s1jS2TB--vwriGJeDwkBIsCauJSDSTw%3D%3D; path=/; HttpOnly
LS_COLORS=rs=0:di=01;34:ln=01;36:mh=00:pi=40;33:so=01;35:do=01;35:bd=40;33;01:cd=40;33;01:or=40;31;01:mi=00:su=37;41:sg=30;43:ca=30;41:tw=30;42:ow=34;42:st=37;44:ex=01;32:*.tar=01;31:*.tgz=01;31:*.arc=01;31:*.arj=01;31:*.taz=01;31:*.lha=01;31:*.lz4=01;31:*.lzh=01;31:*.lzma=01;31:*.tlz=01;31:*.txz=01;31:*.tzo=01;31:*.t7z=01;31:*.zip=01;31:*.z=01;31:*.Z=01;31:*.dz=01;31:*.gz=01;31:*.lrz=01;31:*.lz=01;31:*.lzo=01;31:*.xz=01;31:*.zst=01;31:*.tzst=01;31:*.bz2=01;31:*.bz=01;31:*.tbz=01;31:*.tbz2=01;31:*.tz=01;31:*.deb=01;31:*.rpm=01;31:*.jar=01;31:*.war=01;31:*.ear=01;31:*.sar=01;31:*.rar=01;31:*.alz=01;31:*.ace=01;31:*.zoo=01;31:*.cpio=01;31:*.7z=01;31:*.rz=01;31:*.cab=01;31:*.wim=01;31:*.swm=01;31:*.dwm=01;31:*.esd=01;31:*.jpg=01;35:*.jpeg=01;35:*.mjpg=01;35:*.mjpeg=01;35:*.gif=01;35:*.bmp=01;35:*.pbm=01;35:*.pgm=01;35:*.ppm=01;35:*.tga=01;35:*.xbm=01;35:*.xpm=01;35:*.tif=01;35:*.tiff=01;35:*.png=01;35:*.svg=01;35:*.svgz=01;35:*.mng=01;35:*.pcx=01;35:*.mov=01;35:*.mpg=01;35:*.mpeg=01;35:*.m2v=01;35:*.mkv=01;35:*.webm=01;35:*.ogm=01;35:*.mp4=01;35:*.m4v=01;35:*.mp4v=01;35:*.vob=01;35:*.qt=01;35:*.nuv=01;35:*.wmv=01;35:*.asf=01;35:*.rm=01;35:*.rmvb=01;35:*.flc=01;35:*.avi=01;35:*.fli=01;35:*.flv=01;35:*.gl=01;35:*.dl=01;35:*.xcf=01;35:*.xwd=01;35:*.yuv=01;35:*.cgm=01;35:*.emf=01;35:*.ogv=01;35:*.ogx=01;35:*.aac=00;36:*.au=00;36:*.flac=00;36:*.m4a=00;36:*.mid=00;36:*.midi=00;36:*.mka=00;36:*.mp3=00;36:*.mpc=00;36:*.ogg=00;36:*.ra=00;36:*.wav=00;36:*.oga=00;36:*.opus=00;36:*.spx=00;36:*.xspf=00;36:
MANDATORY_COOKIE=_dummy_session=bJ6Mghd5JoY%2B6lB1lEY3fkewZGYuabYvPjLixg4vhSYoOPr6aVu5SQcLv0kVaXNYQq2IgR1oWmVuZTNVbOK1EiTZeU8tuIJs3XACD4nTzVpft2zAh4NiVO0Fudkp6YHJeglSlo7lUDdBnI5AaTo6jTfUMr%2BGDV2XRMihgFvOaAasHu2E4pMBgp8tPpKQ5zaaPcG%2F80OJkxtHCCh30xodwq6AuItk53iiK6Q76jQXD7fjBBO%2FC%2BcuPx%2BpYbTRC3PwZBPspPhKE6DxLIHRiXq1Zz%2FkrUg1IYiLy7W7rdweOl6xgCXdeALU9%2FELP6Uy7N9qu2e%2FantbcxKGHhIVWZ8vd2mUdmMJlFIkYr4awGWFRepkD5Iuxjx4gGf5DuEGQNpS9R%2FQ5w9jjEn45WMUm3zoQyLP--VsYPHS%2FsPmDmWQKC--TpnmzMK9tFm4iX8I%2FQDZMw%3D%3D; __profilin=p%3Dt; 
METHOD=POST
NO_WC_EXTRA=1
NVM_DIR=/home/wc/.nvm
OLDPWD=/thredded
PATH=/home/wc/.virtualenvs/witcher/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
PORT=9292
PS1=\[[38;5;59m\]\u@\h-2
2 [witcher] \[[38;5;79m\]\w \[\e[0m\]# 
PWD=/p/Witcher/seclab-targets/thredded
SCRIPT_FILENAME=http://127.0.0.1:9292/thredded/main-board/adventure-excitement-a-jedi-craves-not-these-things/2744
SCRIPT_NAME=http://127.0.0.1:9292/thredded/main-board/adventure-excitement-a-jedi-craves-not-these-things/2744
SERVER_NAME=witcher
SHLVL=1
STRICT=3
TERM=xterm-256color
TZ=America/Phoenix
VIRTUALENVWRAPPER_HOOK_DIR=/root/.virtualenvs
VIRTUALENVWRAPPER_PROJECT_FILENAME=.project
VIRTUALENVWRAPPER_SCRIPT=/usr/share/virtualenvwrapper/virtualenvwrapper.sh
VIRTUAL_ENV=/home/wc/.virtualenvs/witcher
WC_CORES=10
WC_FIRST=
WC_INSTRUMENTATION=1
WC_SET_AFFINITY=0
WC_SINGLE_SCRIPT=
WC_TEST_VER=EXWICHR
WC_TIMEOUT=600
WEBPACKER_DEV_SERVER_HOST=0.0.0.0
WORKON_HOME=/root/.virtualenvs
_=/home/wc/.virtualenvs/witcher/bin/python
_VIRTUALENVWRAPPER_API= mkvirtualenv rmvirtualenv lsvirtualenv showvirtualenv workon add2virtualenv cdsitepackages cdvirtualenv lssitepackages toggleglobalsitepackages cpvirtualenv setvirtualenvproject mkproject cdproject mktmpenv
method_map=POST,POST,POST,POST,POST,POST,POST,POST,POST,POST,POST,POST,POST,POST,POST,POST