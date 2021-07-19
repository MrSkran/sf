<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\User;
use App\Classes\GeniusMailer;
use App\Models\Notification;
use Auth;
use Illuminate\Support\Facades\Input;
use Validator;

class RegisterController extends Controller
{
	public function showRegisterForm()
	{
		$this->code_image();
		return view('user.register');
	}
	
    public function register(Request $request)
    {

    	$gs = Generalsetting::findOrFail(1);

    	if($gs->is_capcha == 1)
    	{
	        $value = session('captcha_string');
	        if ($request->codes != $value){
	            return response()->json(array('errors' => [ 0 => 'Please enter Correct Capcha Code.' ]));    
	        }    		
    	}


        //--- Validation Section

        $rules = [
		        'email'   => 'required|email|unique:users',
		        'password' => 'required|confirmed'
                ];
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

	        $user = new User;
	        $input = $request->all();        
	        $input['password'] = bcrypt($request['password']);
	        $token = md5(time().$request->name.$request->email);
	        $input['verification_link'] = $token;
	        $input['affilate_code'] = md5($request->name.$request->email);
			$user->fill($input)->save();

	          if(!empty($request->vendor))
	          {
	            $user->is_vendor = 1;
	            $user->update();
	          }

	        if($gs->is_verification_email == 1)
	        {
	        $to = $request->email;
	        $subject = '.تأكيد البريد الإلكتروني الخاص بك';
	        $msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="x-apple-disable-message-reformatting" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<style type="text/css">
    body, .maintable { height:100% !important; width:100% !important; margin:0; padding:0;}
    img, a img { border:0; outline:none; text-decoration:none;}
    p {margin-top:0; margin-right:0; margin-left:0; padding:0;}
    .ReadMsgBody {width:100%;}
    .ExternalClass {width:100%;}
    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100%;}
    img {-ms-interpolation-mode: bicubic;}
    body, table, td, p, a, li, blockquote {-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%;}
</style>
<style type="text/css">
@media only screen and (max-width: 600px) {
 .rtable {width: 100% !important;}
 .rtable tr {height:auto !important; display: block;}
 .contenttd {max-width: 100% !important; display: block; width: auto !important;}
 .contenttd:after {content: ""; display: table; clear: both;}
 .hiddentds {display: none;}
 .imgtable, .imgtable table {max-width: 100% !important; height: auto; float: none; margin: 0 auto;}
 .imgtable.btnset td {display: inline-block;}
 .imgtable img {width: 100%; height: auto !important;display: block;}
 table {float: none;}
 .mobileHide {display: none !important;}
}
</style>
<!--[if gte mso 9]>
<xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
</head>
<body style="overflow: auto; padding:0; margin:0; font-size: 14px; font-family: arial, helvetica, sans-serif; cursor:auto; background-color:#feffff">
<table cellspacing="0" cellpadding="0" width="100%" bgcolor="#feffff">
<tr>
<td style="FONT-SIZE: 0px; HEIGHT: 0px; LINE-HEIGHT: 0"></td>
</tr>
<tr>
<td valign="top">
<table class="rtable" style="WIDTH: 600px; MARGIN: 0px auto" cellspacing="0" cellpadding="0" width="600" align="center" border="0">
<tr>
<th class="contenttd" style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 600px; BORDER-BOTTOM: medium none; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; TEXT-ALIGN: left; PADDING-TOP: 0px; PADDING-LEFT: 0px; BORDER-LEFT: medium none; PADDING-RIGHT: 0px; BACKGROUND-COLOR: #feffff">
<table style="WIDTH: 100%" cellspacing="0" cellpadding="0" align="left" data-hidewhenresp="0">
<tr style="HEIGHT: 102px" height="102">
<th class="contenttd" style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 570px; VERTICAL-ALIGN: top; BORDER-BOTTOM: medium none; FONT-WEIGHT: normal; PADDING-BOTTOM: 20px; TEXT-ALIGN: left; PADDING-TOP: 20px; PADDING-LEFT: 15px; BORDER-LEFT: medium none; PADDING-RIGHT: 15px; BACKGROUND-COLOR: transparent"><!--[if gte mso 12]>
    <table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td align="center">
<![endif]-->
<table class="imgtable" style="MARGIN: 0px auto" cellspacing="0" cellpadding="0" align="center" border="0">
<tr>
<td style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; PADDING-LEFT: 2px; PADDING-RIGHT: 2px" align="center">
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BACKGROUND-COLOR: transparent"><img style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; DISPLAY: block" alt="" src="https://soft-fire.com/img/Image_1_d8dea417-effe-47bc-a3f0-9647ed086272.png" width="246" hspace="0" vspace="0" /></td>
</tr>
</table>
</td>
</tr>
</table>
<!--[if gte mso 12]>
    </td></tr></table>
<![endif]--></th>
</tr>
</table>
</th>
</tr>
<tr>
<th class="contenttd" style="BORDER-TOP: #c0392b 5px solid; BORDER-RIGHT: medium none; WIDTH: 600px; BORDER-BOTTOM: medium none; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; TEXT-ALIGN: left; PADDING-TOP: 0px; PADDING-LEFT: 0px; BORDER-LEFT: medium none; PADDING-RIGHT: 0px; BACKGROUND-COLOR: #feffff">
<table style="WIDTH: 100%" cellspacing="0" cellpadding="0" align="left" data-hidewhenresp="0">
<tr style="HEIGHT: 206px" height="206">
<th class="contenttd" style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 570px; VERTICAL-ALIGN: top; BORDER-BOTTOM: medium none; FONT-WEIGHT: normal; PADDING-BOTTOM: 20px; TEXT-ALIGN: left; PADDING-TOP: 10px; PADDING-LEFT: 15px; BORDER-LEFT: medium none; PADDING-RIGHT: 15px; BACKGROUND-COLOR: transparent">
<p style="FONT-SIZE: 36px; MARGIN-BOTTOM: 1em; FONT-FAMILY: geneve, arial, helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #2d2d2d; TEXT-ALIGN: center; LINE-HEIGHT: 56px; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align="center">&#1588;&#1603;&#1585;&#1575;&#1611; &#1604;&#1578;&#1587;&#1580;&#1610;&#1604;&#1603; &#1601;&#1610; &#1587;&#1608;&#1601;&#1578; &#1601;&#1575;&#1610;&#1585;</p>
<p style="FONT-SIZE: 18px; MARGIN-BOTTOM: 1em; FONT-FAMILY: arial, helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #575757; TEXT-ALIGN: center; LINE-HEIGHT: 28px; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align="center">&#1575;&#1604;&#1585;&#1580;&#1575;&#1569; &#1575;&#1604;&#1590;&#1594;&#1591; &#1593;&#1604;&#1609; &#1575;&#1604;&#1585;&#1575;&#1576;&#1591; &#1571;&#1583;&#1606;&#1575;&#1607; &#1604;&#1578;&#1601;&#1593;&#1610;&#1604; &#1581;&#1587;&#1575;&#1576;&#1603;</p>
<!--[if gte mso 12]>
    <table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td align="center">
<![endif]-->
<table class="imgtable btnset" style="TEXT-ALIGN: center; MARGIN: 0px auto" cellspacing="0" cellpadding="0" border="0">
<tr>
<td class="contenttd" style="VERTICAL-ALIGN: middle; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px"><a href='.url('user/register/verify/'.$token).'><img title="" border="none" alt="&#1575;&#1590;&#1594;&#1591; &#1607;&#1606;&#1575;&#13;
" src="https://soft-fire.com/img/Image_2_010f125a-c44c-4dbd-b13e-40cf51b041e6.png" /></a> </td>
</tr>
</table>
<!--[if gte mso 12]>
    </td></tr></table>
<![endif]--></th>
</tr>
</table>
</th>
</tr>
<tr>
<th class="contenttd" style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 600px; BORDER-BOTTOM: medium none; FONT-WEIGHT: normal; PADDING-BOTTOM: 1px; TEXT-ALIGN: left; PADDING-TOP: 1px; PADDING-LEFT: 0px; BORDER-LEFT: medium none; PADDING-RIGHT: 0px; BACKGROUND-COLOR: #feffff">
<table style="WIDTH: 100%" cellspacing="0" cellpadding="0" align="left">
<tr style="HEIGHT: 114px" height="114">
<th class="contenttd" style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 570px; VERTICAL-ALIGN: top; BORDER-BOTTOM: medium none; FONT-WEIGHT: normal; PADDING-BOTTOM: 1px; TEXT-ALIGN: left; PADDING-TOP: 1px; PADDING-LEFT: 15px; BORDER-LEFT: medium none; PADDING-RIGHT: 15px; BACKGROUND-COLOR: #feffff">
<div style="PADDING-BOTTOM: 10px; TEXT-ALIGN: center; PADDING-TOP: 10px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px">
<table class="imgtable" style="DISPLAY: inline-block" cellspacing="0" cellpadding="0" border="0">
<tr>
<td style="PADDING-RIGHT: 5px"><a href="https://twitter.com/s0ftfire" target="_blank"><img title="Twitter" style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; DISPLAY: block" alt="Twitter" src="https://soft-fire.com/img/Image_3_48e377b3-7322-4b87-a4d0-1f7a801ac916.png" width="48" /></a> </td>
<td><a href="https://www.youtube.com/channel/UC1LmIeyogjplV0BS7WwUvnA?view_as=subscriber" target="_blank"><img title="Youtube" style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; DISPLAY: block" alt="Youtube" src="https://soft-fire.com/img/Image_4_2159d08f-1a89-493a-8d4e-01c53adc95e0.png" width="48" /></a> </td>
</tr>
</table>
</div>
<p style="FONT-SIZE: 10px; MARGIN-BOTTOM: 1em; FONT-FAMILY: arial, helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #7c7c7c; TEXT-ALIGN: center; LINE-HEIGHT: 12px; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align="center">&nbsp;</p>
<p style="FONT-SIZE: 10px; MARGIN-BOTTOM: 1em; FONT-FAMILY: arial, helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #7c7c7c; TEXT-ALIGN: center; LINE-HEIGHT: 12px; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align="center">Soft-Fire . All rights reserved 2020.©</p>
</th>
</tr>
</table>
</th>
</tr>
</table>
</td>
</tr>
<tr>
<td style="FONT-SIZE: 0px; HEIGHT: 8px; LINE-HEIGHT: 0">&nbsp;</td>
</tr>
</table>
<!-- Created with MailStyler 2.3.1.100 -->
</body>
</html>

';
                
	        //Sending Email To Customer
	        if($gs->is_smtp == 1)
	        {
	        $data = [
	            'to' => $to,
	            'subject' => $subject,
	            'body' => $msg,
	        ];

	        $mailer = new GeniusMailer();
	        $mailer->sendCustomMail($data);
	        }
	        else
	        {
	        $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
	        mail($to,$subject,$msg,$headers);
	        }
          	return response()->json('نحن بحاجة إلى التحقق من عنوان بريدك الإلكتروني. لقد أرسلنا رابط التحقق إلى '.$to.' للتحقق من عنوان البريد الإلكتروني الخاص بك. يرجى النقر على الرابط المرسل للمتابعة.');
	        }
	        else {

            $user->email_verified = 'Yes';
            $user->update();
	        $notification = new Notification;
	        $notification->user_id = $user->id;
	        $notification->save();
            Auth::guard('web')->login($user); 
          	return response()->json(1);
	        }

    }
    private function  code_image()
    {
        $actual_path = str_replace('project','',base_path());
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image,0,0,200,50,$background_color);

        $pixel = imagecolorallocate($image, 0,0,255);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixel);
        }

        $font = $actual_path.'assets/front/fonts/NotoSans-Bold.ttf';
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length-1)];
        $word='';
        //$text_color = imagecolorallocate($image, 8, 186, 239);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $cap_length=6;// No. of character in image
        for ($i = 0; $i< $cap_length;$i++)
        {
            $letter = $allowed_letters[rand(0, $length-1)];
            imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
            $word.=$letter;
        }
        $pixels = imagecolorallocate($image, 8, 186, 239);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixels);
        }
        session(['captcha_string' => $word]);
        imagepng($image, $actual_path."assets/images/capcha_code.png");
    }

    public function token($token)
    {
        $gs = Generalsetting::findOrFail(1);

        if($gs->is_verification_email == 1)
	        {    	
        $user = User::where('verification_link','=',$token)->first();
        if(isset($user))
        {
            $user->email_verified = 'Yes';
            $user->update();
	        $notification = new Notification;
	        $notification->user_id = $user->id;
			$notification->save();
	        $data = [
	            'to' => "crazyplayz048@gmail.com",
	            'subject' => "dddd",
	            'body' => "dsdsd",
	        ];
	        $mailer = new GeniusMailer();
	        $mailer->sendCustomMail($data);
            Auth::guard('web')->login($user); 
            return redirect()->route('user-dashboard')->with('success','.تم التحقق من بريدك الإلكتروني بنجاح');
        }
    		}
    		else {
    		return redirect()->back();	
    		}
    }
}
