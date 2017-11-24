<?php
/**
 * +-----------------------------------------
 * 输出成功的结果
 * +-----------------------------------------
 *
 * @param int $status 输出的状态
 * @param string $info 输出的信息
 * @param Array $data 输出的数据
 *
 * @return JSON     返回json数据
 */
function output($status, $info = "", $data = array())
{
    header('Content-type: application/json');
    $data = !empty($data) ? $data : array();
    $success_content = array();
    $success_content['entity'] = $data;
    $success_content ['status'] = $status; //获取数据状态，0表示获取数据失败，1表示获取数据成功
    $success_content ['msg'] = $info; ////成功或失败的提示信息，用于调试错误
    return json($success_content);
}

/**
 * +-----------------------------------------
 * 输出成功的结果
 * +-----------------------------------------
 *
 * @param int $status 输出的状态
 * @param string $info 输出的信息
 * @param Array $data 输出的数据
 *
 * @return JSON     返回json数据
 */
function outputJson($status, $info = "", $data = array())
{
    header('Content-type: application/json');
    $data = !empty($data) ? $data : array();
    $success_content = array();
    $success_content['entity'] = $data;
    $success_content ['status'] = $status; //获取数据状态，0表示获取数据失败，1表示获取数据成功
    $success_content ['msg'] = $info; ////成功或失败的提示信息，用于调试错误
    return json_encode($success_content);
}


/**
 * +-----------------------------------------
 * 输出列表的json数据
 * +-----------------------------------------
 *
 * @param Array $datalist 输出的数据
 * @param int $total_count 输出的数量
 * @param int $total_page 输出的总页数
 */
function outputList($dataList, $totalCount = 0, $totalPage = 1)
{
    $dataList = empty($dataList) ? array() : $dataList;
    $totalCount = $totalCount == 0 ? count($dataList) : $totalCount;
    $data = array("dataList" => $dataList, "totalCount" => $totalCount, "totalPage" => $totalPage);
    if (!empty($dataList)) {
        return output(1, lang('GET_SUCCESS'), $data);
    } else {
        return output(-1, lang('GET_SUCCESS'), $data);
    }
}