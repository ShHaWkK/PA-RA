<form id="addStockForm">
    <div>
        <label for="product_selector">Product</label>
        <select id="product_selector" name="product_id" required>
            <!-- Options will be populated dynamically -->
        </select>
    </div>
    <div>
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" min="1" required>
    </div>
    <div>
        <label for="availability">Availability</label>
        <select id="availability" name="availability" required>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
        </select>
    </div>
    <input type="hidden" id="warehouse_id" name="warehouse_id" value="" required>

    <div>
        <label for="added-volume">Added volume</label>
        <input id="added-volume" type="text" readonly>
    </div>

    <div>
        <label for="available-volume">Available volume</label>
        <input id="available-volume" type="text" readonly>
    </div>

    <div>
        <button type="submit">Add Stock</button>
    </div>
</form>