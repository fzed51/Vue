<?php

chdir(__DIR__);

require '..\vendor\autoload.php';

// test simple
echo (new fzed51\Vue\Vue('./template'))->render('test1');

// test de vue dans un dossier
echo (new fzed51\Vue\Vue('./template'))->render('test2.test');

// test avec data
echo (new fzed51\Vue\Vue('./template'))->render('test3.test', ['data' => 'data']);

// test avec layout
echo (new fzed51\Vue\Vue('./template'))->render('testX', ['id' => 4, 'layout' => 'test4.layout']);

// test avec layout + section inconnue
echo (new fzed51\Vue\Vue('./template'))->render('testX', ['id' => 5, 'layout' => 'test5.layout']);

// test avec layout + section
echo (new fzed51\Vue\Vue('./template'))->render('testY', ['id' => 6, 'layout' => 'test6.layout']);

// test avec multiple layout
echo (new fzed51\Vue\Vue('./template'))->render('testX', ['id' => 7, 'layout' => 'test7.test7']);
