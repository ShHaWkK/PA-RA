<div id="editCompanyModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditCompanyModal">&times;</span>
        <h2><?php echo $data['edit_company_title']; ?></h2>
        <form id="editCompanyForm">
            <input type="hidden" id="companyId" name="id">

            <label for="editNameInput"><?php echo $data['company_name_label']; ?></label>
            <input type="text" id="editNameInput" name="name" required>

            <label for="editAddressInput"><?php echo $data['address_label']; ?></label>
            <input type="text" id="editAddressInput" name="address" required>

            <label for="editContactInfoInput"><?php echo $data['contact_info_label']; ?></label>
            <input type="text" id="editContactInfoInput" name="contact_info" required>

            <label for="editSiretInput"><?php echo $data['siret_label']; ?></label>
            <input type="text" id="editSiretInput" name="siret" required>

            <label for="editRenewalDateInput"><?php echo $data['renewal_date_label']; ?></label>
            <input type="date" id="editRenewalDateInput" name="renewal_date" required>

            <label for="editRenewalStatusSelect"><?php echo $data['renewal_status_label']; ?></label>
            <select id="editRenewalStatusSelect" name="renewal_status" required>
                <option value="pending"><?php echo $data['status_pending']; ?></option>
                <option value="completed"><?php echo $data['status_completed']; ?></option>
            </select>

            <button type="submit"><?php echo $data['save_changes_button']; ?></button>
        </form>
        <?php
        $loaderId = 'loadingEditCompany';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>