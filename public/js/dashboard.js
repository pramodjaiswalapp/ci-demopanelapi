/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$( function () {
    const domain = window.location.hostname;
    const host = window.location.protocol;
    const URL = host + "//" + domain + "/AdminPanel/admin/dashboard/fetch_data";

    /**
     *
     */
    $( "select[class*='user']" ).on( 'change', function () {
        generate_chart( [ ], [ ] );
        var selected = $( this ).find( "option:selected" ).val();
        hide_all();
        switch ( selected ) {
            case "daily":
                break;
            case "weekly":
                Highcharts.setOptions( { lang: { noData: "Please Select Year & Month" } } );
                set_blank_chart();
                $( '.chart_week' ).val( "" );
                $( ".chart_week" ).css( "display", "block" );
                $( ".chart_month" ).css( "display", "block" );
                $( '#weekyears' ).get( 0 ).selectedIndex = 0;
                $( "#weekyears" ).selectpicker( "refresh" );
                $( '#weekmonth' ).get( 0 ).selectedIndex = 0;
                $( "#weekmonth" ).selectpicker( "refresh" );
                break;
            case "monthly":
                Highcharts.setOptions( { lang: { noData: "Please Select Year to Display Monthly Data" } } );
                set_blank_chart();
                $( ".chart_year" ).css( "display", "block" );
                $( '#years' ).get( 0 ).selectedIndex = 0;
                $( '#years' ).selectpicker( 'refresh' );
                break;
            case "yearly":
                set_blank_chart();
                get_user_yearly_data( "yearly" );
                break;
        }
    } );


    var set_blank_chart = function () {
        generate_chart( [ ], [ ] );
    }

    /**
     * @description Fetch Monthly user registration data according to selected year
     */
    $( "#weekmonth" ).on( 'change', function () {
        var month = $( this ).val();
        var year = $( "#weekyears" ).val();
        var data = {
            type: "weekly",
            month: month,
            csrf_token: csrf_token,
            year: year,
        };
        fetching_data( data );
    } );




    /**
     * @description Fetch Monthly user registration data according to selected year
     */
    $( "#years" ).on( 'change', function () {
        generate_chart( [ ], [ ] );
        var year = $( this ).find( "option:selected" ).val();
        var data = {
            type: "monthly",
            year: year,
            csrf_token: csrf_token
        };
        fetching_data( data );
    } );



    /**
     * @function get_user_yearly_data
     * @description plot chart according to last 10 years data
     * @param {type} type
     * @returns {undefined}
     */
    var get_user_yearly_data = function ( type ) {
        var data = {
            type: type,
            csrf_token: csrf_token
        };
        fetching_data( data );
    };


    /**
     *
     * @param {type} data
     * @returns {undefined}
     */
    var fetching_data = function ( data ) {
        $.ajax( {
            url: URL,
            data: data,
            type: "POST",
            success: function ( res ) {
                var obj = JSON.parse( res );
                if ( obj.status ) {
                    if ( !obj.users.length ) {
                        alert( "Sorry" );
                    }
                    generate_chart( obj.year, obj.users );
                }
            },
            error: function ( res ) {
                alert( "Sorry" );
            }
        } );
    };


    /**
     * @function generate_chart
     * @description funciton generate Charts
     *
     * @param {array} labels labels of X axis
     * @param {array} values array of values to plot on chart
     * @returns {undefined}
     */
    var generate_chart = function ( labels, values ) {
        Highcharts.chart( 'chart1', {

            chart: {
                type: 'spline',
                backgroundColor: 'transparent'
            },

            title: {
                text: false
            },

            xAxis: {
                categories: labels
            },

            yAxis: {
                title: {
                    text: 'No. Of Uses'
                },
                labels: {
                    format: '{value}'
                },
                lineWidth: 2
            },

            credits: {
                enabled: false
            },

            tooltip: {
                formatter: function () {
                    return '<b>' + this.x + '</b><br/>' +
                            'Total: ' + this.y;
                }
            },

            exporting: {
                enabled: false
            },

            series: [ {
                    showInLegend: false,
                    name: '10am - 12pm (Morning)',
                    data: values,
                    color: '#9e0d0d',
                } ],

            responsive: {
                rules: [ {
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    } ]
            }
        } );
    };


    /**
     *@function hide_all
     *@description hide all optional elements
     *@returns {undefined}
     */
    var hide_all = function () {
        $( ".chart_year" ).css( "display", "none" );
        $( ".chart_week" ).css( "display", "none" );
        $( ".chart_month" ).css( "display", "none" );
    };
} );