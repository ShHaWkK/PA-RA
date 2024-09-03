<form id="addStockForm">
    <div>
        <label for="product_selector"><?php echo $data['product_label']; ?></label>
        <select id="product_selector" name="product_id" required>
            <!-- Options will be populated dynamically -->
        </select>
    </div>
    <div>
        <label for="quantity"><?php echo $data['quantity_label']; ?></label>
        <input type="number" id="quantity" name="quantity" min="1" required>
    </div>
    <div>
        <label for="availability"><?php echo $data['availability_label']; ?></label>
        <select id="availability" name="availability" required>
            <option value="available"><?php echo $data['availability_available']; ?></option>
            <option value="unavailable"><?php echo $data['availability_unavailable']; ?></option>
        </select>
    </div>
    <input type="hidden" id="warehouse_id" name="warehouse_id" value="" required>

    <div>
        <label for="added-volume"><?php echo $data['added_volume_label']; ?></label>
        <input id="added-volume" type="text" readonly>
    </div>

    <div>
        <label for="available-volume"><?php echo $data['available_volume_label']; ?></label>
        <input id="available-volume" type="text" readonly>
    </div>

    <div>
        <button type="submit"><?php echo $data['add_stock_button']; ?></button>
    </div>
</form>