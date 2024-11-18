<?php

namespace Icinga\Module\Enrollment;

use Icinga\Application\Config;
use ipl\Html\Html;

class MailHelper
{
    public static function sendMail($user){
        $mail = new Mail();
        $mailFrom = Config::module('enrollment')->getSection('mail')->get('from','noreply@'.gethostname());
        $subject = Config::module('enrollment')->getSection('mail')->get('subject','Icinga User Enrollment');

        $mail->setFrom($mailFrom);
        $mail->setSubject($subject);
        $body = Html::tag('body');
        $instance = Config::module('enrollment')->getSection('icingaweb2')->get('fqdn',gethostname());
        $scheme = Config::module('enrollment')->getSection('icingaweb2')->get('scheme','https');
        $port = Config::module('enrollment')->getSection('icingaweb2')->get('port','');

        $url = \Icinga\Web\Url::fromPath('enrollment/register/user', ['secret' => $user->secret])->setScheme($scheme)->setPort($port)->setHost($instance)->setIsExternal()->getAbsoluteUrl();

        $mailBody = Config::module('enrollment')->getSection('mail')->get('body',"A new User was created for you:\nInstance: %%FQDN%%\nUsername: %%USERNAME%%\nRegistrationUrl:\n%%REGISTRATIONURL%%\n");
        $mailBody=str_replace('%%FQDN%%',$instance,$mailBody);
        $mailBody=str_replace('%%USERNAME%%',$user->name,$mailBody);
        $mailBody=str_replace('%%USERNAME%%',$user->name,$mailBody);
        $parts=explode("\n",$mailBody);

        foreach ($parts as $line){
            if(strpos($line,'%%REGISTRATIONURL%%') !== false){
                $tmp = explode('%%REGISTRATIONURL%%',$line);
                $urlNotAdded=true;
                foreach ($tmp as $partline){
                    $p = Html::tag('p',$partline);
                    $body->add($p);
                    if($urlNotAdded){
                        $a = Html::tag('a',['href'=>$url],$url);
                        $p = Html::tag('p',$a);
                        $body->add($p);
                        $urlNotAdded=false;
                    }
                }

            }else{
                $p = Html::tag('p',$line);
                $body->add($p);
            }

        }

        $mail->send($body,$user->email);
    }
}