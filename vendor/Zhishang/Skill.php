<?php
namespace Zhishang;

class Skill
{
    public function channel()
    {
        global $db, $request, $comId;
        
        $parentId = (int)$request['channel_id'];
        $language = (int)$request['language'];
        $channels = $db->get_results("select id,title,en_title from demo_skill_channel where comId=$comId and is_hot = 1 order by ordering desc");
        foreach ($channels as &$channel) {
            $skills = $db->get_results("select id skill_id, title from demo_skill where channelId = $channel->id and is_del = 0 and status = 1 and language = $language order by ordering desc ");
            $channel->skill = $skills;
        }
       
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = empty($channels) ? array() : $channels;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function info()
    {
        global $db, $request, $comId;
        
        $id = (int)$request['skill_id'];
        $info = $db->get_row("select * from demo_skill where id = $id and is_del = 0 and status = 1");
        if(!$info){
            return '{"code":"0","message":"未获取到服务信息，请核实"}';
        }
        $fileInfo = [];
        if($info->file_info){
            $files = json_decode($info->file_info, true);
            foreach ($files as $name => $downUrl){
                $temp = array(
                    'name' => $name,
                    'down_url' => $downUrl
                );
                
                $fileInfo[] = $temp;
            }
        }
        $info->file_info = $fileInfo;
        $process = $db->get_results("select id process_id, title, jianjie,originalPic from demo_skill_process where skillId = $id and is_del = 0 and type = 0 and status = 1 order by ordering desc ");
        $fangan = $db->get_results("select id fangan_id, title, jianjie from demo_skill_process where skillId = $id and is_del = 0 and type = 1 and status = 1 order by ordering desc ");
        $anli = $db->get_results("select id anli_id, title, subtitle, originalPic, content from demo_skill_process where skillId = $id and is_del = 0 and type = 2 and status = 1 order by ordering desc ");
        
        $info->process = $process;
        $info->fangan = $fangan;
        $info->anli = $anli;
        
        $info->content = str_replace('src="https://beiyinlai.67.zhishangez.cn/','src="http://'.$_SERVER['HTTP_HOST']."/",$info->content);
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $info;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function consult()
    {
        global $db,$request,$comId;

		if($request['name']==''||$request['phone']==''||$request['email']==''||$request['content']==''){
			return '{"code":0,"message":"所有字段不能有空值！"}';
		}
		
		$accesskeys = ['name', 'email', 'phone', 'address', 'content', 'institution', 'content', 'file_info', 'skill_id'];
		
		$consult = array();
		$consult['dtTime'] = date("Y-m-d H:i:s");
		foreach ($accesskeys as $val){
		    $consult[$val] = $request[$val];
		}
		
		$db->insert_update('demo_skill_consult', $consult,'id');
		
		return '{"code":1,"message":"提交成功！"}';
    }
    
}