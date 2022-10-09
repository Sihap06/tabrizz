@extends('admin._layouts.master')

@section('content')
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">{{$shop->name}}</h6>
                </div>
            </div>
            <!-- Card stats -->
            <div class="row">
                <div class="col-4">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Produk</h5>
                                    <span class="h2 font-weight-bold mb-0">{{number_format($temp_product)}}</span>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm">
                                <a href="{{url('produk_toko', $shop_id)}}" class="btn btn-secondary btn-sm">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Pegawai</h5>
                                    <span class="h2 font-weight-bold mb-0">{{number_format($temp_user)}}</span>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm">
                                <a href="{{url('user_toko', $shop_id)}}" class="btn btn-secondary btn-sm">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Transaksi</h5>
                                    <span class="h2 font-weight-bold mb-0">{{number_format($rekap)}}</span>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm">
                                <a href="{{url('rekaptoko', $shop_id)}}" class="btn btn-secondary btn-sm">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-12">
            <div class="card bg-white">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="h3 text-dark mb-0">Total Penjualan (Rp) ({{date('Y')}})</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Chart -->
                    <div class="chart">
                        <!-- Chart wrapper -->
                        <canvas id="chart-total-penjualan" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="h3 mb-0">Total Produk Terjual ({{date('Y')}})</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Chart -->
                    <div class="chart">
                        <canvas id="chart-total-terjual" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @include('admin._layouts.footer')
    
</div>
@endsection
@section('script')


<script>
    
    $.ajax({
        type: "GET",
        url: "{{url('ajax-chart-penjualan-shop')}}" + '/' + "{{$shop->id}}",
        dataType: 'JSON',
        success: function (data) {
            
            var SalesChart = (function() {
                
                var $chart = $('#chart-total-penjualan');
                
                function init($this) {
                    var salesChart = new Chart($this, {
                        type: 'line',
                        options: {
                            scales: {
                                yAxes: [{
                                    gridLines: {
                                        color: Charts.colors.gray[700],
                                        zeroLineColor: Charts.colors.gray[700]
                                    },
                                    ticks: {
                                        
                                    }
                                }]
                            }
                        },
                        data: {
                            labels: data[0],
                            datasets: [{
                                label: 'Total Penjualan (Rp)',
                                data: data[1]
                            }]
                        },
                        options: {
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        return 'Total Penjualan Rp ' +tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                                    }
                                }
                            },
                            
                        }
                    });
                    
                    
                    $this.data('chart', salesChart);
                    
                };
                if ($chart.length) {
                    init($chart);
                }
                
            })();
        }
    });
    
    $.ajax({
        type: "GET",
        url: "{{url('ajax-chart-terjual-shop')}}" + '/' + "{{$shop->id}}",
        dataType: "JSON",
        success: function (data) {
            var BarsChart = (function() {
                
                //
                // Variables
                //
                
                var $chart = $('#chart-total-terjual');
                
                
                //
                // Methods
                //
                
                // Init chart
                function initChart($chart) {
                    
                    // Create chart
                    var ordersChart = new Chart($chart, {
                        type: 'bar',
                        data: {
                            labels: data[0],
                            datasets: [{
                                label: 'Sales',
                                data: data[1]
                            }]
                        },
                        options: {
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        return 'Total Produk Terjual ' +tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                                    }
                                }
                            },
                            
                        }
                    });
                    
                    // Save to jQuery object
                    $chart.data('chart', ordersChart);
                }
                
                
                // Init chart
                if ($chart.length) {
                    initChart($chart);
                }
                
            })();
        }
    })
    
    
</script>
@endsection