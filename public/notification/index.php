<?php
$filterArr  = $this->input->get();
$filterArr  = (object) $filterArr;
$controller = $this->router->fetch_class();
$method     = $this->router->fetch_method();
$module     = $this->router->fetch_module();
?>
<input type="hidden" id="filterVal" value='<?php echo json_encode($filterArr); ?>'>
<input type="hidden" id="pageUrl" value='<?php echo base_url() . $module . '/' . strtolower($controller) . '/' . $method; ?>'>


<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Notification</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->
    <!--Filter Section -->

    <div class="white-wrapper">
        <div class="fltr-srch-wrap clearfix">
            <div class="row">
                <div class="col-lg-2 col-sm-2">
                    <div class="display">
                        <select class="selectpicker dispLimit">
                            <option <?php echo ($perPage == 10) ? 'Selected' : '' ?> value="10">Display 10</option>
                            <option <?php echo ($perPage == 20) ? 'Selected' : '' ?> value="20">Display 20</option>
                            <option <?php echo ($perPage == 50) ? 'Selected' : '' ?> value="50">Display 50</option>
                            <option <?php echo ($perPage == 100) ? 'Selected' : '' ?> value="100">Display 100</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="srch-wrap">
                        <button class="srch search-icon" style="cursor:default"></button>
                        <a href="javascript:void(0)"> <span class="srch-close-icon searchCloseBtn"></span></a>
                        <input type="text" value="<?php echo (isset($searchlike) && !empty($searchlike)) ? $searchlike : '' ?>" class="search-box searchlike" placeholder="Search by name" id="searchuser" name="search">
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <div class="top-opt-wrap text-right">
                        <ul>
                            <li>
                                <a href="javascripit:void(0)" title="File Export" class="icon_filter" id="filter-side-wrapper"><img src="public/images/filter.svg"></a>
                            </li>
                            <li>
                                <a href="<?php echo base_url() . 'admin/notification/add'; ?>" title="Filter" id="filter-side-wrapper" class="icon_filter">
                                    <img src="public/images/add.svg">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Filter Section Close-->
    <!--Filter Wrapper-->


    <div class="filter-wrap">
        <div class="filter_hd clearfix">
            <div class="pull-left">
                <h2 class="fltr-heading">Filter </h2>
            </div>
            <div class="pull-right">
                <span class="close flt_cl" data-dismiss="modal">X</span>
            </div>
        </div>
        <div class="inner-filter-wrap">
            <div class="fltr-field-wrap">
                <label class="admin-label">Platform</label>
                <div class="commn-select-wrap">
                    <select class="selectpicker platform">
                        <option value="">Select</option>
                        <option <?php echo ($platform == 1) ? 'Selected' : '' ?> value="1">Android</option>
                        <option <?php echo ($platform == 2) ? 'Selected' : '' ?> value="2">iOS</option>
                    </select>
                </div>
            </div>
            <div class="fltr-field-wrap">
                <label class="admin-label">Push Date</label>
                <div class="inputfield-wrap">
                    <input type="text" value="<?php echo!empty($startDate) ? date('m/d/Y', strtotime($startDate)) : "" ?>" class="form-date_wrap startDate" id="datepicker_1" placeholder="From">
                    <input type="text" value="<?php echo!empty($endDate) ? date('m/d/Y', strtotime($endDate)) : "" ?>" class="form-date_wrap endDate" id="datepicker_2" placeholder="To">
                </div>

            </div>
            <div class="button-wrap text-center">
                <button type="Submit" class="commn-btn cancel resetfilter">Reset</button>
                <button type="reset" class="commn-btn save applyfilter">Filter</button>
            </div>
        </div>
    </div>

    <!--Filter Wrapper Close-->
    <!--Table-->
    <div class="white-wrapper">
        <div class="table-responsive custom-tbl">
            <!--table div-->
            <table id="example" class="list-table table table-striped sortable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="50px">S.No</th>
                        <th>Push Title</th>
                        <th>Platform</th>
                        <th>Gender</th>
                        <th>Sent Count</th>
                        <th>Added On</th>
                        <th width="100px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i          = 1; ?>
                    <?php if (!empty($notiList)) { ?>
                        <?php foreach ($notiList as $list) { ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <?php echo $list['title'] ?>
                                </td>
                                <td><?php echo ($list['platform'] == 1) ? 'All' : (($list['platform'] == 2) ? 'Android' : 'iOS'); ?></td>
                                <td><?php echo ($list['gender'] == 0) ? 'All' : (($list['gender'] == 1) ? 'Male' : 'Female'); ?></td>
                                <td><?php echo $list['total_sents'] ?></td>
                                <td><?php echo date('d-m-Y', strtotime($list['created_at'])) ?></td>
                                <td>
                                    <a href="javascript:void(0);" aria-hidden="true" data-toggle="modal" data-target="#editModal" onclick="$('#notiToken').val('<?php echo $list['id'] ?>')"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>
                                    <a href="javascript:void(0);" class="table_icon"><i class="fa fa-trash" aria-hidden="true" data-toggle="modal" data-target="#myModal-trash"></i></a>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <!-- Pagenation and Display data wrap-->
            <div class="bottom-wrap clearfix">
                <div class="left-column">
                </div>
                <div class="right-column text-right">
                    <div class="pagenation-wrap">
                        <?php echo $links; ?>
                    </div>
                </div>
            </div>
            <!-- Pagination and Display data wrap-->
        </div>
    </div>
    <!-- table 1 close-->
</div>
<!--Edit  Modal Close-->
<div id="editModal" class="modal fade" role="dialog">
    <input type="hidden" id="uid" name="uid" value="">
    <input type="hidden" id="ustatus" name="ustatus" value="">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-alt-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title modal-heading">Resend</h4>
            </div>
            <div class="modal-body">
                <p class="modal-para">Please select the option ?</p>
            </div>
            <input type="hidden" id="notiToken">
            <div class="modal-footer">
                <div class="button-wrap">
                    <button type="button" class="commn-btn resendPush" data-dismiss="modal">Resend Now</button>
                    <button type="button" class="commn-btn editPush" >Edit & Resend</button>
                </div>
            </div>

        </div>
    </div>
    <!--Edit Modal Close-->
    <script>
        var baseUrl = '<?php echo base_url() ?>';
    </script>