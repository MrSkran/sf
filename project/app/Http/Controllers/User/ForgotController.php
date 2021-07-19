<?php

namespace App\Http\Controllers\User;

use App\Models\Generalsetting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;

class ForgotController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForgotForm()
    {
      return view('user.forgot');
    }

    public function forgot(Request $request)
    {
      $gs = Generalsetting::findOrFail(1);
      $input =  $request->all();
      if (User::where('email', '=', $request->email)->count() > 0) {
      // user found
      $admin = User::where('email', '=', $request->email)->firstOrFail();
      $autopass = str_random(8);
      $input['password'] = bcrypt($autopass);
      $admin->update($input);
      $subject = "كلمة مرورك الجديدة في سوفت فاير";
      $msg = '<table cellspacing="0" cellpadding="0" width="600" align="center" border="0" style="font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(254, 255, 255); width: 600px; margin: 0px auto;"><tbody><tr><th style="text-align: left; border-width: medium; border-style: none; border-color: initial; width: 600px; font-weight: normal; padding: 0px;"><table cellspacing="0" cellpadding="0" align="left" style="width: 599.259px;"><tbody><tr height="102" style="height: 102px;"><th style="text-align: left; border-width: medium; border-style: none; border-color: initial; width: 570px; vertical-align: top; font-weight: normal; padding: 20px 15px; background-color: transparent;"><table cellspacing="0" cellpadding="0" align="center" border="0" style="margin: 0px auto;"><tbody><tr><td align="center" style="padding: 2px;"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="border-width: medium; border-style: none; border-color: initial; background-color: transparent;"><img alt="" src="https://ci5.googleusercontent.com/proxy/gnA_VA0kyz8QFe_ZAQCrS-k46ZK9c21hulVGZNyKIFizae1FTni3ZGHr8g7JYYOJBLM17nMSidUFDAqI-yYB9-U1TDWz9hU6Hy6XHPLQYH6azECll4rBY4nhod5L=s0-d-e1-ft#https://soft-fire.com/img/Image_1_d8dea417-effe-47bc-a3f0-9647ed086272.png" width="246" hspace="0" vspace="0" class="gmail-CToWUd" style="border-width: medium; border-color: initial; border-image: initial; outline: none; display: block;"></td></tr></tbody></table></td></tr></tbody></table></th></tr></tbody></table></th></tr><tr><th style="text-align: left; border-width: 5px medium medium; border-style: solid none none; border-top-color: rgb(192, 57, 43); border-right-color: initial; width: 600px; border-bottom-color: initial; font-weight: normal; padding: 0px; border-left-color: initial;"><table cellspacing="0" cellpadding="0" align="left" style="width: 599.259px;"><tbody><tr height="206" style="height: 206px;"><th style="text-align: left; border-width: medium; border-style: none; border-color: initial; width: 570px; vertical-align: top; padding: 10px 15px 20px; background-color: transparent;"><p style="text-align: right; margin-right: 0px; margin-bottom: 10px; margin-left: 0px; color: rgb(68, 68, 68); font-family: Helvetica, Arial, sans-serif; font-weight: normal; padding: 0px; line-height: 23px; font-size: 14px;">عزيزنا العميل</p><p style="text-align: right; margin-right: 0px; margin-bottom: 10px; margin-left: 0px; color: rgb(68, 68, 68); font-family: Helvetica, Arial, sans-serif; font-weight: normal; padding: 0px; line-height: 23px; font-size: 14px;"><span style="background-color: transparent; text-align: left; color: rgb(70, 85, 65); font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif;">لقد تم إعادة تعيين كلمة المرور لحساب Soft-Fire الخاص بك.</span></p><p style="text-align: right; margin-right: 0px; margin-bottom: 10px; margin-left: 0px; color: rgb(68, 68, 68); font-family: Helvetica, Arial, sans-serif; font-weight: normal; padding: 0px; line-height: 23px; font-size: 14px;"><span style="background-color: transparent;">كلمة المرور الجديدة: '.$autopass.'</span></p><table style="text-align: center; color: rgb(68, 68, 68); font-family: Helvetica, Arial, sans-serif; font-weight: 400; border-spacing: 0px; padding: 0px; vertical-align: middle; border-radius: 8px 8px 6px 6px; background-color: rgb(255, 255, 255); width: 580px; margin: 0px auto;"><tbody><tr style="padding: 0px; vertical-align: middle;"><td style="word-break: break-word; padding: 0px; vertical-align: middle; line-height: 23px; border-collapse: collapse;"><span style="display: block; font-size: 12px; padding: 10px 15px; text-align: right;"><div class="gmail-im" style="color: rgb(80, 0, 80);"><p style="margin-right: 0px; margin-bottom: 10px; margin-left: 0px; color: rgb(68, 68, 68); padding: 0px; line-height: 23px; font-size: 14px;"></p><div style="text-align: center;"><b>مع أطيب التحيات</b></div><div style="text-align: center;"><b>فريق سوفت فاير.</b></div><p></p></div></span></td></tr></tbody></table><table cellspacing="0" cellpadding="0" style="font-weight: normal; width: 599.259px;"><tbody><tr height="114" style="height: 114px;"><th style="text-align: left; border-width: medium; border-style: none; border-color: initial; width: 570px; vertical-align: top; font-weight: normal; padding: 1px 15px;"><div style="padding: 10px; text-align: center;"><table cellspacing="0" cellpadding="0" border="0" style="display: inline-block;"><tbody><tr><td style="padding-right: 5px;"><a href="https://twitter.com/s0ftfire" target="_blank"><img title="Twitter" alt="Twitter" src="https://ci4.googleusercontent.com/proxy/lfCcWsp6o9C2_6Ab5Xj-057OmadKeOSO_Bl836cfCQhxXO81rWW4AB09Ce3uboSiIYEaROoU_qCBV8ZyyvbrUjF4cx9Hh4ZIOzVcXmP8oTL4J3nuCc0jJfAvDuKi=s0-d-e1-ft#https://soft-fire.com/img/Image_3_48e377b3-7322-4b87-a4d0-1f7a801ac916.png" width="48" class="gmail-CToWUd" style="border-width: medium; border-color: initial; border-image: initial; outline: none; display: block;"></a></td><td><a href="https://www.youtube.com/channel/UC1LmIeyogjplV0BS7WwUvnA?view_as=subscriber" target="_blank"><img title="Youtube" alt="Youtube" src="https://ci3.googleusercontent.com/proxy/wk6EsjlRhi3dHejisAu9QuTMXUotykaTVmttjBubb1YXQr70BqZLSpAIJQv1Kd2gtTtOpPpKr4fGjnCNCK1XtNuOMOlDWwDKe9n0LlORBjVe57Ts24Vam_09eOcG=s0-d-e1-ft#https://soft-fire.com/img/Image_4_2159d08f-1a89-493a-8d4e-01c53adc95e0.png" width="48" class="gmail-CToWUd" style="border-width: medium; border-color: initial; border-image: initial; outline: none; display: block;"></a></td></tr></tbody></table></div><p align="center" style="margin-right: 0px; margin-bottom: 1em; margin-left: 0px; font-size: 10px; color: rgb(124, 124, 124); line-height: 12px; font-family: arial, helvetica, sans-serif; padding: 0px; text-align: center; background-color: transparent;">&nbsp;</p><p align="center" style="margin-right: 0px; margin-bottom: 1em; margin-left: 0px; font-size: 10px; color: rgb(124, 124, 124); line-height: 12px; font-family: arial, helvetica, sans-serif; padding: 0px; text-align: center; background-color: transparent;">



.

Soft-Fire . All rights reserved 2020

©</p></th></tr></tbody></table></th></tr></tbody></table></th></tr></tbody></table>';
      if($gs->is_smtp == 1)
      {
          $data = [
                  'to' => $request->email,
                  'subject' => $subject,
                  'body' => $msg,
          ];

          $mailer = new GeniusMailer();
          $mailer->sendCustomMail($data);                
      }
      else
      {
          $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
          mail($request->email,$subject,$msg,$headers);            
      }
      return response()->json(' تم إرسال كلمة المرور الجديدة إلى بريدك الإلكتروني.');
      }
      else{
      // user not found
      return response()->json(array('errors' => [ 0 => 'عذراً , لم يتم العثور على حساب مرتبط بهذا البريد' ]));    
      }  
    }

}
