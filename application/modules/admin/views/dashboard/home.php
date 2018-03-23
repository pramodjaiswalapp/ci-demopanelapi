<link rel="stylesheet" href="public/css/datepicker.min.css" />

<!-- alert -->
<?php if ( null !== $this->session->flashdata( "greetings" ) ) { ?>
     <div class="alert alert-success" role="alert">
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
         </button>
         <h4 class="alert-heading"><?php echo $this->session->flashdata( "greetings" ) ?></h4>
         <p><?php echo $this->session->flashdata( "message" ) ?></p>
     </div>
 <?php } ?>
<!-- //alert -->

<div class="inner-right-panel">

    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Dashboard</li>
        </ol>
    </div>

    <!-- totalwrapper-section-->
    <div class="totalwrapper-section">
        <div class="row">
            <a href="<?php echo base_url().'admin/users' ?>">
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <div class="total-status-wrapper bgcolor1 clearfix">
                        <div class="total-usersicon"><i class="fa fa-users"></i></div>
                        <div class="total-numbers">Users
                            <span class="total-userstxt"><?php echo $userCount ?></span>
                        </div>
                        <div class="total-newusers-status">
                            <p class="total-newusers">New Users</p>
                            <p class="total-userscount"><?php echo $userCount ?></p>
                        </div>
                    </div>
                </div>
            </a>
            <div class="col-lg-3 col-md-3 col-sm-6">
                <div class="total-status-wrapper bgcolor2 clearfix">
                    <div class="total-usersicon"><i class="fa fa-users"></i></div>
                    <div class="total-numbers">Lorem Ipsum
                        <span class="total-userstxt">300</span>
                    </div>
                    <div class="total-newusers-status">
                        <p class="total-newusers">Lorem Ipsum</p>
                        <p class="total-userscount">100</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //totalwrapper-section-->

    <!-- Graph Section -->
    <div class="graph-section">
        <div class="row">

            <div class="col-lg-6">
                <div class="number-ofusers-wrapper">
                    <label class="label-txt">User Data</label>
                    <div class="row">
                        <!-- Select Picker -->
                        <div class="col-lg-4">
                            <div class="form-blk selector">
                                <select placeholder="Select" class="selectpicker form-control user">
                                    <option value="">Select</option>
                                    <!--<option value="daily">Daily</option>-->
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <!-- //Select Picker -->

                        <!-- Year Selector Yearly-->
                        <?php
                         $start = date( 'Y' );
                         $end   = $start - 15;
                        ?>
                        <div class="col-lg-4 chart_year" style="display: none">
                            <div class="form-blk selector">
                                <select class="selectpicker form-control" id="years">
                                    <option>-Select Year-</option>
                                    <?php
                                     for ( $i = $start; $i >= $end; $i-- ) {
                                         echo '<option vlaue='.$i.'>'.$i.'</option>';
                                     }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- Year Selector-->


                        <!-- Month selector-->
                        <div class="col-lg-4 chart_week" style="display: none">
                            <div class="form-blk selector">
                                <select class="selectpicker form-control" id="weekyears">
                                    <option>-Select Year-</option>
                                    <?php
                                     for ( $i = $start; $i >= $end; $i-- ) {
                                         echo '<option vlaue='.$i.'>'.$i.'</option>';
                                     }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 chart_month" style="display: none">
                            <div class="form-blk selector">
                                <select class="selectpicker form-control" id="weekmonth">
                                    <option>-Select Year-</option>
                                    <?php
                                     foreach ( $months as $index => $month ) {
                                         echo '<option vlaue='.$index.'>'.$month.'</option>';
                                     }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- month selector-->

                        <?php if ( 0 ) { ?>
                             <div class="col-lg-4">
                                 <div class="form-blk selector">
                                     <!-- calendar -->
                                     <input readonly="" name="dpd1" data-provide="datepicker" value="" class="form-control startDate" id="dpd1" placeholder="From" type="text">

                                     <label class="ficon ficon-right" for="dpd1"><i class="fa fa-calendar"></i></label>
                                     <!-- //calendar -->
                                 </div>
                             </div>

                             <div class="col-lg-4">
                                 <div class="form-blk selector">
                                     <!-- calendar -->
                                     <input type="text" placeholder="To" id="dpd2" name="dpd2" value="" class="form-control form-field">
                                     <label class="ficon ficon-right" for="dpd2"><i class="fa fa-calendar"></i></label>
                                     <!-- //calendar -->
                                 </div>
                             </div>
                         <?php } ?>
                    </div>
                    <div class="graph">
                        <div id="chart1" style="min-width:250px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="number-ofusers-wrapper">
                    <label class="label-txt">Number of Merchant</label>
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="form-blk selector">
                                <!-- Select Picker -->
                                <select placeholder="Select" class="selectpicker form-control">
                                    <option>Select</option>
                                    <option>Daily</option>
                                    <option>Weekly</option>
                                    <option>Monthly</option>
                                    <option>Yearly</option>
                                </select>
                                <!-- //Select Picker -->
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-blk selector">
                                <!-- calendar -->
                                <input type="text" placeholder="From" id="dpd3" name="dpd3" value="" class="form-control">
                                <label class="ficon ficon-right" for="dpd3"><i class="fa fa-calendar"></i></label>
                                <!-- //calendar -->
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-blk selector">
                                <!-- calendar -->
                                <input type="text" placeholder="To" id="dpd4" name="dpd4" value="" class="form-control">
                                <label class="ficon ficon-right" for="dpd4"><i class="fa fa-calendar"></i></label>
                                <!-- //calendar -->
                            </div>
                        </div>
                    </div>
                    <div class="graph">
                        <div id="chart2" style="min-width:250px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //Graph Section -->

</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
<script src="<?php echo base_url() ?>public/js/dashboard.js"></script>
<script src="<?php echo base_url() ?>public/js/custom-dashboard.js"></script>
<script src="<?php echo base_url() ?>public/js/datepicker.min.js"></script>