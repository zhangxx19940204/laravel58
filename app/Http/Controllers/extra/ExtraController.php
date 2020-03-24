<?php

namespace App\Http\Controllers\extra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Illuminate\Support\Facades\Input;
use App\EmailConfig;
use App\EmailData;
use Illuminate\Support\Facades\Storage;

class ExtraController extends Controller
{
    //
    public function get_mail_list(){
    	$user_id = Input::get('id', '');
    	$config_data = EmailConfig::where('user_id', $user_id)->get()->toarray();
    	foreach ($config_data as $key=>$value){
    		//进行邮箱数据的读取
    		$this->get_single_mail_data($value);
    	}
    	
    }
    
    public function get_single_mail_data($data){
    	var_dump($data);
    	//进行邮箱的连接，并获得邮件的文件列表
		// var_dump($data);
		$mailServer=$data['host_port']; //IMAP主机

		$mailLink="{{$mailServer}}INBOX" ; //imagp连接地址：不同主机地址不同
		
		$mailUser = $data['email_address']; //邮箱用户名
		
		$mailPass = $data['email_password']; //邮箱密码
		
		$mbox = imap_open($mailLink,$mailUser,$mailPass); //开启信箱imap_open
		
		$totalrows = imap_num_msg($mbox); //取得信件数
				
				
		for ($i=1;$i <= $totalrows;$i++){
			echo $i. "<br/>";
		   	//echo $headers = imap_fetchheader($mbox, $i); //获取信件标头  (信的来源信息和发送时间等)
		  	$header = imap_header($mbox, $i);
		  	
		  	$content = strip_tags(iconv("GBK", "utf-8//IGNORE", base64_decode(imap_body($mbox, $i))));
		  	var_dump($header,$content);
		  	
		    // imap_mail_move($mbox,  $i , $data->move_folder);
		  	
		}
		
		// imap_expunge($mbox);
    	die();
    }
    
    public function showupload(Request $request){
    	return view('extra.showupload', []);
    }
    //处理传上来的图片
    public function uploadimage(Request $request){
    	
		if ($_FILES["file"]["error"] > 0)
		  {
			echo "Error: " . $_FILES["file"]["error"] . "<br />";
		  }
		else
		  {
			  //echo "Upload: " . $_FILES["file"]["name"] . "<br />";
			  //echo "Type: " . $_FILES["file"]["type"] . "<br />";
			  //echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
			  //echo "Stored in: " . $_FILES["file"]["tmp_name"];
			  $realPath = $_FILES["file"]["tmp_name"];
			  //接收到图片继续处理
			  $filename = date('Y_m_d_H_i_s') .time().rand(10,999). $_FILES["file"]["name"];
			  //$bool = Storage::disk('public')->put($filename, file_get_contents($realPath));
			  //var_dump($bool);
			  move_uploaded_file($_FILES["file"]["tmp_name"],"upload/wechathead/" . $filename);
			  $absolute_path = substr($_SERVER['SCRIPT_FILENAME'],0,-10);
			  $real_file_path = $absolute_path."/upload/wechathead/" . $filename;
			  //加载图片合成图片
			  $real_bg_path = $absolute_path.'/upload/bg.png';
		      
		      $dst = imagecreatefromstring(file_get_contents($real_bg_path));
			  $src = imagecreatefromstring(file_get_contents($real_file_path));
			  
			  //获取水印图片的宽高
				$src_w =139;$src_h=58;
				list($src_w,$src_h) = getimagesize($real_file_path);
				//如果水印图片本身带透明色，则使用imagecopy方法
				//imagecopy($dst, $src, 410,230, 0, 0, $src_w, $src_h);// x大向右，y大向下
				imagecopyresized($dst, $src, 200,460, 0, 0, 160 , 220 ,$src_w, $src_h);// x大向右，y大向下
				
				//生成新图片名
				$relative_path = '/upload/newhead/'.date("YmdHis").rand(1000,9999).".png";
				$image = $absolute_path.$relative_path;
				//输出图片
				list($src_w, $src_h, $dst_type) = getimagesize($real_bg_path);
				switch ($dst_type) {
					case 1://GIF
						imagegif($dst, $image);
						break;
					case 2://JPG
						imagejpeg($dst, $image);
						break;
					case 3://PNG
		//              header('Content-Type: image/png');
						imagepng($dst, $image);
						break;
					default:
						break;
				}
				// return $image;
				return '<img src="https://www.wintersweet.cn'.$relative_path.'"  alt="点击长按保存" />';
			  //接收到图片进行处理
		  }
		
    }    
}

