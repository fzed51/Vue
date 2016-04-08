<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VueTest
 *
 * @author fabien.sanchez
 */
class VueTest extends PHPUnit_Framework_TestCase
{

    private function escapeChar($subject)
    {
        return preg_replace("/\r\n|\r|\n/", PHP_EOL, $subject);
    }

    private function equals($expected, $actual, $message = '')
    {
        $this->assertEquals($this->escapeChar($expected), $this->escapeChar($actual), $message);
    }

    private function result($file)
    {
        $fullname = './test/result/' . $file . '.txt';
        if (file_exists($fullname)) {
            $result = file_get_contents('./test/result/' . $file . '.txt');
            return $result;
        } else {
            echo (PHP_EOL . "le fichier de résultat '$fullname' n'existe pas!" . PHP_EOL);
        }
    }

    public function testRenderSimple()
    {
        $vue = new fzed51\Vue\Vue('./test/template');
        $this->equals($this->result('test_1'), ($vue->render('test1')));
    }

    public function testRenderFolder()
    {

        $vue = new fzed51\Vue\Vue('./test/template');
        $this->equals($this->result('test_2'), ($vue->render('test2.test')));
    }

    public function testRenderData()
    {
        $vue = new fzed51\Vue\Vue('./test/template');
        $this->equals($this->result('test_3'), ($vue->render('test3.test', ['data' => 'data'])));
    }

    public function testRenderLayout()
    {
        $vue = new fzed51\Vue\Vue('./test/template');
        $this->equals($this->result('test_4'), ($vue->render('testX', ['id' => 4, 'layout' => 'test4.layout'])));
    }

    public function testRenderLayoutSectionUnknow()
    {
        $vue = new fzed51\Vue\Vue('./test/template');
        $this->equals($this->result('test_5'), ($vue->render('testX', ['id' => 5, 'layout' => 'test5.layout'])));
    }

    public function testRenderLayoutSection()
    {
        $vue = new fzed51\Vue\Vue('./test/template');
        $this->equals($this->result('test_6'), ($vue->render('testY', ['id' => 6, 'layout' => 'test6.layout'])));
    }

    public function testRenderMultiLayout()
    {
        $vue = new fzed51\Vue\Vue('./test/template');
        $this->equals($this->result('test_7'), ($vue->render('testX', ['id' => 7, 'layout' => 'test7.test7'])));
    }

    /*

      // test avec layout + section
      echo (new fzed51\Vue\Vue('./template'))->render('testY', ['id' => 6, 'layout' => 'test6.layout']);

      // test avec multiple layout
      echo (new fzed51\Vue\Vue('./template'))->render('testX', ['id' => 7, 'layout' => 'test7.test7']);
     */
}
