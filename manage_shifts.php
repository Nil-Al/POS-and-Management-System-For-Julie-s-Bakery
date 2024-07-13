<?php
require_once("DBConnection.php");

// Fetch shift details if editing an existing shift
if(isset($_GET['shift_id'])){
    $shift_id = $_GET['shift_id'];
    $qry = $conn->query("SELECT * FROM `shifts` WHERE shift_id = '$shift_id'");
    if($qry->num_rows > 0) {
        $shift = $qry->fetch_assoc();
        $cashier_id = $shift['cashier_id'];
        $starting_cash = $shift['starting_cash'];
        $starting_inventory = $shift['starting_inventory'];
        $shift_date = $shift['shift_date'];
        $time_in = $shift['time_in'];
        $time_out = $shift['time_out'];
        $notes = $shift['notes'];
    } else {
        // Shift not found
        // You can handle this case, like redirecting or showing an error message
    }
}

?>
<div class="container-fluid">
    <form action="" id="shift-form">
        <input type="hidden" name="shift_id" value="<?php echo isset($shift_id) ? $shift_id : '' ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cashier_id" class="control-label">Cashier ID</label>
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
                <div class="form-group">
                    <label for="starting_cash" class="control-label">Starting Cash</label>
                    <input type="number" step="any" name="starting_cash" id="starting_cash" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($starting_cash) ? $starting_cash : '' ?>">
                </div>
                <div class="form-group">
                    <label for="starting_inventory" class="control-label">Starting Inventory</label>
                    <input type="number" step="any" name="starting_inventory" id="starting_inventory" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($starting_inventory) ? $starting_inventory : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="shift_date" class="control-label">Shift Date</label>
                    <input type="date" name="shift_date" id="shift_date" required class="form-control form-control-sm rounded-0" value="<?php echo isset($shift_date) ? $shift_date : '' ?>">
                </div>
                <div class="form-group">
                    <label for="time_in" class="control-label">Time In</label>
                    <input type="time" name="time_in" id="time_in" required class="form-control form-control-sm rounded-0" value="<?php echo isset($time_in) ? $time_in : '' ?>">
                </div>
                <div class="form-group">
                    <label for="time_out" class="control-label">Time Out</label>
                    <input type="time" name="time_out" id="time_out" required class="form-control form-control-sm rounded-0" value="<?php echo isset($time_out) ? $time_out : '' ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="notes" class="control-label">Notes</label>
                    <textarea name="notes" id="notes" cols="30" rows="3" class="form-control rounded-0"><?php echo isset($notes) ? $notes : '' ?></textarea>
                </div>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-sm rounded-0 btn-primary">Save</button>
    </form>
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
            type: 'POST',
            dataType: 'json',
            error: function(err) {
                console.log(err);
                _el.addClass('alert alert-danger');
                _el.text("An error occurred.");
                _this.prepend(_el);
                _el.show('slow');
                $('#uni_modal button').attr('disabled', false);
                $('#uni_modal button[type="submit"]').text('Save');
            },
            success: function(resp) {
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
});
</script>
