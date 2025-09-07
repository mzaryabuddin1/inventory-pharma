<?php $pagename = "dashboard"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>

<link rel="stylesheet" href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="wrapper wrapper-content">
    <!-- KPI Row -->
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-primary float-right">Today</span>
                    <h5>Sales</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_today_sales">0</h1>
                    <small>Sales made today</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-info float-right">Today</span>
                    <h5>Purchases</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_today_purchases">0</h1>
                    <small>Purchases today</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-success float-right">MTD</span>
                    <h5>Sales (MTD)</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_mtd_sales">0</h1>
                    <small>Month-to-date</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-warning float-right">MTD</span>
                    <h5>Purchases (MTD)</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_mtd_purchases">0</h1>
                    <small>Month-to-date</small>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Row 2 -->
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-navy float-right">Live</span>
                    <h5>Receivables</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_receivables">0</h1>
                    <small>Customers owe</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-danger float-right">Live</span>
                    <h5>Payables</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_payables">0</h1>
                    <small>We owe suppliers</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-default float-right">All</span>
                    <h5>Stock Items</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_stock_items">0</h1>
                    <small>Distinct products in stock</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="label label-warning float-right">Alert</span>
                    <h5>Low Stock</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="kpi_low_stock">0</h1>
                    <small>Below threshold</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Sales vs Purchases (Last 12 months)</h5>
                </div>
                <div class="ibox-content">
                    <canvas id="chartSalesPurchases" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Payments Breakdown (6 months)</h5>
                </div>
                <div class="ibox-content">
                    <canvas id="chartPayments" height="120"></canvas>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Top 5 Products (Sales)</h5>
                </div>
                <div class="ibox-content">
                    <canvas id="chartTopProducts" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest activity -->
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Recent Activity</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Purchases</h4>
                            <ul class="list-group" id="listPurchases"></ul>
                        </div>
                        <div class="col-md-4">
                            <h4>Sales</h4>
                            <ul class="list-group" id="listSales"></ul>
                        </div>
                        <div class="col-md-4">
                            <h4>Payments</h4>
                            <ul class="list-group" id="listPayments"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once("layout/footer.php") ?>

<script>
    (function() {
        function n(x) { // number format
            const v = parseFloat(x || 0);
            return v.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function c(x) { // currency-ish
            const v = parseFloat(x || 0);
            return v.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        let spChart, payChart, topChart;

        function loadStats() {
            $.getJSON("<?= base_url() . 'dashboard-stats'; ?>", function(res) {
                if (!res || !res.success) return;

                const d = res.data;

                // KPIs
                $("#kpi_today_sales").text("₨ " + c(d.today_sales));
                $("#kpi_today_purchases").text("₨ " + c(d.today_purchases));
                $("#kpi_mtd_sales").text("₨ " + c(d.mtd_sales));
                $("#kpi_mtd_purchases").text("₨ " + c(d.mtd_purchases));
                $("#kpi_receivables").text("₨ " + c(d.receivables));
                $("#kpi_payables").text("₨ " + c(d.payables));
                $("#kpi_stock_items").text(n(d.stock_items));
                $("#kpi_low_stock").text(n(d.low_stock));

                // Sales vs Purchases
                const l1 = d.sales_vs_purchases_12m.labels || [];
                const s1 = d.sales_vs_purchases_12m.sales || [];
                const p1 = d.sales_vs_purchases_12m.purchases || [];
                const ctx1 = document.getElementById('chartSalesPurchases').getContext('2d');
                if (spChart) spChart.destroy();
                spChart = new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: l1,
                        datasets: [{
                                label: 'Sales',
                                data: s1,
                                fill: false
                            },
                            {
                                label: 'Purchases',
                                data: p1,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: v => '₨ ' + v
                                }
                            }
                        }
                    }
                });

                // Payments breakdown
                const l2 = d.payments_breakdown_6m.labels || [];
                const cs = d.payments_breakdown_6m.customer || [];
                const ss = d.payments_breakdown_6m.supplier || [];
                const ctx2 = document.getElementById('chartPayments').getContext('2d');
                if (payChart) payChart.destroy();
                payChart = new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: l2,
                        datasets: [{
                                label: 'Received (Customers)',
                                data: cs
                            },
                            {
                                label: 'Paid (Suppliers)',
                                data: ss
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: v => '₨ ' + v
                                }
                            }
                        }
                    }
                });

                // Top products
                const l3 = d.top_5_products.labels || [];
                const v3 = d.top_5_products.values || [];
                const ctx3 = document.getElementById('chartTopProducts').getContext('2d');
                if (topChart) topChart.destroy();
                topChart = new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: l3,
                        datasets: [{
                            data: v3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

            });
        }

        function loadLatest() {
            $.getJSON("<?= base_url() . 'dashboard-latest'; ?>", function(res) {
                if (!res || !res.success) return;
                const d = res.data;

                // Purchases
                const $p = $("#listPurchases").empty();
                (d.purchases || []).forEach(x => {
                    $p.append(`<li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div><strong>${x.ref_no}</strong></div>
            <small>${x.purchase_date}</small>
          </div>
          <span>₨ ${Number(x.total_amount || 0).toLocaleString()}</span>
        </li>`);
                });

                // Sales
                const $s = $("#listSales").empty();
                (d.sales || []).forEach(x => {
                    $s.append(`<li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div><strong>${x.invoice_no}</strong></div>
            <small>${x.sale_date}</small>
          </div>
          <span>₨ ${Number(x.total_amount || 0).toLocaleString()}</span>
        </li>`);
                });

                // Payments
                const $pay = $("#listPayments").empty();
                (d.payments || []).forEach(x => {
                    $pay.append(`<li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div><strong>${x.ref_no}</strong> <small class="text-muted">(${x.type}/${x.mode})</small></div>
            <small>${x.payment_date}</small>
          </div>
          <span>₨ ${Number(x.amount || 0).toLocaleString()}</span>
        </li>`);
                });
            });
        }

        // initial load + small refresh button support (optional)
        loadStats();
        loadLatest();
        // setInterval(loadStats, 120000); // refresh every 2 min if you like
    })();
</script>