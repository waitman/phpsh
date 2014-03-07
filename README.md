```

phpsh

Copyright (c) 2014, Waitman Gobble <ns@waitman.net>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met: 

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer. 
2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution. 

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those
of the authors and should not be interpreted as representing official policies, 
either expressed or implied, of the FreeBSD Project.




a PHP shell ... (alpha stage)

there are plenty of PHP shells available (even php -a) however this is my implementation.

This software uses readline and memcached (for variable persistance).

configuration for php:

./configure --prefix=/usr \
	--with-readline \
	--with-pgsql=/usr \
	--with-openssl=/usr \
	--disable-cgi \
	--enable-pcntl

using pcntl to keep user from bailing with control-c. (use 'exit' like a real shell.)
postgres and openssl support is optional, so far not implemented.

NOTE: this passes commands through eval(), which is potentially a security risk.
If you create a php user as in the example below, then the user can do not much more damage
than they could do anyhow. The program first runs the submitted code through a 'mini jail' 
process to see if it will break the shell execution, because the eval() command will crash 
the shell if it receives code that generates a 'Fatal error'. 

Example:

# echo /path/to/phpsh >> /etc/shells
# groupadd php
# useradd -g php -c "PHP user" -d /home/php -s /path/to/phpsh
# passwd php
# su php
p[1] > system('pwd')
/home/projects/phpsh
p[2] > for ($i=0;$i<10;$i++) print $i; 
0123456789p[3] > 
p[4] > $a=array();
p[5] > $a[1]=4;
p[6] > $a[2]=5;
p[7] > $a[4]='foo';
p[8] > print_r($a);
Array
(
    [1] => 4
    [2] => 5
    [4] => foo
)
p[9] > help
exit()		Exit the phpsh shell.
reset_mc()	Erase all stored variables.
list_vars()	Print all stored variables (with values).
history()	Show history.
h(n)		Execute history line n.
p[10] > list_vars()
$i = 10
Array $a
Array
(
    [1] => 4
    [2] => 5
    [4] => foo
)
p[11] > history
[1] => system('pwd');
[2] => for ($i=0;$i<100;$i++) print $i;
[3] => $a=array();
[4] => $a[1]=4;
[5] => $a[2]=5;
[6] => $a[4]='foo';
[7] => print_r($a);
[8] => help();
[9] => list_vars();
[10] => history();
p[11] > h(7)
Executing: print_r($a);
Array
(
    [1] => 4
    [2] => 5
    [4] => foo
)
p[12] > exit
#

Note: restarting the shell will keep variables intact. (which could result in wierdness)

TODO: lots. 
1) save history to ~/.phpsh-history
2) bug fixes
3) lots more. 


```
