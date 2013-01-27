<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *	version				:	1.3.2
 *	copyright			:	(c) 2012 Monoray
 *	website				:	http://www.monoray.ru/
 *	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/

class tFile extends CMessageSource {
    public $forceTranslation=true;

    const CACHE_KEY_PREFIX='Yii.InstallMessageSource.';

    /**
     * @var integer the time in seconds that the messages can remain valid in cache.
     * Defaults to 0, meaning the caching is disabled.
     */
    public $cachingDuration=0;
    /**
     * @var string the ID of the cache application component that is used to cache the messages.
     * Defaults to 'cache' which refers to the primary cache application component.
     * Set this property to false if you want to disable caching the messages.
     */
    public $cacheID='cache';
    /**
     * @var string the base path for all translated messages. Defaults to null, meaning
     * the "messages" subdirectory of the application directory (e.g. "protected/messages").
     */
    public $basePath;

    private $_files=array();

    public function __construct() {
        $this->basePath = Yii::getPathOfAlias('application.messages');
    }

    /**
     * Determines the message file name based on the given category and language.
     * If the category name contains a dot, it will be split into the module class name and the category name.
     * In this case, the message file will be assumed to be located within the 'messages' subdirectory of
     * the directory containing the module class file.
     * Otherwise, the message file is assumed to be under the {@link basePath}.
     * @param string $category category name
     * @param string $language language ID
     * @return string the message file path
     */
    protected function getMessageFile($category,$language)
    {
        if(!isset($this->_files[$category][$language]))
        {
            if(($pos=strpos($category,'.'))!==false)
            {
                $moduleClass=substr($category,0,$pos);
                $moduleCategory=substr($category,$pos+1);
                $class=new ReflectionClass($moduleClass);
                $this->_files[$category][$language]=dirname($class->getFileName()).DIRECTORY_SEPARATOR.'messages'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$moduleCategory.'.php';
            }
            else
                $this->_files[$category][$language]=$this->basePath.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$category.'.php';
        }
        return $this->_files[$category][$language];
    }

    /**
     * Loads the message translation for the specified language and category.
     * @param string $category the message category
     * @param string $language the target language
     * @return array the loaded messages
     */
    protected function loadMessages($category,$message,$language=null)
    {
        if($language===null)
            $language=Yii::app()->getLanguage();

        $messageFile=$this->getMessageFile($category,$language);

        if($this->cachingDuration>0 && $this->cacheID!==false && ($cache=Yii::app()->getComponent($this->cacheID))!==null)
        {
            $key=self::CACHE_KEY_PREFIX . $messageFile;
            if(($data=$cache->get($key))!==false)
                return unserialize($data);
        }

        if(is_file($messageFile))
        {
            $messages=include($messageFile);
            if(!is_array($messages))
                $messages=array();
            if(isset($cache))
            {
                $dependency=new CFileCacheDependency($messageFile);
                $cache->set($key,serialize($messages),$this->cachingDuration,$dependency);
            }
            if ($messages && isset($messages[$message]))
                return $messages[$message];
            return '%'.$message.'%';
        }
        else
            return array();
    }

    public static function getT($category,$message,$language=null) {
        $tFile = new tFile();
        return $tFile->loadMessages($category,$message,$language=null);
    }
}