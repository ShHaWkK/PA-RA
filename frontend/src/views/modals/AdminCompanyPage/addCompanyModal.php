<div id="addCompanyModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddCompanyModal">&times;</span>
        <h2><?php echo $data['add_company_title']; ?></h2>
        <form id="addCompanyForm">
            <label for="addNameInput"><?php echo $data['company_name_label']; ?></label>
            <input type="text" id="addNameInput" name="name" required>

            <label for="addAddressInput"><?php echo $data['address_label']; ?></label>
            <input type="text" id="addAddressInput" name="address" required>

            <label for="addContactInfoInput"><?php echo $data['contact_info_label']; ?></label>
            <input type="text" id="addContactInfoInput" name="contact_info" required>

            <label for="addSiretInput"><?php echo $data['siret_label']; ?></label>
            <input type="text" id="addSiretInput" name="siret" required>

            <label for="addRenewalDateInput"><?php echo $data['renewal_date_label']; ?></label>
            <input type="date" id="addRenewalDateInput" name="renewal_date" required>

            <label for="addRenewalStatusSelect"><?php echo $data['renewal_status_label']; ?></label>
            <select id="addRenewalStatusSelect" name="renewal_status" required>
                <option value="pending"><?php echo $data['status_pending']; ?></option>
                <option value="completed"><?php echo $data['status_completed']; ?></option>
            </select>

            <button type="submit"><?php echo $data['add_company_button']; ?></button>
        </form>
        <?php
        $loaderId = 'loadingAddCompany';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>