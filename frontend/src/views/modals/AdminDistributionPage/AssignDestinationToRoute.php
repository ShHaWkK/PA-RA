<div id="addDestinationModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddDestinationModal">&times;</span>

        <div id="addDestinationContent">
            <h2><?php echo $data['add_destination_title']; ?></h2>

            <form id="addDestinationForm">
                <div class="form-group">
                    <label for="address"><?php echo $data['address_label']; ?></label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="form-group">
                    <label for="recipientType"><?php echo $data['recipient_type_label']; ?></label>
                    <select id="recipientType" name="recipient_type" required>
                        <option value="individual"><?php echo $data['recipient_individual']; ?></option>
                        <option value="association"><?php echo $data['recipient_association']; ?></option>
                        <option value="company"><?php echo $data['recipient_company']; ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="warehouseSelect"><?php echo $data['warehouse_label']; ?></label>
                    <select name="warehouse" id="warehouseSelect" class="warehouseSelect" required></select>
                </div>

                <div class="form-group">
                    <label for="comment"><?php echo $data['comment_label']; ?></label>
                    <textarea id="comment" name="comment" rows="4" required><?php echo $data['default_comment']; ?></textarea>
                </div>

                <!-- Section dynamique pour ajouter des produits/livraisons -->
                <div id="deliveriesSection">
                    <h2><?php echo $data['deliveries_section_title']; ?></h2>
                    <div id="deliveryContainer">
                        <h3><?php echo $data['delivery_item_title']; ?> 0</h3>
                        <div class="delivery-item">
                            <div class="form-group">
                                <label for="productSelect_0"><?php echo $data['product_label']; ?></label>
                                <select name="product[]" id="productSelect_0" required>
                                    <!-- Options à remplir dynamiquement -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantity_0"><?php echo $data['quantity_label']; ?></label>
                                <input type="number" name="quantity[]" id="quantity_0" min="1" required>
                            </div>

                            <div class="form-group">
                                <label for="comment_0"><?php echo $data['delivery_comment_label']; ?></label>
                                <input type="text" name="comment[]" id="comment_0">
                            </div>

                            <div class="form-group">
                                <label for="status_0"><?php echo $data['status_label']; ?></label>
                                <select name="status[]" id="status_0" required>
                                    <option value="pending"><?php echo $data['status_pending']; ?></option>
                                    <option value="delivered"><?php echo $data['status_delivered']; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addDeliveryButton"><?php echo $data['add_delivery_button']; ?></button>
                </div>

                <button type="submit" id="addDestinationsButton"><?php echo $data['save_button']; ?></button>
            </form>
        </div>

        <?php
        $loaderId = 'loadingBodyAddDestination';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>
    </div>
</div>