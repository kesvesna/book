<?php

namespace backend\models;

use Yii;
use yii\base\Model;


class Parser extends Model
{
    public $parserSourceAddress = '';

    public function rules()
    {
        return [
            [['parserSourceAddress'], 'required'],
        ];
    }

    public function getFileContent(){

        $content = @file_get_contents($this->parserSourceAddress);

        return json_decode($content, true);
    }
}