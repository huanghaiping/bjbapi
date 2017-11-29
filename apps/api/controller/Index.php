<?php
namespace app\api\controller;
class Index extends Common
{
    public function index()
    {
        return output(401, lang('INVALID_REQUEST'));
    }
}
