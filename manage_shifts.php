<?php
require_once("DBConnection.php");

// Fetch shift details if editing an existing shift
if(isset($_GET['shift_id'])){
    $shift_id = $_GET['shift_id'];
    $qry = $conn->query("SELECT * FROM `cashier_shifts` WHERE shift_id = '$shift_id'");
    if($qry->num_rows > 0) {
        $shift = $qry->fetch_assoc();
        $cashier_id = $shift['cashier_id'];
        $starting_cash = $shift['starting_cash'];
        $ending_cash = $shift['ending_cash'];
        $starting_inventory = $shift['starting_inventory'];
        $ending_inventory = $shift['ending_inventory'];
        $sales = $shift['sales'];
        $shift_date = $shift['shift_date'];
        $time_in = $shift['time_in'];
        $time_out = $shift['time_out'];
        $time_in = !empty($time_in) ? date('Y-m-d H:i:s', strtotime($time_in)) : null;
        $time_out = !empty($time_out) ? date('Y-m-d H:i:s', strtotime($time_out)) : null;
        $notes = $shift['notes'];
    } else {
        // Shift not found
        // You can handle this case, like redirecting or showing an error message
    }
}

// Date range for filtering
$dfrom = isset($_GET['date_from']) ? $_GET['date_from'] : date("Y-m-d", strtotime(date("Y-m-d") . " -1 week"));
$dto = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-d");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <form action="" id="shift-form">
                <input type="hidden" name="shift_id" value="<?php echo isset($shift_id) ? $shift_id : '' ?>">
                <div class="form-group">
                    <label for="cashier_id" class="control-label">Cashier ID</label>
                    <select name="cashier_id" id="cashier_id" class="form-select form-select-sm rounded-0" required>
                        <option <?php echo (!isset($cashier_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                        <?php
                        $cashier_qry = $conn->query("SELECT * FROM `user_list` WHERE `type` = 0 ORDER BY `fullname` ASC");
                        while($row = $cashier_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $row['user_id'] ?>" <?php echo (isset($cashier_id) && $cashier_id == $row['user_id']) ? 'selected' : '' ?>><?php echo $row['fullname'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="starting_cash" class="control-label">Starting Cash</label>
                    <input type="number" step="any" name="starting_cash" id="starting_cash" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($starting_cash) ? $starting_cash : '' ?>">
                </div>
                <div class="form-group">
                    <label for="starting_inventory" class="control-label">Starting Inventory</label>
                    <input type="number" step="any" name="starting_inventory" id="starting_inventory" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($starting_inventory) ? $starting_inventory : '' ?>">
                </div>
                <div class="form-group">
                    <label for="shift_date" class="control-label">Shift Date</label>
                    <input type="date" name="shift_date" id="shift_date" required class="form-control form-control-sm rounded-0" value="<?php echo isset($shift_date) ? $shift_date : '' ?>">
                </div>
                <div class="form-group">
                    <label for="time_in" class="control-label">Time In</label>
                    <input type="time" name="time_in" id="time_in" class="form-control form-control-sm rounded-0" value="<?php echo isset($time_in) ? date('H:i', strtotime($time_in)) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="notes" class="control-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control form-control-sm rounded-0" rows="5"><?php echo isset($notes) ? $notes : '' ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary rounded-0"><?php echo isset($shift_id) ? 'Update Shift' : 'Add Shift' ?></button>
                    <?php if(isset($shift_id)): ?>
                        <a href="?delete_shift=<?php echo $shift_id ?>" class="btn btn-danger rounded-0" onclick="return confirm('Are you sure you want to delete this shift?')">Delete Shift</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="col-md-8">
            <div class="card rounded-0 shadow">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Shifts</h3>
                </div>
                <div class="form-group col-md-4 d-flex">
                <!-- <div class="col-auto">
                    <button class="btn btn-primary rounded-0" id="filter" type="button"><i class="fa fa-filter"></i> Filter</button>
                    <button class="btn btn-success rounded-0" id="print-button" onclick="printTable()" type="button"><i class="fa fa-print" ></i> Print</button>
                </div> -->
            </div>
                <div class="card-body">
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-striped table-bordered">
                            <colgroup>
                                <col width="5%">
                                <col width="12%">
                                <col width="10%">
                                <col width="8%">
                                <col width="8%">
                                <col width="8%">
                                <col width="8%">
                                <col width="8%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center p-0">#</th>
                                    <th class="text-center p-0">Date</th>
                                    <th class="text-center p-0">Cashier</th>
                                    <th class="text-center p-0">Starting Cash</th>
                                    <th class="text-center p-0">Ending Cash</th>
                                    <th class="text-center p-0">Starting Inventory</th>
                                    <th class="text-center p-0">Ending Inventory</th>
                                    <th class="text-center p-0">Time In</th>
                                    <th class="text-center p-0">Time Out</th>
                                    <th class="text-center p-0">Total Sales</th>
                                    <th class="text-center p-0">Notes</th>
                                    <th class="text-center p-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "SELECT cs.*, u.fullname as cashier 
                                        FROM `cashier_shifts` cs 
                                        INNER JOIN `user_list` u ON cs.cashier_id = u.user_id 
                                        WHERE date(cs.shift_date) BETWEEN '{$dfrom}' AND '{$dto}' 
                                        ORDER BY cs.shift_date ASC";
                                $qry = $conn->query($sql);
                                $i = 1;
                                while($row = $qry->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center p-0"><?php echo $i++; ?></td>
                                        <td class="py-0 px-1"><?php echo date("Y-m-d", strtotime($row['shift_date'])) ?></td>
                                        <td class="py-0 px-1"><?php echo $row['cashier'] ?></td>
                                        <td class="py-0 px-1 text-end"><?php echo number_format($row['starting_cash'], 2) ?></td>
                                        <td class="py-0 px-1 text-end"><?php echo number_format($row['ending_cash'], 2) ?></td>
                                        <td class="py-0 px-1 text-end"><?php echo number_format($row['starting_inventory']) ?></td>
                                        <td class="py-0 px-1 text-end"><?php echo number_format($row['ending_inventory']) ?></td>
                                        <td class="py-0 px-1"><?php echo $row['time_in'] ?></td>
                                        <td class="py-0 px-1"><?php echo $row['time_out'] ?></td>
                                        <td class="py-0 px-1 text-end"><?php echo number_format($row['sales'], 2) ?></td>
                                        <td class="py-0 px-1"><?php echo $row['notes'] ?></td>
                                        <td class="py-0 px-1 text-center">
                                            <a href="#" class="btn btn-sm btn-primary rounded-0 edit-shift" data-shift-id="<?php echo $row['shift_id']; ?>">Edit</a>
                                            <a href="#" class="text-danger ms-2 delete-shift" data-shift-id="<?php echo $row['shift_id']; ?>">Delete</a>
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
</div>
</div>
<div class="modal fade" id="editShiftModal" tabindex="-1" aria-labelledby="editShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editShiftModalLabel">Edit Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-shift-form">
                    <input type="hidden" name="shift_id">
                    
                    <!-- Existing fields -->
                    <div class="mb-3">
                        <label for="edit-cashier-id" class="form-label">Cashier ID</label>
                        <select name="cashier_id" id="cashier_id" class="form-select form-select-sm rounded-0" required>
                        <option <?php echo (!isset($cashier_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                        <?php
                        $cashier_qry = $conn->query("SELECT * FROM `user_list` WHERE `type` = 0 ORDER BY `fullname` ASC");
                        while($row = $cashier_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $row['user_id'] ?>" <?php echo (isset($cashier_id) && $cashier_id == $row['user_id'] ) ? 'selected' : '' ?>><?php echo $row['fullname'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-starting-cash" class="form-label">Starting Cash</label>
                        <input type="number" class="form-control" id="edit-starting-cash" name="starting_cash" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-starting-inventory" class="form-label">Starting Inventory</label>
                        <input type="number" class="form-control" id="edit-starting-inventory" name="starting_inventory" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-shift-date" class="form-label">Shift Date</label>
                        <input type="date" class="form-control" id="edit-shift-date" name="shift_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-time-in" class="form-label">Time In</label>
                        <input type="time" class="form-control" id="edit-time-in" name="time_in" value="<?php echo isset($time_in) ? date('H:i', strtotime($time_in)) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="edit-time-out" class="form-label">Time Out</label>
                        <input type="time" class="form-control" id="edit-time-out" name="time_out" value="<?php echo isset($time_out) ? date('H:i', strtotime($time_out)) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="edit-notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit-notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-ending-cash" class="form-label">Ending Cash</label>
                        <input type="number" class="form-control" id="edit-ending-cash" name="ending_cash">
                    </div>
                    <div class="mb-3">
                        <label for="edit-ending-inventory" class="form-label">Ending Inventory</label>
                        <input type="number" class="form-control" id="edit-ending-inventory" name="ending_inventory">
                    </div>
                    <div class="mb-3">
                        <label for="edit-sales" class="form-label">Total Sales</label>
                        <input type="number" class="form-control" id="edit-sales" name="sales">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
$(function(){
    $('#shift-form').submit(function(e){
        e.preventDefault();
        $('.pop_msg').remove();
        var _this = $(this);
        var _el = $('<div>').addClass('pop_msg');
        $('#uni_modal button').attr('disabled', true);
        $('#uni_modal button[type="submit"]').text('Submitting form...');

        $.ajax({
            url: './Actions.php?a=save_shift',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function(err) {
                console.log('Error:', err);
                _el.addClass('alert alert-danger');
                _el.text("An error occurred.");
                _this.prepend(_el);
                _el.show('slow');
                $('#uni_modal button').attr('disabled', false);
                $('#uni_modal button[type="submit"]').text('Save');
            },
            success: function(resp) {
                console.log(resp); // Log response for debugging
                if(resp.status == 'success') {
                    _el.addClass('alert alert-success');
                    _el.text(resp.msg);
                    $('#uni_modal').on('hide.bs.modal', function(){
                        location.reload();
                    });
                } else {
                    _el.addClass('alert alert-danger');
                    _el.text(resp.msg);
                }
                _el.hide();
                _this.prepend(_el);
                _el.show('slow');
                $('#uni_modal button').attr('disabled', false);
                $('#uni_modal button[type="submit"]').text('Save');
            }
        });
    });

    $('#filter').click(function(){
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        window.location.href = 'manage_shifts.php?date_from=' + date_from + '&date_to=' + date_to;
    });

    $('#print').click(function(){
        window.print();
    });
    
});
$(function(){
    $('.edit-shift').click(function(e){
    e.preventDefault();
    var shift_id = $(this).data('shift-id');
    $.ajax({
        url: './Actions.php?a=get_shift_details',
        method: 'POST',
        data: { shift_id: shift_id },
        dataType: 'json',
        success: function(response){
            if(response.status == 'success'){
                var shift = response.data;
                $('#editShiftModal input[name="shift_id"]').val(shift.shift_id);
                $('#editShiftModal select[name="cashier_id"]').val(shift.cashier_id);
                $('#editShiftModal input[name="starting_cash"]').val(shift.starting_cash);
                $('#editShiftModal input[name="starting_inventory"]').val(shift.starting_inventory);
                $('#editShiftModal input[name="shift_date"]').val(shift.shift_date);
                $('#editShiftModal input[name="time_in"]').val(shift.time_in ? shift.time_in.substring(11, 16) : ''); // Extract HH:MM from timestamp
                $('#editShiftModal input[name="time_out"]').val(shift.time_out ? shift.time_out.substring(11, 16) : ''); // Extract HH:MM from timestamp
                $('#editShiftModal textarea[name="notes"]').val(shift.notes);
                $('#editShiftModal input[name="ending_cash"]').val(shift.ending_cash);
                $('#editShiftModal input[name="ending_inventory"]').val(shift.ending_inventory);
                $('#editShiftModal input[name="sales"]').val(shift.sales);
                $('#editShiftModal').modal('show');
            } else {
                alert('Failed to retrieve shift details.');
            }
        },
        error: function(err){
            console.error('Error:', err);
            alert('An error occurred while fetching shift details.');
        }
    });
});

    // Handle submission of edited shift form
$('#edit-shift-form').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: './Actions.php?a=update_shift',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response){
                if(response.status == 'success'){
                    alert(response.msg);
                    $('#editShiftModal').modal('hide');
                    location.reload(); // Reload page after successful update
                } else {
                    alert('Failed to update shift: ' + response.msg);
                }
            },
            error: function(err){
                console.error('Error:', err);
                alert('An error occurred while updating shift.');
            }
        });
    });
});
$(document).ready(function() {
    $('.delete-shift').on('click', function(e) {
        e.preventDefault();
        var shift_id = $(this).data('shift-id');
        if (confirm('Are you sure you want to delete this shift?')) {
            $.ajax({
                url: './Actions.php?a=delete_shift',
                method: 'POST',
                data: { shift_id: shift_id },
                dataType: 'json',
                success: function(response) {
                    alert(response.msg);
                    if (response.status == 'success') {
                        location.reload(); // Reload page after successful deletion
                    }
                },
                error: function(err) {
                    console.error('Error:', err);
                    alert('An error occurred while deleting the shift.');
                }
            });
        }
    });
});
$('#filter').click(function() {
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();
        location.href = './?page=shifts&date_from=' + dateFrom + '&date_to=' + dateTo;
    });

    function printTable() {
    const printContent = document.getElementById('shifts-table');
    const originalContent = document.body.innerHTML;

    // Create a new window for printing
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Print Shifts</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
    printWindow.document.write('table, th, td { border: 1px solid black; }');
    printWindow.document.write('th, td { padding: 8px; text-align: left; }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(printContent.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Wait for the new window to load content and then print
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };

    // Restore the original content
    document.body.innerHTML = originalContent;
}

</script>


