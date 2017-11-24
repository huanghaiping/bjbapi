<?php
namespace app\common\model;
use think\Model;
class Common extends Model {
    public $lang = "";

    /**
     *初始化项目开始
     */
    public function initialize() {
        $this->lang = LANG_SET;
    }
}