FROM ubuntu:22.04

ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=${USER_UID}

ENV LANG=C.UTF-8
ENV TZ=Asia/Tokyo

# user/group
RUN groupadd -g ${USER_GID} ${USERNAME} \
 && useradd -u ${USER_UID} -g ${USER_GID} -G adm,sudo -m -s /bin/bash ${USERNAME}

# packages
RUN export DEBIAN_FRONTEND=noninteractive \
 && sed -i 's/archive.ubuntu.com/ftp.jaist.ac.jp/' /etc/apt/sources.list \
 && apt-get update \
 && apt-get install -y --no-install-recommends \
        bash-completion \
        bind9-dnsutils \
        bzip2 \
        composer \
        curl \
        file \
        git \
        less \
        make \
        netbase \
        openssh-client \
        patch \
        php-apcu \
        php-cli \
        php-curl \
        php-sqlite3 \
        publicsuffix \
        sqlite3 \
        sudo \
        unzip \
        vim-nox \
        xz-utils \
        zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# sudo
RUN echo "${USERNAME} ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/user \
 && chmod 440 /etc/sudoers.d/user

USER ${USERNAME}
CMD [ "/bin/bash", "-l" ]
