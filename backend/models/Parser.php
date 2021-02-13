<?php

namespace backend\models;

use Yii;


class Parser
{
    public function getFileContent($url){

        $content = @file_get_contents($url);

        return json_decode($content, true);
    }
}