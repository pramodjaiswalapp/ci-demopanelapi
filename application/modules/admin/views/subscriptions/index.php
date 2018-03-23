<?php
$filterArr  = $this->input->get();
$filterArr  = (object) $filterArr;
$controller = $this->router->fetch_class();
$method     = $this->router->fetch_method();
$module     = $this->router->fetch_module();
?>

<link href="<?php echo base_url() ?>public/css/bootstrap-datetimepicker.min.css" rel='stylesheet'>

<input type="hidden" id="filterVal" value='<?php echo json_encode($filterArr); ?>'>
<input type="hidden" id="pageUrl" value='<?php echo base_url() . $module . '/' . strtolower($controller) . '/' . $method; ?>'>

<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Subscriptions</li>
        </ol>
    </div>
    <!--Filter Section -->
    <div class="fltr-srch-wrap white-wrapper clearfix">
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
            <div class="col-lg-4 col-sm-4">
                <div class="srch-wrap">
                    <button class="srch search-icon" style="cursor:default"></button>
                    <a href="javascript:void(0)"> <span class="srch-close-icon searchCloseBtn"></span></a>
                    <input type="text" onkeypress="return restrict_special_chars(event);" maxlength="15" value="<?php echo (isset($searchlike) && !empty($searchlike)) ? $searchlike : '' ?>" class="search-box searchlike" placeholder="Search by subscription name" id="searchuser" name="search">
                </div>
            </div>

            <div class="col-lg-6 col-sm-6">
                <div class="top-opt-wrap text-right">
                    <ul>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#subcribe-modal" title="Add Content" class="icon_filter">
                                <img  src="<?php echo base_url() ?>public/images/add.svg">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--Filter Section Close-->
    <div class="row">
        <div class="col-lg-6">Total Subscription: <?php echo $totalrows ?></div>
    </div>
    <!--Table-->
    <label id="error">
        <?php
        $alertMsg   = $this->session->flashdata('alertMsg');
        ?>
        <div class="alert alert-success" <?php echo (!(isset($alertMsg) && !empty($alertMsg))) ? "style='display:none'" : "" ?> role="alert">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            <strong>
                <span class="alertType"><?php echo (isset($alertMsg) && !empty($alertMsg)) ? $alertMsg['type'] : "" ?></span>
            </strong>
            <span class="alertText" style="display:block;"><?php echo (isset($alertMsg) && !empty($alertMsg)) ? $alertMsg['text'] : "" ?></span>
        </div>
    </label>
    <!-- Content -->
    <input type="hidden" id="filterparams" value='<?php echo json_encode($filterArr); ?>'>
    <section class="content-wrapper clearfix">

        <div class="col-lg-6 col-sm-6 hide-mobile"></div>
        <div class="clear"></div>
        <!-- Content section -->
        <div class="white-wrapper p-md">
            <div class="table-responsive custom-tbl">
                <table class="table outlet-table table-striped m-n">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th style="cursor:pointer">Name<a  href="javascript:void(0);" class="th-icon"><i class=""></i></a></th>
                            <th>Price</th>
                            <th>Status </th>
                            <th>Subscription Type</th>
                            <th style="cursor:pointer">Added On<a  href="javascript:void(0);" class="th-icon"><i class=""></i></a> </th>

                            <th width='80px' class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pagecount  = 10;
                        $i          = (($page * $pagecount) - $pagecount) + 1;
                        ?>
                        <?php
                        if (!empty($records)) {
                            foreach ($records as $key => $data) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td class="text-nowrap" align="left">
                                        <a href="admin/Subscriptions/view?data=<?php echo queryStringBuilder("id=" . $data['subscription_id']); ?>">
                                            <?php echo $data['subscription_name'] ?>
                                        </a>
                                    </td>

                                    <?php echo '<td class="text-nowrap" align="left">$' . $data['price'] . '</td>' ?>
                                    <td>
                                        <?php
                                        $status = ($data['status'] == 1) ? 'Active' : 'Blocked';
                                        $status = ($data['status'] == 3) ? 'Deleted' : $status;
                                        echo $status;
                                        ?>
                                    </td>
                                    <td align="left">
                                    <?php echo (isset($subscription_type_arr[$data['subs_recurring']])) ? $subscription_type_arr[$data['subs_recurring']] : "N/A" ?>
                                    </td>

        <?php echo '<td align="left">' . date('d-M-Y', strtotime($data['create_date'])) . '</td>'; ?>

                                    <td width='80px'  class="text-center" >

                                        <?php
                                        switch ($data['status']) {
                                            case ACTIVE:
                                                ?>
                                                <a class="table_icon" title="Edit" href="javascript:void(0);"><i class="fa fa-pencil-square-o" onclick="edit_subscription('<?php echo $data['subscription_name'] ?>', '<?php echo $data['price'] ?>', '<?php echo encryptDecrypt($data['subscription_id']); ?>', '<?php echo $data['description']; ?>', '<?php echo $data['subs_recurring']; ?>');" aria-hidden="true"></i></a>
                                                <a href="javascript:void(0);" id="block_<?php echo $data['subscription_id']; ?>" class="table_icon" title="Block"><i class="fa fa-ban" aria-hidden="true" onclick="blockUser('subscriptions', 2, '<?php echo encryptDecrypt($data['subscription_id']); ?>', 'req/change-user-status', 'Do you really want to block this Subscription?', 'Block');"></i></a>
                                                <a href="javascript:void(0);" class="table_icon" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="deleteUser('subscriptions', 3, '<?php echo encryptDecrypt($data['subscription_id']); ?>', 'req/change-user-status', 'Do you really want to delete this Subscription?');"></i></a>
                                                <?php
                                                break;

                                            case BLOCKED:
                                                ?>
                                                <a href = "javascript:void(0);" id = "unblock_<?php echo $data['subscription_id']; ?>" class = "table_icon"><i class = "fa fa-unlock" aria-hidden = "true" onclick = "blockUser('subscriptions', 1, '<?php echo encryptDecrypt($data['subscription_id']); ?>', 'req/change-user-status', 'Do you really want to unblock this Subscription?', 'Unblock');"></i></a>

                                                <?php
                                                break;

                                            default:
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="9" class="text-center">No subscriptions exist</td>
                            </tr>
<?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="clear"></div>
            <nav class="pagination-wrapper m-t-md">
                <ul class="pagination">
                    <?php
                    if (isset($links)) {
                        echo $links;
                    }
                    ?>
                </ul>
            </nav>
        </div>
        <!-- Content section End -->
    </section>
</div>

<?php
//Add subscription modal
$this->load->view('/subscriptions/add_subscription_modal');

//Edit subscription modal
$this->load->view('/subscriptions/edit_subscription_modal');

//Block and Delete modal
$this->load->view('/subscriptions/block_delete_modal');
?>
