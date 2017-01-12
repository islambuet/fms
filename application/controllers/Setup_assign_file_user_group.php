<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_assign_file_user_group extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;

    public $class_name_array=array();
    public $class_parent_array=array();
    public $type_name_array=array();
    public $type_parent_array=array();
    public $name_name_array=array();
    public $name_parent_array=array();
    public $selected_array=array();

    public function __construct()
    {
        parent::__construct();
        $this->message='';
        $this->permissions=User_helper::get_permission('Setup_assign_file_user_group');
        $this->controller_url='setup_assign_file_user_group';
    }
    public function index($action='list',$id=0)
    {
        if($action=='list')
        {
            $this->system_list($id);
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=='details')
        {
            $this->system_details($id);
        }
        elseif($action=='get_file_permission_list')
        {
            $this->system_get_file_permission_list($id);
        }
        elseif($action=='edit')
        {
            $this->system_edit($id);
        }
        elseif($action=='save')
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']='List of User Groups to Assign Files';
            $ajax['system_content'][]=array('id'=>$this->config->item('system_div_id'),'html'=>$this->load->view($this->controller_url.'/list',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $ajax['status']=true;
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $this->details_id=$item_id;

            $this->db->select('name');
            $this->db->from($this->config->item('table_system_user_group'));
            $this->db->where('id',$item_id);
            $user_group_name=$this->db->get()->row_array();
            $data['item_id']=$item_id;
            $data['title']='Details File Permissions for ('.$user_group_name['name'].')';

            $ajax['system_content'][]=array('id'=>$this->config->item('system_div_id'),'html'=>$this->load->view($this->controller_url.'/details',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $ajax['status']=true;
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_get_file_permission_list($id)
    {
        $this->db->select('n.*,ctg.name category_name,cls.name class_name,t.name type_name');
        $this->db->from($this->config->item('table_setup_assign_file_user_group').' fug');
        $this->db->join($this->config->item('table_setup_file_name').' n','n.id=fug.id_file');
        $this->db->join($this->config->item('table_setup_file_type').' t','t.id=n.id_type');
        $this->db->join($this->config->item('table_setup_file_class').' cls','cls.id=t.id_class');
        $this->db->join($this->config->item('table_setup_file_category').' ctg','ctg.id=cls.id_category');
        $this->db->where('fug.user_group_id',$id);
        $this->db->where('fug.status',$this->config->item('system_status_active'));
        $this->json_return($this->db->get()->result_array());
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $this->db->select('name');
            $this->db->from($this->config->item('table_system_user_group'));
            $this->db->where('id',$item_id);
            $user_group_name=$this->db->get()->row_array();
            $data['item_id']=$item_id;
            $data['title']='Edit File Permission to ('.$user_group_name['name'].')';

            $this->db->select('id_file');
            $this->db->from($this->config->item('table_setup_assign_file_user_group'));
            $this->db->where('user_group_id',$item_id);
            $this->db->where('status',$this->config->item('system_status_active'));
            $selected_files=$this->db->get()->result_array();
            foreach($selected_files as $sf)
            {
                $this->selected_array[]=$sf['id_file'];
            }

            $this->category_name_array=$this->get_id_name_array($this->get_data('id,name',$this->config->item('table_setup_file_category')));

            $class_array=$this->get_data('id,name,id_category',$this->config->item('table_setup_file_class'));
            $this->class_name_array=$this->get_id_name_array($class_array);
            $this->class_parent_array=$this->get_parent_array($class_array,'id_category');
            unset($class_array);

            $type_array=$this->get_data('id,name,id_class',$this->config->item('table_setup_file_type'));
            $this->type_name_array=$this->get_id_name_array($type_array);
            $this->type_parent_array=$this->get_parent_array($type_array,'id_class');
            unset($type_array);

            $name_array=$this->get_data('id,name,id_type',$this->config->item('table_setup_file_name'));
            $this->name_name_array=$this->get_id_name_array($name_array);
            $this->name_parent_array=$this->get_parent_array($name_array,'id_type');
            unset($name_array);

            $ajax['system_content'][]=array('id'=>$this->config->item('system_div_id'),'html'=>$this->load->view($this->controller_url.'/add_edit',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $ajax['status']=true;
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $id=$this->input->post('id');
        $data=$this->input->post('items');
        if(!is_array($data))
        {
            $data=array();
        }
        $user=User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
                $this->json_return($ajax);
                die();
            }
        }
        if($id==0)
        {
            $ajax['status']=false;
            $ajax['system_message']='You violate your rules.';
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start(); //DB Transaction Handle START

            $this->db->set('status',$this->config->item('system_status_delete'));
            $this->db->where('user_group_id',$id);
            $this->db->update($this->config->item('table_setup_assign_file_user_group'));

            $this->db->select('id_file');
            $this->db->from($this->config->item('table_setup_assign_file_user_group'));
            $this->db->where('user_group_id',$id);
            $permitted_files_for_process=$this->db->get()->result_array();
            #$permitted_files_for_process=Query_helper::get_info($this->config->item('table_setup_assign_file_user_group'),'id_file',array('user_group_id'=>$id),0,0);
            $permitted_files=array();
            foreach($permitted_files_for_process as $pf)
            {
                $permitted_files[]=$pf['id_file'];
            }
            $data_add=array();
            $data_edit=array();
            $data_add['user_group_id']=$id;
            $data_add['user_created']=$user->user_id;
            $data_add['date_created']=time();
            $data_edit['user_updated']=$user->user_id;
            $data_edit['date_updated']=$data_add['date_created'];
            $data_edit['status']=$this->config->item('system_status_active');
            foreach($data as $d=>$v)
            {
                $id_file=substr($d,1);
                if(in_array($id_file,$permitted_files))
                {
                    Query_helper::update($this->config->item('table_setup_assign_file_user_group'),$data_edit,array('user_group_id='.$id,'id_file='.$id_file));
                }
                else
                {
                    $data_add['id_file']=$id_file;
                    Query_helper::add($this->config->item('table_setup_assign_file_user_group'),$data_add);
                }
            }
            $this->db->trans_complete(); //DB Transaction Handle END
            if($this->db->trans_status()===true)
            {
                $this->message=$this->lang->line('MSG_SAVED_SUCCESS');
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['desk_message']=$this->lang->line('MSG_SAVED_FAIL');
                $this->json_return($ajax);
            }
        }
    }
    private function system_get_items()
    {
        $user=User_helper::get_user();
        if($user->user_group==1)
        {
            $items=Query_helper::get_info($this->config->item('table_system_user_group'),array('id','name','status','ordering'),array('status !="'.$this->config->item('system_status_delete').'"'));
        }
        else
        {
            $items=Query_helper::get_info($this->config->item('table_system_user_group'),array('id','name','status','ordering'),array('id !=1','status !="'.$this->config->item('system_status_delete').'"'));
        }
        $this->json_return($items);
    }
    private function get_id_name_array($parent_array)
    {
        $new_array=array();
        foreach($parent_array as $a)
        {
            $new_array[$a['id']]=$a['name'];
        }
        return $new_array;
    }
    private function get_parent_array($parent_array,$column)
    {
        $new_array=array();
        foreach($parent_array as $a)
        {
            $new_array[$a[$column]][]=$a['id'];
        }
        return $new_array;
    }
    private function get_data($select,$table)
    {
        $this->db->select($select);
        $this->db->from($table);
        return $this->db->get()->result_array();
    }
    public function table_cells($cat_id,$cat_name,$class_id,$class_name,$type_id,$type_name,$name_id,$name_name,&$check_array,$selected_array)
    {
        $is_first_category=false;
        $is_first_class=false;
        $is_first_type=false;
        if(isset($check_array['category'][$cat_id]))
        {
            $is_first_category=false;
            $check_array['category'][$cat_id]+=1;
        }
        else
        {
            $is_first_category='_first';
            $check_array['category'][$cat_id]=1;
        }
        if(isset($check_array['class'][$class_id]))
        {
            $is_first_class=false;
            $check_array['class'][$class_id]+=1;
        }
        else
        {
            $is_first_class='_first';
            $check_array['class'][$class_id]=1;
        }
        if(isset($check_array['type'][$type_id]))
        {
            $is_first_type=false;
            $check_array['type'][$type_id]+=1;
        }
        else
        {
            $is_first_type='_first';
            $check_array['type'][$type_id]=1;
        }
        $checked='';
        if(in_array($name_id,$selected_array))
        {
            $checked=' checked';
        }
        echo "
                <tr>
                    <td class='category-$cat_id$is_first_category'>
                    <input id='category-$cat_id' type='checkbox' data-id='$cat_id' data-type='category' class='all category'>
                    <label for='category-$cat_id'>$cat_name</label>
                    </td>
                    <td class='class-$class_id$is_first_class'>
                    <input id='class-$class_id' type='checkbox' data-id='$class_id' data-type='class' class='all class category_$cat_id'>
                    <label for='class-$class_id'>$class_name</label>
                    </td>
                    <td class='type-$type_id$is_first_type'>
                    <input id='type-$type_id' type='checkbox' data-id='$type_id' data-type='type' class='all type category_$cat_id class_$class_id'>
                    <label for='type-$type_id'>$type_name</label>
                    </td>
                    <td>
                    <input id='name-$name_id' type='checkbox' name='items[n$name_id]' class='all name category_$cat_id class_$class_id type_$type_id'$checked>
                    <label for='name-$name_id'>$name_name</label>
                    </td>
                </tr>
             ";
    }
    public function javascript_code_gen($array,$type)
    {
        foreach($array as $id=>$rowspan)
        {
            echo '$(".'.$type.'-'.$id.'").remove();';
            echo '$(".'.$type.'-'.$id.'_first").attr("rowspan","'.$rowspan.'");';
        }
    }
}
