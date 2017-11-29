<?php
namespace app\jzadmin\controller\note;
class Notebook extends \app\jzadmin\controller\Common
{
    /**
     * 笔记本列表管理
     */
    public function index(){
        $param = $this->request->param();
        $keyword = isset ($param ['keyword']) ? addSlashesFun($param ['keyword']) : "";
        $where = array();
        if (!empty ($keyword)) {
            $where ['name'] = array('like', '%' . $keyword . '%');
        }
        $request = array('status','uid');
        foreach ($request as $value) {
            $status = isset ($param [$value]) ? $param [$value] : "";
            if ($status != "") {
                $where [$value] = $status;
                $this->assign($value, $status);
            }
        }
        $noteBookModel = model("Notebook");
        $noteBookList = $noteBookModel->where($where)->order("id desc")->paginate(20, false, array("query" => $param));
        if (!$noteBookList->isEmpty()) {
            $userList = model("User")->getUserInfoByUids(array_unique(getSubByKey($noteBookList->all(),"uid")));
            foreach ($noteBookList as $key => $value) {
                $value ['userInfo'] = $userList && array_key_exists($value ['uid'], $userList) ? $userList [$value ['uid']] : array();
                $noteList [$key] = $value;
            }
        }
        return $this->fetch('', array('keyword' => $keyword, 'list' => $noteBookList, 'page' => $noteBookList->render()));
    }
}