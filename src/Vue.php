<?php

/*
 * Cette Classe est une réécriture "mineure" de slimphp/PHP-View
 *
 * @link      https://github.com/slimphp/PHP-View
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/PHP-View/blob/master/LICENSE.md (MIT License)
 */

/*
 * The MIT License
 *
 * Copyright 2016 fabien.sanchez.
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

namespace fzed51\Vue;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class Vue
 * @package fzed51\Vue
 */
class Vue
{

    /**
     * @var array
     */
    protected $attributes = [
        'extension' => '.phtml'
    ];

    /**
     * @var string
     */
    protected $templatePath;

    /**
     * @var array
     */
    private $sectionContent = [];

    /**
     * @var string
     */
    private $layout = '';

    /**
     * @var string
     */
    private $courentSection = '';

    /**
     * @var int
     */
    private $levelStartObcache = 0;

    /**
     * @param string $templatePath
     * @param array $attributes
     */
    public function __construct($templatePath = "", $attributes = [])
    {
        $this->templatePath = $templatePath;
        $this->attributes = $attributes;
    }

    public function render($template, array $data = [])
    {
        return templateToString($template, $data);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (!isset($this->attributes[$key])) {
            return false;
        }
        return $this->attributes[$key];
    }

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * Retourne le rendu du template sous forme de chaine
     *
     * Il ne peut pas y avoir de clé template pour les data
     *
     * @param $template
     * @param array $data
     * @return string
     * @throws InvalidArgumentException
     */
    protected function templateToString($template, array $data)
    {

        $templateFullName = $this->getTemplateFullName($template);

        if (!is_file($templateFullName)) {
            throw new RuntimeException("View cannot render `$template` because the template does not exist");
        }

        $fullData = array_merge($this->attributes, $data);
        $this->levelStartObcache = ob_get_level();
        $this->startSection($this->courentSection);
        $this->protectedIncludeScope($this->templatePath . $template, $fullData);
        $output = $this->endSection(true);
        while (ob_get_level() > $this->levelStartObcache) {
            ob_clean();
        }

        if (!empty($this->layout)) {
            $layout = $this->layout;
            $this->layout = '';
            return $this->templateToString($layout, $fullData);
        }

        return $output;
    }

    /**
     * @param string $_L_A_Y_O_U_T_
     * @param array $_D_A_T_A_
     */
    protected function protectedIncludeScope($_L_A_Y_O_U_T_, array $_D_A_T_A_)
    {
        extract($_D_A_T_A_);
        unset($_D_A_T_A_);
        include $_L_A_Y_O_U_T_;
    }

    /**
     * @param string $templateName
     * @return string
     */
    protected function getTemplateFullName($templateName)
    {
        return rtrim($this->templatePath, DIRECTORY_SEPARATOR) .
                DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $templateName) .
                $this->attributes['extension'];
    }

    /**
     * @param string $layout
     */
    protected function extend($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param string $name
     * @param string $content
     */
    protected function startSection($name, $content = null)
    {
        if (ob_get_level() <= $this->levelStartObcache) {
            ob_start();
        }
        $this->courentSection = $name;
        if (!is_null($content)) {
            echo $content;
            $this->endSection();
        }
    }

    /**
     * @param bool $return
     * @return void|string
     */
    protected function endSection($return = false)
    {
        $content = ob_get_contents();
        ob_end_clean();

        if (empty($this->courentSection)) {
            // Si la section courante n'est pas définie, on est par défaut dans
            // la section "content". On ajoute à la suite si celle-ci est déjà
            // défine.
            $this->sectionContent['content'] = isset($this->sectionContent['content']) ?
                    $this->sectionContent['content'] :
                    '';
            $this->sectionContent['content'] .= $content;
        } else {
            $this->sectionContent[$this->courentSection] = $content;
        }
        $this->courentSection = '';

        ob_start();

        if ($return) {
            return $content;
        }
    }

    /**
     *
     * @param string $name
     * @param string $else
     * @throws RuntimeException
     */
    protected function section($name, $else = null)
    {
        if (array_search($name, array_keys($this->sectionContent)) === false) {
            if (is_null($else)) {
                throw new \RuntimeException("La vue n'a pas de section $name, ni de valeur de remplacement.");
            } else {
                echo $else;
            }
        } else {
            echo $this->sectionContent[$name];
        }
    }

    /**
     * affiche le contenu de la section en cour
     */
    protected function getParent()
    {
        $this->section(empty($this->courentSection) ? 'content' : $this->courentSection, '');
    }

}
