<?php

arch('the codebase does not reference env variables outside of config files.')
    ->expect('env')
    ->not->toBeUsed();

arch('the codebase does not contain any debugging code.')
    ->expect([
        'dd',
        'dump',
        'var_dump',
        'die',
        'sleep',
        'usleep',
        'exit',
    ])
    ->not->toBeUsed();

arch()->preset()->php();
arch()->preset()->security()->ignoring('md5');
