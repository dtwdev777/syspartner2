<?php
namespace App\Services;
class IpGenerator {


public static function create_playlist($categories, $package="", $type="m3u8", $token_u="7447eb505c650fb8ab86677fec91e4623bffd6dd" , $parent=true){
    try{
        $token = $token_u;
        $file_types = ['m3u8','m3u', 'ottplayer', 'siptv','ssiptv' ,'ottplayerm'];
        $type_ext="m3u8";
        $title_name = "";
        $token_user  = $token ;
        $name_host  = env("SERVERV3");
      
          $epg="";
          $url_setting = env("SERVERV3");
          $sub_domen  = env("SERVERV3");
        // $host  = parse_url($url_setting ,PHP_URL_HOST);
        // $port  = parse_url($url_setting,PHP_URL_PORT);
        $flussonic_url  = "";
        $iconUrl = "$name_host/wp-content/uploads/icons/";
       

      
        
      
       $content="#EXTM3U url-tvg=\"$epg\"\n";
      
       if($file_types[4] == $type){
        $content="#EXTM3U url-tvg=\"$epg\" m3uautoload=1 cache=500 deinterlace=6 tvg-shift=0\n";
     }
    

     
      
      
          
           foreach($categories as $k => $chanels){
                  // $k =  "utf-8", "windows-1251";
                  //parent controll
                
                  if( ($parent == 'false')  &&  ($k == 'Эротические') ) continue ;
                  $k =  array_keys($chanels)[0];
                 
                   if($file_types[0] == $type){
                    $content.="#EXTGRP: \"$k\" \n";
                    $title_name  = $file_types[0];

                   }
                     //m3u
            elseif($file_types[1] == $type){
                $type_ext= "m3u";
                $title_name  = $file_types[1];
            }
            elseif($file_types[2] == $type){
              
                $title_name  = $file_types[2];
            }
            elseif($file_types[3] == $type){
                $type_ext= "m3u";
                $title_name  = $file_types[3];
            }
            elseif($file_types[4] == $type){                    

                $type_ext= "m3u";
                $title_name  = $file_types[4];
            }
            elseif($file_types[5] == $type){                    

                $type_ext= "m3u";
                $title_name  = $file_types[5];
            }
                  
                   foreach($chanels as $country=> $chanel){

                 
                       // dd($country);
                          // $token_hash = create_token($token_user);
                           $token = "token=".$token_u;
                         //  add_token($token_hash,$token_user);
                          
                           $chanel_title = str_replace('"', '', $chanel->title) ;
                           //m3u8
                            if($file_types[0] == $type){
                            $content .="#EXTINF:-1 tvg-id=\"{$chanel->dataid}\"  tvg-name=\"$chanel_title\", group-title=\"$country\"  , {$chanel_title}\n" ;
                            $content .= "{$chanel->link}/{$chanel->name}/video.m3u8?$token\n";
                            }

                            // m3u
                            elseif($file_types[1] == $type){
                                $content .="#EXTINF:-1 tvg-id=\"{$chanel->dataid}\"  tvg-name=\"$chanel_title\",  {$chanel_title}\n" ;
                                $content .= "{$chanel->link}/{$chanel->name}/mpegts?$token\n";
                               }

                               //ottpplayer
                          //      elseif($file_types[2] == $type){
                               
                          //       $content .="#EXTINF:0  tvg-id=\"{$chanel->dataid}\" tvg-logo=\"$iconUrl{$chanel->channel}.png\"  tvg-rec=\"3\", {$chanel_title}\n" ;
                          //       $content.="#EXTGRP:\"$k\"\n";
                          //       //
                          //       $content .= "$sub_domen/{$chanel->channel}/mpegts?$token&filter=tracks:v1a1\n";
                          //      }
                          //      //siptv
                          //      elseif($file_types[3] == $type){
                          //          if($chanel->package_id  == 21){
                          //           $content .="#EXTINF:-1 tvg-id=\"{$chanel->dataid}\" tvg-logo=\"$iconUrl{$chanel->channel}.png\" group-title=\"$k\" parent-code=\"1234\",{$chanel_title}\n" ;
                          //          }
                          //          else{
                          //           $content .="#EXTINF:-1 tvg-id=\"{$chanel->dataid}\" tvg-logo=\"$iconUrl{$chanel->channel}.png\" group-title=\"$k\" tvg-rec=\"3\" timeshift=\"3\",{$chanel_title}\n" ;
                          //          }
                              
                          //     // $content .="#EXTINF:0 group-title=\"$k\" ,{$chanel_title}\n" ;
                          //       $content .="$sub_domen/{$chanel->channel}/video.m3u8?$token&filter=tracks:v1a1\n";
                          //      }
                          //   //ssiptv       
                          //     elseif($file_types[4] == $type){
                              
                          
                          //    $content .="#EXTINF:0 tvg-id=\"{$chanel->dataid}\" tvg-logo=\"$iconUrl{$chanel->channel}.png\" catchup=\"flussonic\"  tvg-rec=\"3\" timeshift=\"3\" catchup-days=\"3\" group-title=\"$k\",{$chanel_title}\n ";
                          //  //  $content .="#EXTINF:0 group-title=\"$k\"  tvg-rec=\"3\" timeshift=\"3\", {$chanel_title}\n" ;
                          //    $content .= "$flussonic_url/{$chanel->channel}/video.m3u8?$token\n";
                          //   }
                          //   //
                          //      //ottpplayerm http://ott.dugatv.me
                          //      elseif($file_types[5] == $type){
                               
                          //       $content .="#EXTINF:0 tvg-id=\"{$chanel->dataid}\" tvg-logo=\"$iconUrl{$chanel->channel}.png\"   tvg-rec=\"3\", {$chanel_title}\n" ;
                          //       $content.="#EXTGRP:\"$k\"\n";
                          //       //
                          //       $content .= "$sub_domen/{$chanel->channel}/video.m3u8?$token&filter=tracks:v1a1\n";
                          //      }

                            }
                        } 
            
      // dd($content);
      return $content;
     
    }

    catch(\Exception $err){
        echo $err;
    }
}

 



}