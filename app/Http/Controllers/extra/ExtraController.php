<?php

namespace App\Http\Controllers\extra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Illuminate\Support\Facades\Input;
use App\EmailConfig;
use App\EmailData;
use App\EmailPass;
use Illuminate\Support\Facades\Storage;
use PhpImap\Mailbox;
use Illuminate\Support\Facades\DB;

class ExtraController extends Controller
{
    //
    public function get_mail_list(){
    	$user_id = Input::get('user', '');
    	//先去查询出可以读取的邮件列表
        $from_mail_list = EmailPass::where('user_id', $user_id)->get();
        $from_mail_arr = [];//可以被获取的收件人的邮件（在这个邮件列表中的邮箱可以抓取内容）
        foreach ($from_mail_list as $key=>$single_from_mail){
            $from_mail_arr[] = $single_from_mail->email_account;
        }

    	//查询属于这个用户的邮件账号，同时查询出状态为可用的（1）
    	$config_data = EmailConfig::where('user_id', $user_id)->where('status','>','0')->get()->toarray();
//        var_dump($config_data);
//        die();
    	foreach ($config_data as $key=>$value){
    		//进行邮箱数据的读取
            //需要先进行
    		return $this->get_single_mail_data($value,$from_mail_arr);
    	}
    	
    }

    //获取邮箱账号的相关的邮件
    public function get_single_mail_data($data,$from_mail_arr){

        $mailServer=$data['host_port']; //IMAP主机

        $mailbox = new Mailbox(
            "{{$mailServer}}INBOX", // IMAP server and mailbox folder
            $data['email_address'], // Username for the before configured mailbox
            $data['email_password'], // Password for the before configured username
            null, // Directory, where attachments will be saved (optional)
            'UTF-8' // Server encoding (optional)
        );
        $mailbox->setAttachmentsIgnore(true);

        try {
            // Get all emails (messages)
            // PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
            $mailsIds = $mailbox->searchMailbox('UNSEEN');  //结果为：array(2) { [0]=> int(3) [1]=> int(4) }
        } catch(PhpImap\Exceptions\ConnectionException $ex) {
            echo "连接出错，稍后再试 " . $ex;
            die();
        }

        // If $mailsIds is empty, no emails could be found
        if(!$mailsIds) {
            die('并未收到新的信息邮件');
        }

        // Get the first message
        // If '__DIR__' was defined in the first line, it will automatically
        // save all attachments to the specified directory
        $be_to_flaged = [];
        $total_mail_data = [];
        foreach ($mailsIds as $key=>$mailId){//循环符合条件邮件id列表

            $email = $mailbox->getMail($mailId,false);

            //处理相关信息的分离和整合
            $from_email = $email->fromAddress;

            //判断此邮件发件人是否在执行列表中
            if (!in_array($from_email,$from_mail_arr)){
                continue;
            }else{

                $email_title = $email->subject;
                $email_date =date('Y-m-d H:i:s', strtotime($email->date));
                $email_content = $email->textHtml;

                echo $email_content.'<br/>';
                echo $email->textPlain.'<br/>';

//                $mail_data = [];
                if ($from_email == '2162750756@qq.com'){//此信息由公司内部模板发出
                    //姓名、电话、来源
                    $mail_data = $this->deal_inside_mail($email_content);
                }else{ //是由的外部模板
                    $mail_data = $this->deal_outside_mail($email_content);
                }

                echo '<pre>';
                var_dump($mail_data);
                echo '</pre>';


                $mail_data['from_mail'] = $from_email;
                $mail_data['mail_title'] = $email_title;
                $mail_data['mail_date'] = $email_date;
                $mail_data['mail_content'] = $email_content;

                //获取到了数据，进行插入数据库
                $mail_data['econfig_id'] = $data['id'];
                $mail_data['user_id'] = $data['user_id'];
                $total_mail_data[] = $mail_data;

                $be_to_flaged[] = $mailId;//将已经记录好的邮件id记录，准备下步进行星标


            }//此邮件在认证列表中

        }

        //邮件循环完毕，进行插入数据库和更改状态
        var_dump($total_mail_data);
        try
        {
            DB::connection()->enableQueryLog();
            $insert_status = DB::table('email_data')->insert($total_mail_data);
            dump(DB::getQueryLog());

            $mailbox->setFlag($be_to_flaged,'\\Seen \\Flagged'); //将星标的邮件，标志成已读 同时 为已记录邮件标记星标
            $close_status = $mailbox->disconnect();
            if ($close_status){
                echo '邮箱关闭';
            }else{
                echo '邮箱未关闭';
            }

        }
        //捕获异常
        catch(Exception $e)
        {
            echo '错误信息: ' .$e->getMessage();
        }

        echo '执行完毕';


    }
    //用来处理我们自己的提交模板
    public function deal_inside_mail($str){
        $position_name =  strpos($str, '姓名：');//返回
        $position_phone = strpos($str, '电话：');
        $position_message = strpos($str, '留言内容：');
        $position_from = strpos($str, '来源：');
        $position_des = strpos($str, '描述：');


        $username = str_replace("姓名：","",substr($str,$position_name, ($position_phone-$position_name))); //电话这个词的位置减去姓名词所在的位置
        $phone = str_replace("电话：","",substr($str,$position_phone, ($position_message-$position_phone)));
        $from = str_replace("来源：","",substr($str,$position_from, ($position_des-$position_from)));

        return ['username'=>$username,'phone'=>$phone,'from'=>$from,'title'=>'','data_date'=>'2000-01-01 00:00:00'];
    }
    //用来处理外来的模板
    public function deal_outside_mail($str){
        $position_title_first = strpos($str, '"');
        $position_title_second = strrpos($str, '"');


        $position_date_start = strrpos($str, '时间为：');
        $position_date_end = strrpos($str, '。请');


        $position_phone_start = strrpos($str, '电话:');



        $username = '';
        $from = '';
        $title = str_replace('"',"",substr($str,$position_title_first, ($position_title_second-$position_title_first)));
        $data_date = str_replace('时间为：',"",substr($str,$position_date_start, ($position_date_end-$position_date_start)));
        $phone = str_replace("电话:","",substr($str,$position_phone_start,18));

        return ['username'=>$username,'phone'=>$phone,'from'=>$from,'title'=>$title,'data_date'=>$data_date];
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

