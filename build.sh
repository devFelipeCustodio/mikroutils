docker build . -t mikroutils && \
    docker run --name mikroutils -d -p 3434:80 mikroutils