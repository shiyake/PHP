<?php

/**
*
*
*/

function C($key=null,$value=null){
	
	if($key==null){
		return false;
	}

	$c=new Cores\AccessConfig(BASEDIR.'/Cores/configs');

	if($value==null){
		return $c->offsetGet($key);
	}else{
		$c->offsetSet($key,$value);
	}
}
	

//创建数据库,根据当前配置文件中的数据库类型生成数据库对象
function D($table=null){	
	$dbconfig=C('dbconfig');
	return Cores\Databases::CreateDatabase($dbconfig,$table);
}

//创建模型
function M($modelName){

}

function hump($str){
	$result='';

	if(stripos($str,'_')){
		$str_array=explode("_",$str);
		foreach ($str_array as $key => $value) {
			$result.=ucfirst($value);
		}
	}else{
		$result=ucfirst($str);
	}

	return $result;
}

function guid($namespace = '') {    
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid =	substr($hash,  0,  8) .
            '-' .
            substr($hash,  8,  4) .
            '-' .
            substr($hash, 12,  4) .
            '-' .
            substr($hash, 16,  4) .
            '-' .
            substr($hash, 20, 12) ;
    return $guid;
}

function alert($content){
	echo "<script>alert('$content');</script>";
}

function debug($content){
	echo "<script>console.log('$content')</script>";
}

function redirectTo($url){
	echo "<script>window.location.href='$url';</script>";
}

function success($text){
	return '<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$text.'</div>';
}

function warning($text){
	return '<div class="alert alert-warning alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$text.'</div>';
}

function dangerConfirm($text){
	return '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
  <span aria-hidden="true">'.$text.'</span>
</button>';
}

function confirm($tips,$request,$btnValue){
	return $tips=warning($tips.'
        <p><a href="'.$request.'" class="btn btn-danger">'.$btnValue.'</a></p>');
}

function modal($title,$content){
	return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">'.$title.'</button>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      '.$content.'
    </div>
  </div>
</div>';
}

function generatorCommentsEditingForm($comments,$request=null){
    return '<div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        修改
                    </div>
                    <div class="panel-body">
                        <textarea class="form-control">'.$comments.'</textarea>
                        <div style="padding-top:10px;" class="text-right">
                            <a ref="'.$request.'" id="modify_comments_content" onclick="editCommentsContent(this)" class="btn btn-sm btn-primary">确定</a>
                        </div>
                    </div>
                </div>
            </div>';
}

function generatorCommentsViewingForm($comments,$time,$ref='#',$request=null,$rid=null){
    return '<div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        查看
                    </div>
                    <div class="panel-body">
                        <div class="list-group">
                          <a href="'.$ref.'" class="list-group-item">
                            <h4 class="list-group-item-heading">发布于 : '.$time.'</h4>
                            <p class="list-group-item-text">正文 : <p></p>'.$comments.'</p>
                          </a>
                        </div>
                        <div class="text-right">
                            <a href="admin.php?v='.$_GET['v'].'&action=edit_comments&rid='.$rid.'" class="btn btn-primary btn-sm">修改</a>
                        </div>
                    </div>
                </div>
            </div>';
}

function objectToArray($obj){
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if(is_array($arr)){
        return array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}

function arrayToObject($arr){
    if(is_array($arr)){
        return (object) array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}

function cutOutStr($str,$count=20){
	$s=mb_substr($str , 0 , $count);
	return strlen($str)>20?$s.'[...]':$s;
}
