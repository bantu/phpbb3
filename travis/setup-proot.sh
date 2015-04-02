#!/bin/bash
wget http://static.proot.me/proot-x86_64
chmod +x proot-x86_64

mkdir rootfs
pushd rootfs
curl -L http://cdimage.ubuntu.com/ubuntu-core/releases/14.04/release/ubuntu-core-14.04.2-core-amd64.tar.gz | tar xz 2> /dev/null
popd
