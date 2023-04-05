FROM php:7.1-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends runit \
    && apt-get install -y --no-install-recommends cron \
    && mkdir /etc/service/cron \
    && echo '#!/bin/sh' > /etc/service/cron/run \
    && echo 'exec /usr/sbin/cron -f' >> /etc/service/cron/run \
    && chmod -R 700 /etc/service/cron/ \
    && chmod 600 /etc/crontab \
    && rm -f /etc/cron.daily/standard \
    && rm -f /etc/cron.daily/upstart \
    && rm -f /etc/cron.daily/dpkg \
    && rm -f /etc/cron.daily/password \
    && rm -f /etc/cron.weekly/fstrim \
    && apt-get purge -y --auto-remove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

CMD ["runsv", "/etc/service/cron"]
