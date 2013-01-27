<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *    version                :    1.3.2
 *    copyright            :    (c) 2012 Monoray
 *    website                :    http://www.monoray.ru/
 *    contact us            :    http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/

class langFieldWidget extends CWidget
{
    public $model;
    public $field;

    public $type = 'string';

    public $htmlOption;

    private $_activeLang;

    public $row_id = '';

    public function getViewPath($checkTheme = false)
    {
        return Yii::getPathOfAlias('application.modules.lang.views');
    }

    public function run()
    {
        $this->_activeLang = Lang::getActiveLangs();

        echo $this->genContentTab($this->field.'_'.Yii::app()->language, Yii::app()->language);
    }

    private function genContentTab($field, $lang)
    {
        $fieldId = 'id_' . $this->field.'_'.$lang;

        $str = '';

        $str .= '<div class="rowold">';
        $str .= CHtml::activeLabel($this->model, $this->field, array('required' => $this->model->isLangAttributeRequired($this->field)));

        switch ($this->type) {
            case 'string':
                $str .= CHtml::activeTextField($this->model, $field, array(
                    'class' => 'width300',
                    'maxlength' => 255,
                    'id' => $fieldId
                ));
                break;

            case 'text':
                $str .= CHtml::activeTextArea($this->model, $field, array(
                    'class' => 'width500',
                    'id' => $fieldId
                ));
                break;

            case 'text-editor':
                $str .= $this->widget('application.modules.editor.EImperaviRedactorWidget',array(
                                        'model'=>$this->model,
                                        'attribute'=>$field,
                                        'htmlOptions' => array('class' => 'editor_textarea', 'style'=>'width: 940px; height: 300px;'),
                                        'options'=>array(
                                            'toolbar'=>'custom', /*original, classic, mini, */
                                            'lang' => Yii::app()->language,
                                            'focus' => false,
                                        )
                                    ),true);
                break;
        }

        $str .= CHtml::error($this->model, $field);
        $str .= '</div>';

        return $str;
    }

}