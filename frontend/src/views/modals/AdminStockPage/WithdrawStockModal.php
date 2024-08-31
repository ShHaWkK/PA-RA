<!-- Fenêtre modale pour retirer du stock -->
<div id="withdrawStockModal" class="modal">
    <div class="modal-content">
        <span id="closeWithdrawModal" class="close">&times;</span>
        <h2>Retirer du stock</h2>

        <label for="withdrawQuantity">Quantité à retirer:</label>
        <input type="number" id="withdrawQuantity" name="withdrawQuantity" min="1" required>

        <label for="currentStockVolume">Volume du stock actuel:</label>
        <input type="number" id="currentStockVolume" name="currentStockVolume" disabled>

        <label for="warehouseVolume">Volume de l'entrepôt:</label>
        <input type="number" id="warehouseVolume" name="warehouseVolume" disabled>

        <label for="withdrawVolume">Volume à retirer:</label>
        <input type="number" id="withdrawVolume" name="withdrawVolume" disabled>

        <label for="postWithdrawStockVolume">Volume du stock après retrait:</label>
        <input type="number" id="postWithdrawStockVolume" name="postWithdrawStockVolume" disabled>

        <button id="confirmWithdrawButton">Confirmer le retrait</button>
    </div>
</div>
