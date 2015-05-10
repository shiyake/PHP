<?php

namespace Api\Controller;
use Think\Controller;
use Application\Common\common;

function isInfoNull($info){
	if(is_array($info)){
		foreach($info as $key => $value){if($value==null){return true;}}
	}else{return $info==null;}
}

function guid() {
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = substr($charid, 0, 8).$hyphen
    .substr($charid, 8, 4).$hyphen
    .substr($charid,12, 4).$hyphen
    .substr($charid,16, 4).$hyphen
    .substr($charid,20,12);
    return $uuid;
}

function getServerResponse($status,$msg,$data){
	$ajaxData['status']=$status;
	$ajaxData['msg']=$msg;
	$ajaxData['data']=$data;
	return $ajaxData;
}

class UserController extends Controller {

	protected $requestMethod='get.';
	protected $groupList=["root"=>'0',"admin"=>'1',"user"=>'2',"visitor"=>'3'];
	protected $groupListReversion=['0'=>'root','1'=>'admin','2'=>'user','3'=>'visitor'];

	public function index(){
		$this->show('非法操作');
	}

	/*
	* @param $fieldName string 字段名
	* @param $filedValue string 字段值
	*/
	protected function getUserId($fieldName,$fieldValue){
		return D('User')->where("`%s`='%s'",array($fieldName,$fieldName))->getField('`user_id`');
	}

	protected function getUserByUserId($id){
		if(isInfoNull($id)){
			return 0;//数据不能为空
		}else{
			$postData=M('User')->where("user_id=$id")->field(array('user_password'),true)->find();
			if($postData){
				return $postData;
			}else{
				return 0;//读取失败
			}
		}
	}

	protected function getUserGroup($user_id){
		return D('UserGroup')->where("`user_id`=$user_id")->getField('`ug_type`');
	}

	protected function getGroupUser($page,$limit,$groupName){
		if(isInfoNull(array($page,$limit))){
			$this->ajaxReturn(getServerResponse('0','数据不能为空',''));
		}else{
			$allUser=D('UserGroup');
			$start=10*$page-9;
			$end=10*$page+1;
			$userlist=$allUser->where("`ug_type`='$groupName'")->page($start,$end)->field('`user_id`')->select();
			if($userlist){
				$userCount=count($userlist);
				foreach ($userlist as $key => $user) {
					$userData[$key]=$this->getUserByUserId($user['user_id']);
				}
				$this->ajaxReturn(getServerResponse('0','读取成功',$userData));
			}else{
				$this->ajaxReturn(getServerResponse('0','读取失败或该组用户为空',$userlist));
			}
		}
	}

	public function changeUserGroup($user_id,$ug_type){
		$upToUser['ug_type']=$ug_type;
		D('UserGroup')->where("`user_id`=%s",array($user_id))->data($upToUser)->save();
	}

	public function register(){
		$email=I($this->requestMethod."email");
		$password=I($this->requestMethod."password");
		if(isInfoNull(array($email,$password))){
			$this->ajaxReturn(getServerResponse('0','数据不能为空',''));
		}else{
			$currentTime=date('y-m-d h:i:s',time());
			$registerUser=D('User');
			$registerData['user_email']=$email;
			$registerData['user_password']=sha1($password);
			$registerData['token_id']=guid();
			$registerData['user_activate_key']=guid();
			$registerData['user_register_time']=$currentTime;
			$registerData['user_last_login_time']=$currentTime;
			$data=$registerUser->create($registerData);
			if(!$data){
				$this->ajaxReturn(getServerResponse('0',$registerUser->getError(),''));
			}else{
				$result=$registerUser->add();
				if($result){
					$userGroupData['user_id']=$this->getUserId('user_email',$email);
					$userGroupData['ug_type']=$this->groupList['visitor'];
					$handGroup=D('UserGroup');
					if($handGroup->create($userGroupData)){
						if($handGroup->add()){
							$registerData['user_password']=null;
							$registerData['user_id']=$userGroupData['user_id'];
							$registerData['user_group']=$userGroupData['ug_type'];
							$this->ajaxReturn(getServerResponse('1','注册成功',$registerData));
						}else{
							$this->ajaxReturn(getServerResponse('0',$handGroup->getError(),''));
						}
					}else{
						$this->ajaxReturn(getServerResponse('0','注册失败',''));
					}
				}else{
					$this->ajaxReturn(getServerResponse('0',$registerUser->getError(),''));
				}
			}
		}
	}

	public function login(){
		$email=I($this->requestMethod."email");
		$password=I($this->requestMethod."password");
		if(isInfoNull(array($email,$password))){
			$this->ajaxReturn(getServerResponse('0','数据不能为空',''));
		}else{
			$loginUser=D('User');
			$password=sha1($password);
			$data=$loginUser->where("`user_email`='$email' AND `user_password`='$password'")->field(array('user_password'),true)->find();
			$modifiedLoginTime['token_id']=guid();
			$loginUser->where("`user_email`='$email'")->data($modifiedLoginTime)->save();
			if($data){
				$this->ajaxReturn(getServerResponse('0','验证成功',$data));
			}else{
				$this->ajaxReturn(getServerResponse('0','验证失败,帐号或密码有误',''));
			}
		}
	}

	public function activate(){
		$activate_key=I($this->requestMethod."activate_key");
		$user_id=I($this->requestMethod."user_id");
		$token_id=I($this->requestMethod."token_id");
		$ug_type=$this->getUserGroup($user_id);
		if($ug_type!='3'){
			$this->ajaxReturn(getServerResponse('1','您已经验证过了,请不要重复验证',''));
		}else{
			$activate_result=D('User')->where("`user_id`=$user_id AND `user_activate_key`='$activate_key' AND `token_id`='$token_id'")->select();
			if($activate_result){
				$this->changeUserGroup($user_id,$this->groupList['user']);
				$this->ajaxReturn(getServerResponse('1','帐户验证成功',$activate_result));
			}else{
				$this->ajaxReturn(getServerResponse('0','帐户验证失败',$activate_result));
			}
		}
	}

	public function getAllUser(){
		$page=I($this->requestMethod."page");
		$limit=I($this->requestMethod."limit");
		if(isInfoNull(array($page,$limit))){
			$this->ajaxReturn(getServerResponse('0','数据不能为空',''));
		}else{
			$allUser=D('User');
			$start=10*$page-9;
			$end=10*$page+1;
			$userlist=$allUser->order('`user_register_time`')->page($start,$end)->field(array('user_password','user_sex'),true)->select();
			if($userlist){
				$this->ajaxReturn(getServerResponse('0','数据不能为空',$userlist));
			}else{
				$this->ajaxReturn(getServerResponse('0',$userlist->getError(),$userlist));
			}
		}
	}

	public function getAllVisitor(){
		$page=I($this->requestMethod."page");
		$limit=I($this->requestMethod."limit");
		$this->getGroupUser($page,$limit,$this->groupList['visitor']);
	}

	public function getAllCommonUser(){
		$page=I($this->requestMethod."page");
		$limit=I($this->requestMethod."limit");
		$this->getGroupUser($page,$limit,$this->groupList['user']);
	}

	public function getAllAdmin(){
		$page=I($this->requestMethod."page");
		$limit=I($this->requestMethod."limit");
		$this->getGroupUser($page,$limit,$this->groupList['admin']);	
	}

	public function getAllRoot(){
		$page=I($this->requestMethod."page");
		$limit=I($this->requestMethod."limit");
		$this->getGroupUser($page,$limit,$this->groupList['root']);
	}

	protected function isUserRoot($type){return $this->groupListReversion[$type]==='root';}

	protected function isUserAdmin($type){return $this->groupListReversion[$type]==='admin';}

	protected function isUserCommon($type){return $this->groupListReversion[$type]==='user';}

	protected function isUserVisitor($type){return $this->groupListReversion[$type]==='visitor';}

	public function addPrivilege(){
		$ug_type=I($this->requestMethod."ug_type");
		$ugp_name=I($this->requestMethod."ugp_name");
		$user_id=I($this->requestMethod."user_id");
		if(isInfoNull(array($ug_type,$ugp_name,$user_id)) || !is_numeric($user_id)){
			$this->ajaxReturn(getServerResponse('0','数据不能为空或有误',''));
		}else{
			$user_group_type=$this->getUserGroup($user_id);
			if($this->isUserRoot() || $this->isUserAdmin()){
				$privilegeData['ug_type']=$ug_type;
				$privilegeData['ugp_name']=$ugp_name;
				$addRights=M('UserGroupPrivileges');
				$createResult=$addRights->create($privilegeData);
				if(!$createResult){
					$this->ajaxReturn(getServerResponse('0',$addRights->getError(),''));
				}else{
					$addResult=$addRights->add();
					if(!$addResult){
						$this->ajaxReturn(getServerResponse('0','添加权限失败',''));
					}else{
						$this->ajaxReturn(getServerResponse('1','添加权限成功',''));
					}
				}
			}else{
				$this->ajaxReturn(getServerResponse('0','没有权限',''));
			}
		}
	}

	public function removePrivilege(){
		$ugp_name=I($this->requestMethod.".ugp_name");
		$user_id=I($this->requestMethod.".user_id");
		if(isInfoNull($ugp_name)){
			$this->ajaxReturn(getServerResponse('0','数据不能为空或有误',''));
		}else{
			$user_group_type=$this->getUserGroup($user_id);
		}
	}

	public function getAllPrivilege(){

	}

	public function getPrivilege($token_id=''){
		if(isInfoNull($token_id)){
			return null;
		}else{
			$user_id=M('User')->where("`token_id`='$token_id'")->getField('user_id');
			$user_group_id=M('UserGroup')->where("`user_id`=$user_id")->getField('ug_id');
			$user_privilege=M('UserGroupPrivileges')->where("`ug_id`=$user_group_id")->field("`ugp_name`")->select();
			print_r($user_privilege);
			return $user_privilege;
		}
	}

	public function changePrivilege(){

	}

	public function removeUser(){
		$user_id=I($this->requestMethod.".user_id");
		$token_id=I($this->requestMethod.".token_id");
		if($this->isInfoNull(array($user_id,$token_id))){
			$this->ajaxReturn(getServerResponse('0','数据不能为空',''));
		}else{
			if($this->getPrivilegeByGuid($user_id)==='0'){
				$dResult=M('User')->where("`user_id`='$user_id' AND `token_id`=$token_id")->delete();
				if($dResult){
					$this->ajaxReturn(getServerResponse('1','删除成功',''));
				}else{
					$this->ajaxReturn(getServerResponse('0','删除失败',''));
				}
			}else{
				$this->ajaxReturn(getServerResponse('0','没有权限',''));
			}
		}
	}

	public function updateUserFile(){

	}

}

?>