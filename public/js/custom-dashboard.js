$( function () {

    // =========== Chart ===========
    Highcharts.chart( 'chart1', {

        chart: {
            type: 'spline',
            backgroundColor: 'transparent'
        },

        title: {
            text: false
        },

        xAxis: {
            categories: [ ]
        },

        yAxis: {
            title: {
                text: 'Temperature'
            },
            labels: {
                format: '{value}°'
            },
            lineWidth: 2
        },

        credits: {
            enabled: false
        },

        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '<br/>' +
                        'Total: ' + this.point.stackTotal;
            }
        },

        exporting: {
            enabled: false
        },

        series: [ {
                showInLegend: false,
                name: '10am - 12pm (Morning)',
                data: [ ],
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

    // =========== Chart ===========
    Highcharts.chart( 'chart2', {

        chart: {
            type: 'line',
            backgroundColor: 'transparent'
        },

        title: {
            text: false
        },

        xAxis: {
            categories: [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ]
        },

        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: false
            }
        },

        credits: {
            enabled: false
        },

        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '<br/>' +
                        'Total: ' + this.point.stackTotal;
            }
        },

        exporting: {
            enabled: false
        },

        series: [ {
                // showInLegend: false,
                name: '10am - 12pm (Morning)',
                data: [ 2000, 8000, 8000, 800, 1000, 3000, 2500 ],
                color: '#53c4ce',
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

} );

$( document ).ready( function () {

    // Date Picker
    var nowTemp = new Date();
    var now = new Date( nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0 );

    // =============== Linked Datepicker =============== //
    var add_start = $( '#dpd1' ).datepicker( {
        format: 'dd/mm/yyyy',
        todayHighlight: 'TRUE',
        autoclose: true,
        onRender: function ( date ) {
            return date.valueOf() > now.valueOf() ? 'disabled' : '';
        }
    } ).on( 'changeDate', function ( ev ) {
        if ( ev.date.valueOf() < add_end.date.valueOf() ) {
            var newDate = new Date( ev.date )
            newDate.setDate( newDate.getDate() );
            add_end.setValue( newDate );
            add_start.hide();
        }
        add_start.hide();
        $( '#dpd2' )[0].focus();
    } ).data( 'datepicker' );

    var add_end = $( '#dpd2' ).datepicker( {
        format: 'dd/mm/yyyy',
        todayHighlight: 'TRUE',
        autoclose: true,
        onRender: function ( date ) {
            return date.valueOf() < add_start.date.valueOf() ? 'disabled' : '';
        }
    } ).on( 'changeDate', function ( ev ) {
        add_end.hide();
    } ).data( 'datepicker' );

    // =============== Linked Datepicker =============== //
    var add_start = $( '#dpd3' ).datepicker( {
        format: 'dd/mm/yyyy',
        todayHighlight: 'TRUE',
        autoclose: true,
        onRender: function ( date ) {
            return date.valueOf() > now.valueOf() ? 'disabled' : '';
        }
    } ).on( 'changeDate', function ( ev ) {
        if ( ev.date.valueOf() < add_end.date.valueOf() ) {
            var newDate = new Date( ev.date )
            newDate.setDate( newDate.getDate() );
            add_end.setValue( newDate );
            add_start.hide();
        }
        add_start.hide();
        $( '#dpd4' )[0].focus();
    } ).data( 'datepicker' );

    var add_end = $( '#dpd4' ).datepicker( {
        format: 'dd/mm/yyyy',
        todayHighlight: 'TRUE',
        autoclose: true,
        onRender: function ( date ) {
            return date.valueOf() < add_start.date.valueOf() ? 'disabled' : '';
        }
    } ).on( 'changeDate', function ( ev ) {
        add_end.hide();
    } ).data( 'datepicker' );

});