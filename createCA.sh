#!/bin/bash

CA_DIR=ca

mkdir -p $CA_DIR

#Step 1: Generate CA private key
if [ ! -f $PWD/$CA_DIR/ca-private-key.pem ]; then
    openssl genrsa -out $PWD/$CA_DIR/ca-private-key.pem 2048
    echo 'Step 1: CA private key created'
else
    echo 'Step 1: CA private key already exists'
fi

#Step 2: Generate CA certificate using the private key
if [ ! -f $PWD/$CA_DIR/ca-cert.pem ]; then
    openssl req -sha1 -new -x509 -nodes -days 3650 -key $PWD/$CA_DIR/ca-private-key.pem \
    -out $PWD/$CA_DIR/ca-cert.pem -subj "/C=GB/ST=Greater London/L=London/O=Dell EMC/OU=AWG/CN=abd.dell.com"
    echo 'Step 2: CA certificate created'
else
    echo 'Step 2: CA certificate already exists'
fi
