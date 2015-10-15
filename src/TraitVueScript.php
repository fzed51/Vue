<?php

/*
 * The MIT License
 *
 * Copyright 2015 fabien.sanchez.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Core\Vue;

if (!defined('WEBROOT')) {
	define('DS', DIRECTORY_SEPARATOR);
	define('WS', '/');
	define('ROOT', __DIR__ . DS);
	$dir = basename(ROOT);
	$tabUrl = explode($dir, $_SERVER['REQUEST_URI']);
	if (count($tabUrl) > 1) {
		define('WEBROOT', $tabUrl[0] . $dir . WS);
	} else {
		define('WEBROOT', WS);
	}
}

/**
 *
 * @author fabien.sanchez
 */
trait TraitVueScript {

	static $DossierScript = __DIR__ . '\..\..\script';
	private $Scripts = array();

	public function addScript($script) {
		$key = md5($script);
		$this->Scripts[$key] = '<script type="text/javascript" >' . $script . '</script>';
		return $this;
	}

	public function addFileScript($fileName) {
		$key = md5($fileName);
		$path_file = $fileName . '.js';
		$web_file = concatPath(WEBROOT . '/script', $path_file, WS);
		$sys_file = concatPath(static::$DossierScript, $path_file, DS);
		if (file_exists($sys_file)) {
			$this->Scripts[$key] = '<script type="text/javascript" src="' . $web_file . '"></script>';
		}
		return $this;
	}

	public function getScript() {
		return $this->Scripts;
	}

	public function renderScript() {
		return implode(PHP_EOL, $this->Scripts);
	}

}
