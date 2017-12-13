#!/bin/bash

if [ ! -d "src" ]; then
  echo "Please, run this script where the 'src' directory is present"
  exit 1
fi

# Extract version number from config.xml
configFile=./src/app/code/community/Bitbull/Soisy/etc/config.xml
versionNumber=$(cat $configFile | grep -Po '(?<=version>)\d+.\d+.\d+')
#echo $versionNumber

# Create archive
cp LICENSE src/app/code/community/Bitbull/Soisy/LICENSE && \
cp README.md src/app/code/community/Bitbull/Soisy/README.md && \
cd src/ && \
zip -q -r Bitbull_Soisy-$versionNumber.zip * && \
rm app/code/community/Bitbull/Soisy/LICENSE app/code/community/Bitbull/Soisy/README.md && \
mv Bitbull_Soisy-$versionNumber.zip ../ && \
cd ..