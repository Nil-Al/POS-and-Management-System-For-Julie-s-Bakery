<style>
    .glowing-border {
        padding: 1rem;
        background: linear-gradient(145deg, #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #9400d3);
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
        position: relative;
        overflow: hidden;
        animation: glowing 5s linear infinite;
    }

    @keyframes glowing {
        0% { box-shadow: 0 0 10px rgba(255, 0, 0, 0.5), 0 0 20px rgba(255, 0, 0, 0.3), 0 0 30px rgba(255, 0, 0, 0.2); }
        12.5% { box-shadow: 0 0 20px rgba(255, 127, 0, 0.5), 0 0 30px rgba(255, 127, 0, 0.3), 0 0 40px rgba(255, 127, 0, 0.2); }
        25% { box-shadow: 0 0 10px rgba(255, 255, 0, 0.5), 0 0 20px rgba(255, 255, 0, 0.3), 0 0 30px rgba(255, 255, 0, 0.2); }
        37.5% { box-shadow: 0 0 20px rgba(0, 255, 0, 0.5), 0 0 30px rgba(0, 255, 0, 0.3), 0 0 40px rgba(0, 255, 0, 0.2); }
        50% { box-shadow: 0 0 10px rgba(0, 0, 255, 0.5), 0 0 20px rgba(0, 0, 255, 0.3), 0 0 30px rgba(0, 0, 255, 0.2); }
        62.5% { box-shadow: 0 0 20px rgba(75, 0, 130, 0.5), 0 0 30px rgba(75, 0, 130, 0.3), 0 0 40px rgba(75, 0, 130, 0.2); }
        75% { box-shadow: 0 0 10px rgba(148, 0, 211, 0.5), 0 0 20px rgba(148, 0, 211, 0.3), 0 0 30px rgba(148, 0, 211, 0.2); }
        87.5% { box-shadow: 0 0 20px rgba(255, 0, 0, 0.5), 0 0 30px rgba(255, 0, 0, 0.3), 0 0 40px rgba(255, 0, 0, 0.2); }
        100% { box-shadow: 0 0 10px rgba(255, 0, 0, 0.5), 0 0 20px rgba(255, 0, 0, 0.3), 0 0 30px rgba(255, 0, 0, 0.2); }
    }

    .btn-gradient {
    background: linear-gradient(145deg, #000000, #434343);
    color: #ffffff;
    border: none;
    padding: 5px 10px;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background 0.3s ease;
}

.btn-gradient:hover {
    background: linear-gradient(145deg, #222222, #555555);
}

</style>


<div class="content py-3 glowing-border">
    <div class="card rounded-0 shadow">
        <div class="card-body">
        <h3 style="font-family: 'Pacifico', cursive; background: linear-gradient(145deg, #00ffbd, #00bfff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Welcome to Julie's Bakery Shop Management System</h3>
            <hr style="border-top: 1px solid rgba(0,0,0,0.1);">
            <div class="col-12">
                <div class="row gx-3 row-cols-1 row-cols-md-2 row-cols-lg-4">
                    <div class="col mb-4">
                        <div class="card text-dark h-100" style="border-radius: 15px; background-color: #f8f9fa;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="col-auto pe-3">
                                        <span class="fa fa-th-list fs-3 text-primary"></span>
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div class="fs-5"><b>Categories</b></div>
                                        <div class="fs-6 text-end fw-bold">
                                            <?php 
                                            $category = $conn->query("SELECT count(category_id) as `count` FROM `category_list` where delete_flag = 0 ")->fetch_array()['count'];
                                            echo $category > 0 ? format_num($category) : 0 ;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-4">
                        <div class="card text-dark h-100" style="border-radius: 15px; background-color: #f8f9fa;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="col-auto pe-3">
                                        <span class="fas fa-shopping-bag fs-3 text-secondary"></span>
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div class="fs-5"><b>Products</b></div>
                                        <div class="fs-6 text-end fw-bold">
                                            <?php 
                                            $product = $conn->query("SELECT count(product_id) as `count` FROM `product_list` where delete_flag = 0 ")->fetch_array()['count'];
                                            echo $product > 0 ? format_num($product) : 0 ;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-4">
                        <div class="card text-dark h-100" style="border-radius: 15px; background-color: #f8f9fa;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="col-auto pe-3">
                                        <span class="fa fa-file-alt fs-3 text-info"></span>
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div class="fs-5"><b>Total Stocks</b></div>
                                        <div class="fs-6 text-end fw-bold">
                                            <?php 
                                            $stock = 0;
                                            $stock_query = $conn->query("SELECT * FROM `stock_list` where product_id in (SELECT product_id FROM `product_list` where delete_flag = 0) and unix_timestamp(CONCAT(`expiry_date`)) >= unix_timestamp(CURRENT_TIMESTAMP) ");
                                            while($row = $stock_query->fetch_assoc()):
                                                $stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` where unix_timestamp(CONCAT(`expiry_date`, ' 23:59:59')) >= unix_timestamp(CURRENT_TIMESTAMP) and product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                                $stock_out = $conn->query("SELECT sum(quantity) as `total` FROM `transaction_items` where product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                                $stock_in = $stock_in > 0 ? $stock_in : 0;
                                                $stock_out = $stock_out > 0 ? $stock_out : 0;
                                                $qty = $stock_in - $stock_out;
                                                $qty = $qty > 0 ? $qty : 0;
                                                $stock += $qty;
                                            endwhile;
                                            echo $stock > 0 ? format_num($stock) : 0 ;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-4">
                        <div class="card text-dark h-100" style="border-radius: 15px; background-color: #f8f9fa;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="col-auto pe-3">
                                        <span class="fa fa-coins fs-3 text-warning"></span>
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div class="fs-5"><b>Today's Sales</b></div>
                                        <div class="fs-6 text-end fw-bold">
                                            <?php 
                                            $sales = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` where date(date_added) = date(CURRENT_TIMESTAMP) ".(($_SESSION['type'] != 1)? " and user_id = '{$_SESSION['user_id']}' " : ""))->fetch_array()[0];
                                            echo $sales > 0 ? format_num($sales) : 0 ;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="border-top: 1px solid rgba(0,0,0,0.1);">
                <h3 class="mb-4" style="font-family: 'Arial', sans-serif; font-weight: bold; color: #333;">Stock Available</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" id="inventory">
                        <colgroup>
                            <col width="25%">
                            <col width="25%">
                            <col width="25%">
                            <col width="25%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="py-0 px-1">Category</th>
                                <th class="py-0 px-1">Product Code</th>
                                <th class="py-0 px-1">Product Name</th>
                                <th class="py-0 px-1">Available Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.*, c.name as cname FROM `product_list` p inner join `category_list` c on p.category_id = c.category_id where p.status = 1 and p.delete_flag = 0 order by `name` asc";
                            $qry = $conn->query($sql);
                            while($row = $qry->fetch_assoc()):
                                $stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` where unix_timestamp(CONCAT(`expiry_date`, ' 23:59:59')) >= unix_timestamp(CURRENT_TIMESTAMP) and product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                $stock_out = $conn->query("SELECT sum(quantity) as `total` FROM `transaction_items` where product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                $stock_in = $stock_in > 0 ? $stock_in : 0;
                                $stock_out = $stock_out > 0 ? $stock_out : 0;
                                $qty = $stock_in - $stock_out;
                                $qty = $qty > 0 ? $qty : 0;
                            ?>
                                <tr class="<?php echo $qty < 50 ? 'bg-danger bg-opacity-25' : ''; ?>">
                                    <td class="td py-0 px-1"><?php echo $row['cname'] ?></td>
                                    <td class="td py-0 px-1"><?php echo $row['product_code'] ?></td>
                                    <td class="td py-0 px-1"><?php echo $row['name'] ?></td>
                                    <td class="td py-0 px-1 text-end">
                                        <?php if($_SESSION['type'] == 1 && $qty < $row['alert_restock']): ?>
                                            <button type="button" class="btn btn-sm btn-gradient restock" data-pid="<?php echo $row['product_id']; ?>" data-name="<?php echo $row['product_code'].' - '.$row['name']; ?>">
                                                Restock
                                            </button>
                                        <?php endif; ?>
                                        <?php echo $qty ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    $('.restock').click(function(){
        uni_modal('Add New Stock for <span class="text-primary">' + $(this).attr('data-name') + "</span>", "manage_stock.php?pid=" + $(this).attr('data-pid'))
    });
    $('table#inventory').dataTable({
        paging: false,
        searching: false,
        info: false
    });
});
</script>
