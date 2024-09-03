<!-- Fenêtre modale pour retirer du stock -->
<div id="withdrawStockModal" class="modal">
    <div class="modal-content">
        <span id="closeWithdrawModal" class="close">&times;</span>
        <h2><?php echo $data['withdraw_stock_title']; ?></h2>

        <label for="withdrawQuantity"><?php echo $data['withdraw_quantity_label']; ?></label>
        <input type="number" id="withdrawQuantity" name="withdrawQuantity" min="1" required>

        <label for="currentStockVolume"><?php echo $data['current_stock_volume_label']; ?></label>
        <input type="number" id="currentStockVolume" name="currentStockVolume" disabled>

        <label for="warehouseVolume"><?php echo $data['warehouse_volume_label']; ?></label>
        <input type="number" id="warehouseVolume" name="warehouseVolume" disabled>

        <label for="withdrawVolume"><?php echo $data['withdraw_volume_label']; ?></label>
        <input type="number" id="withdrawVolume" name="withdrawVolume" disabled>

        <label for="postWithdrawStockVolume"><?php echo $data['post_withdraw_stock_volume_label']; ?></label>
        <input type="number" id="postWithdrawStockVolume" name="postWithdrawStockVolume" disabled>

        <button id="confirmWithdrawButton"><?php echo $data['confirm_withdraw_button']; ?></button>
    </div>
</div>
