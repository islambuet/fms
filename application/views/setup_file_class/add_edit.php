<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data['action_back']=site_url($CI->controller_url);
$action_data['action_save']='#save_form';
$action_data['action_save_new']='#save_form';
$action_data['action_clear']='#save_form';
$CI->load->view('action_buttons',$action_data);
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>">
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="id_category" class="control-label pull-right">
                    <?php echo $CI->lang->line('LABEL_FILE_CATEGORY');?>
                    <span style="color:#FF0000">*</span>
                </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="id_category" name="item[id_category]" class="form-control" tabindex="-1">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($categories as $category)
                    {?>
                        <option value="<?php echo $category['value']?>" <?php if($category['value']==$item['id_category']){ echo 'selected';}?>><?php echo $category['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="name" class="control-label pull-right">
                    <?php echo $this->lang->line('LABEL_NAME');?>
                    <span style="color:#FF0000">*</span>
                </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[name]" id="name" class="form-control" value="<?php echo $item['name'];?>"/>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="ordering" class="control-label pull-right">
                    <?php echo $CI->lang->line('LABEL_ORDER');?>
                    <span style="color:#FF0000">*</span>
                </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[ordering]" id="ordering" class="form-control" value="<?php echo $item['ordering'] ?>" >
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right">
                    Remarks
                    <span style="color:#FF0000">*</span>
                </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control"><?php echo $item['remarks'] ?></textarea>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    jQuery(document).ready(function()
    {
        $(document).off("click", "#id_category");
    });
</script>