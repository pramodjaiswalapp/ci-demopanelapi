<?php
 $filterArr  = $this->input->get();
 $filterArr  = ( object ) $filterArr;
 $controller = $this->router->fetch_class();
 $method     = $this->router->fetch_method();
 $module     = $this->router->fetch_module();
?>
<input type="hidden" name="<?php echo $csrfName; ?>"  id="<?php echo $csrfName; ?>" value="<?php echo $csrfToken; ?>">
<link href="<?php echo base_url() ?>public/css/datepicker.min.css" rel='stylesheet'>
<input type="hidden" id="filterVal" value='<?php echo json_encode( $filterArr ); ?>'>
<input type="hidden" id="pageUrl" value='<?php echo base_url().$module.'/'.strtolower( $controller ).'/'.$method; ?>'>
<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Post Management</a></li>

        </ol>
    </div>
    <!--breadcrumb wrap close-->
    <!--Filter Section -->
    <div class="fltr-srch-wrap clearfix white-wrapper">
        <div class="row">
            <div class="col-lg-2 col-sm-2">
                <div class="display">
                    <select class="selectpicker dispLimit">
                        <option <?php echo ($limit == 10) ? 'Selected' : '' ?> value="10">Display 10</option>
                        <option <?php echo ($limit == 20) ? 'Selected' : '' ?> value="20">Display 20</option>
                        <option <?php echo ($limit == 50) ? 'Selected' : '' ?> value="50">Display 50</option>
                        <option <?php echo ($limit == 100) ? 'Selected' : '' ?> value="100">Display 100</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <form action="">
                    <div class="srch-wrap">
                        <button class="srch search-icon" style="cursor:default"></button>
                        <a href="javascript:void(0)"> <span class="srch-close-icon searchCloseBtn"></span></a>
                        <input type="text" value="<?php echo (isset( $searchlike ) && !empty( $searchlike )) ? $searchlike : '' ?>" class="search-box searchlike" placeholder="Search by version name" id="searchuser" name="search">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Filter Section Close-->
    <div class="row">
        <div class="col-lg-6">Total Content: <?php echo $totalrows ?></div>
    </div>
    <!--Table-->
    <label id="error">
        <?php $alertMsg   = $this->session->flashdata( 'alertMsg' ); ?>
        <div class="alert alert-success" <?php echo (!(isset( $alertMsg ) && !empty( $alertMsg ))) ? "style='display:none'" : "" ?> role="alert">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            <strong>
                <span class="alertType"><?php echo (isset( $alertMsg ) && !empty( $alertMsg )) ? $alertMsg['type'] : "" ?></span>
            </strong>
            <span class="alertText"><?php echo (isset( $alertMsg ) && !empty( $alertMsg )) ? $alertMsg['text'] : "" ?></span>
        </div>
    </label>
    <div class="clearfix white-wrapper">
        <div class="table-responsive custom-tbl">
            <!--table div-->
            <table id="example" class="list-table table table-striped sortable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="50px">S.No</th>
                        <!-- <th>Post Title</th> -->
                        <th>Description</th>
                        <th>Created Date</th>
                        <?php if ( $accesspermission['deletep'] || $accesspermission['editp'] ) { ?>
                             <th width="70px" class="text-center" >Action</th>
                             <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                     if ( $page > 1 ) {
                         $i = (($page * $limit) - $limit) + 1;
                     }
                     else {
                         $i = 1;
                     }
                     foreach ( $postlist as $key => $value ) {
                         echo "<tr>";
                         echo "<td>".$i++."</td>";
                         echo "<td>".base64_decode( $value['post_description'] )."</td>";
                         echo "<td>".setDate( $value['create_date'] )."</td>";
                         echo "<td>";
                         echo '<a href="'.base_url().'admin/posts/postDetails" class="table_icon"> <i class="fa fa-eye" aria-hidden="true"></i></a>';
                         echo '<a href="javascript:void(0);" class="table_icon"><button data-id='.queryStringBuilder( ['id' => $value['post_id']] ).' class="dynamic"><i class="fa fa-film" aria-hidden="true"></i></button></a>';
                         echo '<a href="" class="table_icon"><i class="fa fa-trash" aria-hidden="true" onclick=""></i></a>';
                         echo "</td>";
                         echo "</tr>";
                     }
                    ?>

                </tbody>
            </table>

        </div>
    </div>
    <!-- table 1 close-->
</div>
<!--Table listing-->

