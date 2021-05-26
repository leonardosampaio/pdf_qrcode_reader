## PDF qrcode extractor
 

**Leonardo Sampaio - Upwork - 2021-05-26**

**leonardors@gmail.com**

Usage:

    php reader.php /path/to/input.pdf /path/to/output.txt #(output file optional)

Dependencies:

Linux (php, convert, java and zbar-img should be in $PATH):

    sudo apt install php7.4 #(7.4 or newer)
    sudo apt install zbar-tools
    sudo apt install imagemagick #(after installation do sudo rm /etc/ImageMagick-6/policy.xml)
    sudo apt install openjdk-8-jdk #(8 or newer)
Windows (php, imagemagick, java and zbar-img should be in $Env:Path):

    https://chocolatey.org/install    
    choco install php --version=7.4.13 #(7.4 or newer)    
    choco install imagemagick    
    choco install zbar    
    choco install openjdk8 #(8 or newer) 

or manually install from

https://windows.php.net/download/

https://sourceforge.net/projects/zbar/

https://imagemagick.org/script/download.php

https://openjdk.java.net/