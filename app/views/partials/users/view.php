
<?php
$comp_model = new SharedController;

$csrf_token = Csrf :: $token;

//Page Data Information from Controller
$data = $this->view_data;

//$rec_id = $data['__tableprimarykey'];
$page_id = Router::$page_id; //Page id from url

$view_title = $this->view_title;

$show_header = $this->show_header;
$show_edit_btn = $this->show_edit_btn;
$show_delete_btn = $this->show_delete_btn;
$show_export_btn = $this->show_export_btn;

?>

<section class="page">
    
    <?php
    if( $show_header == true ){
    ?>
    
    <div  class="bg-light p-3 mb-3">
        <div class="container">
            
            <div class="row ">
                
                <div class="col-12 ">
                    <h3 class="record-title">View  Users</h3>
                    
                </div>
                
            </div>
        </div>
    </div>
    
    <?php
    }
    ?>
    
    <div  class="">
        <div class="container">
            
            <div class="row ">
                
                <div class="col-md-12 comp-grid">
                    
                    <?php $this :: display_page_errors(); ?>
                    
                    <div  class=" animated fadeIn">
                        <div class="profile-bg mb-2">
                            <div class="profile">
                                <div class="d-flex flex-row justify-content-center">
                                    <div class="avatar">
                                        <?php 
                                        if(!empty($data['user_dp'])){
                                        Html::page_img($data['user_dp'],100,100); 
                                        }
                                        ?>
                                    </div>
                                    <h2 class="title"><?php echo $data['user_name']; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div>
                            <table class="table table-hover table-borderless table-striped">
                                <tbody>
                                    
                                    <tr>
                                        <th class="title"> Id :</th>
                                        <td class="value"> <?php echo $data['id']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> User Name :</th>
                                        <td class="value"> <?php echo $data['user_name']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Phone :</th>
                                        <td class="value"> <?php echo $data['phone']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Date :</th>
                                        <td class="value"> <?php echo $data['date']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Email :</th>
                                        <td class="value"> <?php echo $data['email']; ?> </td>
                                    </tr>
                                    
                                    
                                </tbody>    
                            </table>    
                        </div>  
                        <div class="mt-2">
                            
                            <a class="btn btn-sm btn-info"  href="<?php print_link("users/edit/$rec_id"); ?>">
                                <i class="fa fa-edit"></i> 
                            </a>
                            
                            
                            <a class="btn btn-sm btn-danger record-delete-btn"  href="<?php print_link("users/delete/$rec_id/?csrf_token=$csrf_token"); ?>" data-prompt-msg="" data-display-style="none">
                                <i class="fa fa-times"></i> 
                            </a>
                            
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>
    
</section>
