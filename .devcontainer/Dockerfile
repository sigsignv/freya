FROM debian:bookworm

ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=${USER_UID}

ENV LANG=C.UTF-8
ENV TZ=Asia/Tokyo

# Add a user
RUN groupadd -g ${USER_GID} ${USERNAME} \
 && useradd -u ${USER_UID} -g ${USER_GID} -G adm,sudo -m -s /bin/bash ${USERNAME}

# packages
RUN export DEBIAN_FRONTEND=noninteractive \
 && apt-get update \
 && apt-get upgrade -y \
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
        php-xml \
        publicsuffix \
        sqlite3 \
        sudo \
        unzip \
        vim-nox \
        xz-utils \
        whiptail \
        zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Enable sudo
RUN echo "${USERNAME} ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/user \
 && chmod 440 /etc/sudoers.d/user

USER ${USERNAME}
CMD [ "/bin/bash", "-l" ]
