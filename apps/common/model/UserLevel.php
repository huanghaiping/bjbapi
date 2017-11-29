<?php
namespace app\common\model;
class UserLevel extends Common {
	
	/**
	 * +---------------------------------------------------------------
	 * 保存会员等级
	 * +---------------------------------------------------------------
	 * 
	 * @param Array $postData	POST提交过来的数据
	 */
	public function addLevel($postData) {
		if (empty ( $postData ))
			return false;
		$data = array ();
		$data ['level_name'] = isset ( $postData ['level_name'] ) ? addSlashesFun ( $postData ['level_name'] ) : "";
		if (empty ( $data ['level_name'] )) {
			$this->error = '等级名称不能为空';
		}
		$data['level_value']=isset ( $postData ['level_value'] ) ? floatval ( $postData ['level_value'] ) : "";
		$data ['amount'] = isset ( $postData ['amount'] ) ? floatval ( $postData ['amount'] ) : "";
		$data ['discount'] = isset ( $postData ['discount'] ) ? floatval ( $postData ['discount'] ) : "";
		$data ['description'] = isset ( $postData ['description'] ) ? addSlashesFun ( $postData ['description'] ) : "";
		$data ['status'] = isset ( $postData ['status'] ) ? intval ( $postData ['status'] ) : "";
		$data ['condition']=isset($postData['condition']) ? intval($postData['condition']) : 0;
		$data ['lang'] = $this->lang;
		$data ['ctime'] = time ();
		if (empty ( $postData ['id'] )) {
			$result = $this->allowField ( true )->save ( $data );
		} else {
			$result = $this->allowField ( true )->where ( "id", $postData ['id'] )->update ( $data );
		}
		F ( "user_level_" . $this->lang, null );
		return $result;
	}
	
	/**
	 * 创建用户等级的等级值
	 * Enter description here ...
	 */
	public function createLevelValue(){
		return array(0,10,20,30,40,50,60,70,80,90,100,110);
	}
	
	/**
	 * +-----------------------------------------
	 * 获取所有的用户等级
	 * +----------------------------------------
	 */
	public function getUserLevel() {
		$row = F("user_level_" . $this->lang); 
		if (! $row) {
			$list = $this->where ( array ('lang' => $this->lang, 'status' => 1 ) )->order ( "id asc" )->select ();
			if ($list) {
				$list=$list->toArray();
				foreach ( $list as $value ) {
					$row [$value ['level_value']] = $value;
				}
				F ("user_level_" . $this->lang, $row );
			}
		}
		return $row;
	}
	
	/**
	 * +-----------------------------------------
	 * 根据等级ID获取相对应的用户
	 * +----------------------------------------
	 */
	public function getUserByLevenId($level_id) {
		if (empty ( $level_id ))
			return false;
		$map = array ('status' => 1, 'level_id' => $level_id );
		$user_info = model ( "User" )->field ( "uid,nickname,email,mobile,reg_time" )->where ( $map )->select ();
		if ($user_info){
			$user_info=$user_info->toArray();
		}
		return $user_info;
	}

    /**
     * 更新会员等级,折扣，消费总额
     * @param $user_id  用户ID
     * @return boolean
     */
    public function updateUserLevel($user_id) {
        $userLevelList = $this->getUserLevel ();
        if ($userLevelList) {
            //计算会员消费的所有总额
            $orderModel=model("Order");
            $userTotalAmount = $orderModel->getUserOrderAmount($user_id,1); //已付款订单总额
            foreach ( $userLevelList as $key => $value ) {
                if ($userTotalAmount >= $value ['amount']) {
                    $levelValue = $value ['level_value'];
                    $discount = $value ['discount'] / 100;
                }
            }
            $updateData=array('total_amount'=>$userTotalAmount); //更新累计修复额度
            $userModel=model ( 'User' );
            if (!isset( $this->user_info ['level_id'])){
                $this->user_info ['level_id']=$userModel->where("uid",$user_id)->value("level_id");
            }
            $loginUserLevelId=$this->user_info ['level_id'];
            //累计额度达到新等级，更新会员折扣
            if (isset ( $levelValue ) && $levelValue > $loginUserLevelId) {
                $updateData ['level_id'] = $levelValue;
                $updateData ['discount'] = $discount;
                $this->user_info['level_id']=$levelValue;
                session ( "USER_INFO",  $this->user_info ); //保存登录的信息
            }
            $userModel->where ( array ('uid' => $user_id ) )->update ( $updateData );
        }
    }

    /**
     *  获取用户等级条件
     * @param $userLevelCondition
     */
    public function getLevelIdByCondition($userLevelCondition){
        switch ($userLevelCondition){
            //升级后首次购买
            case 1 :
                $orderModel=model("Order");
                $userTotalAmount = $orderModel->getUserOrderAmount($this->user_info['uid'],'',$this->user_info['level_id']); //已付款订单总额
                if ($userTotalAmount<=0){
                    return true;
                }
                break;
        }
        return false;
    }

}