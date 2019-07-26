<?php


namespace LTBackup\Extension\Tools\Form\Field;


use Encore\Admin\Admin;
use Encore\Admin\Form\Field\Number;
use Encore\Admin\Form\Field\Text;
use Illuminate\Contracts\Support\Arrayable;
use LTBackup\Extension\Tools\Form\Builder;
use LTBackup\Extension\Tools\Form\Field;
use LTBackup\Extension\Tools\Form\Form;


class IButton extends Field
{
    protected $inline = true;
    protected $lastWithInput = [];
    protected $id;

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }
        $this->id = 'id_'.mt_rand(100000,999999);
        $this->options = (array) $options;
        return $this;
    }



    /**
     * Set options.
     *
     * @param array|callable|string $values
     *
     * @return $this
     */
    public function values($values)
    {
        return $this->options($values);
    }


    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $url = $this->options()->value;
        $token = csrf_token();
        $this->script = <<<EOT
        $('{$this->getElementClassSelector()}').on('click',function(){
             var address,port,username,password;
             address = $('.ltbackup_ftp_address').val().trim();
             port =  $('.ltbackup_ftp_port').val();
              username =  $('.ltbackup_ftp_username').val().trim();
              password =  $('.ltbackup_ftp_password').val().trim();
             if(address.length == 0 || port == 0 || username.length == 0 || password.length == 0){
                 var parent =  $(this).parent().parent().parent().parent().siblings('.box-footer')
                 parent.find('button[type="submit"]').click();
             }else{
                var index = layer.load(2, {shade: false}); //0代表加载的风格，支持0-2
                $.ajax({
                    url:"/admin/{$url}",
                    dataType:"json",
                    async:true,
                    data:{"address":address,"port":port,"username":username,"password":password,"_token":"{$token}"},
                    type:"POST",
                    success:function(req){
                          layer.close(index)
                          layer.msg(req.message);
                        if(req.code == 200){
                           
                        }
                     },
                    error:function(){
                        layer.close(index)
                    }

                });  
             }
                
        });

EOT;
        Admin::script($this->script);

        return '<div class="form-group"> <label  class="col-sm-2  control-label"></label><div class="col-sm-8"><a href="javascript:void(0);" class="btn btn-primary '.$this->options()->column.'"> '.$this->options()->label.'</a></div></div>';

    }


}