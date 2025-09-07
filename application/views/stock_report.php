<?php $pagetab = "report"; ?>
<?php $pagename = "stock_report"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Stock Report</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    <!-- Filters -->
                    <form id="filters" class="mb-3">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label>Product</label>
                                <select class="form-control" name="product_id">
                                    <option value="">All</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['product_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Batch No</label>
                                <input type="text" class="form-control" name="batch_no" placeholder="Batch">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Qty Min</label>
                                <input type="number" class="form-control" name="qty_min" placeholder="0">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Qty Max</label>
                                <input type="number" class="form-control" name="qty_max" placeholder="e.g. 100">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Only In-stock</label><br>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="only_instock" value="1"> Show qty &gt; 0
                                </label>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3 mb-2">
                                <label>Date From</label>
                                <input type="date" class="form-control" name="date_from">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Date To</label>
                                <input type="date" class="form-control" name="date_to">
                            </div>
                            <div class="col-md-6 d-flex align-items-end mb-2">
                                <button type="button" id="btnFilter" class="btn btn-primary mr-2">Apply</button>
                                <button type="button" id="btnReset" class="btn btn-white">Reset</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="stockTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Batch No</th>
                                    <th>Qty</th>
                                    <th>Last Cost</th>
                                    <th>Value</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Totals:</th>
                                    <th id="t_qty">0</th>
                                    <th></th>
                                    <th id="t_value">0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
    let stockDT;

    function currentFilters() {
        const f = $('#filters').serializeArray();
        const data = {};
        for (const {
                name,
                value
            }
            of f) data[name] = value;
        // checkbox (only_instock) fix:
        if (!$('input[name="only_instock"]').is(':checked')) data.only_instock = 0;
        return data;
    }

    $(function() {
        stockDT = $('#stockTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            responsive: true,
            order: [
                [5, 'desc']
            ],
            ajax: {
                url: "<?= base_url() . 'stock-report-data'; ?>",
                type: "POST",
                data: function(d) {
                    // attach filters
                    return Object.assign(d, currentFilters());
                }
            },
            columns: [{
                    data: 'product_name',
                    title: 'Product'
                },
                {
                    data: 'batch_no',
                    title: 'Batch No'
                },
                {
                    data: 'qty',
                    title: 'Qty',
                    className: 'text-right'
                },
                {
                    data: 'last_cost',
                    title: 'Last Cost',
                    className: 'text-right',
                    render: d => d ? parseFloat(d).toFixed(2) : '0.00'
                },
                {
                    data: 'value',
                    title: 'Value',
                    className: 'text-right',
                    render: d => parseFloat(d).toFixed(2)
                },
                {
                    data: 'updated_at',
                    title: 'Updated'
                }
            ],
            drawCallback: function(settings) {
                // compute totals for current page
                let qtyTotal = 0,
                    valTotal = 0;
                const api = this.api();
                api.rows({
                    page: 'current'
                }).every(function() {
                    const r = this.data();
                    qtyTotal += parseFloat(r.qty || 0);
                    valTotal += parseFloat(r.value || 0);
                });
                $('#t_qty').text(qtyTotal);
                $('#t_value').text(valTotal.toFixed(2));
            },
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [{
                    extend: "copy"
                },
                {
                    extend: "csv",
                    title: "StockReport"
                },
                {
                    extend: "excel",
                    title: "StockReport"
                },
                {
                    extend: "pdf",
                    title: "StockReport"
                },
                {
                    extend: "print",
                    customize: function(win) {
                        $(win.document.body).addClass("white-bg").css("font-size", "10px");
                        $(win.document.body).find("table").addClass("compact").css("font-size", "inherit");
                    }
                }
            ],
            searchDelay: 500
        });

        $('#btnFilter').on('click', function() {
            stockDT.ajax.reload();
        });

        $('#btnReset').on('click', function() {
            $('#filters')[0].reset();
            stockDT.ajax.reload();
        });
    });
</script>