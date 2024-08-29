import { populateCompanyTable } from "../modules/tables/CompaniesTable.js";
import { deleteCompany, getCompany, updateCompany, createCompany, getEmployeesFromCompany } from "../api/Companies.js";

async function deleteCompanies() {
    const selectedRadio = document.querySelector('input[name="CompanySelection"]:checked');
    const selectedCompanyId = selectedRadio ? selectedRadio.value : null;

    if (!selectedCompanyId) {
        alert("Veuillez sélectionner au moins une société à supprimer.");
        return;
    }

    const isConfirmed = confirm("Êtes-vous sûr de vouloir supprimer cette société ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        await deleteCompany(selectedCompanyId);
        alert("Société supprimée avec succès");
        await populateCompanyTable();
    } catch (error) {
        console.error("Erreur lors de la suppression de la société :", error.message);
        alert("Une erreur est survenue lors de la suppression de la société.");
    }
}

async function populateEditCompanyModalData(companyId) {
    const formElement = document.getElementById('editCompanyForm');
    const loadingIndicator = document.getElementById('loadingEditCompany');

    formElement.classList.add('hidden');
    loadingIndicator.classList.remove('hidden');

    try {
        const companyData = await getCompany(companyId);
        if (!companyData) {
            throw new Error('Données de la société non trouvées');
        }

        console.log("company Data",companyData);

        document.getElementById('companyId').value = companyData.id ?? '';
        document.getElementById('editNameInput').value = companyData.name ?? '';
        document.getElementById('editAddressInput').value = companyData.address ?? '';
        document.getElementById('editContactInfoInput').value = companyData.contact_info ?? '';
        document.getElementById('editSiretInput').value = companyData.siret ?? '';
        document.getElementById('editRenewalDateInput').value = companyData.renewal_date ?? '';
        document.getElementById('editRenewalStatusSelect').value = companyData.renewal_status ?? '';

    } catch (error) {
        console.error('Erreur lors du chargement des données de la société :', error.message);
        alert('Une erreur est survenue lors du chargement des données de la société. Veuillez réessayer plus tard.');
    } finally {
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

export async function populateCompanyEmployeesModal(companyID) {
    // Afficher le loader
    document.getElementById('loadingCompanyEmployeesDetails').classList.remove('hidden');

    const modalBody = document.getElementById('modalBodyCompanyEmployeesDetails');
    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';

    try {
        // Récupérer les employés de l'entreprise
        console.log("companyID",companyID);
        const employees = await getEmployeesFromCompany(companyID);

        console.log(employees);

        // Vérifier si le conteneur de la modale existe
        const modalContent = document.getElementById('modalBodyCompanyEmployeesDetails');
        if (!modalContent) {
            console.error('Company employees modal content container not found.');
            return;
        }

        // Effacer le contenu existant du modal
        modalContent.innerHTML = '';

        if (!employees || employees.length === 0) {
            modalContent.textContent = 'No employees found for this company.';
            return;
        }

        // Créer une liste pour afficher les employés
        const employeeList = document.createElement('ul');
        employeeList.classList.add('employee-list'); // Ajout d'une classe pour le style, si nécessaire

        // Parcourir les employés et les ajouter à la liste
        employees.forEach(employee => {
            const employeeItem = document.createElement('li');
            employeeItem.classList.add('employee-item'); // Ajout d'une classe pour le style, si nécessaire

            // Contenu de l'employé
            const employeeInfo = document.createElement('span');
            employeeInfo.innerHTML = `
                <strong>Name:</strong> ${employee.user_name} <br>
                <strong>Email:</strong> ${employee.user_mail} <br>
                <strong>Phone:</strong> ${employee.user_phone} <br>
                <strong>Role:</strong> ${employee.role} <br>
            `;

            // Ajouter les informations de l'employé à l'élément de la liste
            employeeItem.appendChild(employeeInfo);

            // Ajouter l'élément à la liste
            employeeList.appendChild(employeeItem);
        });

        // Ajouter la liste des employés au modal
        modalContent.appendChild(employeeList);

        // Afficher la fenêtre modale
        document.getElementById('companyEmployeesModal').style.display = 'block';

    } catch (error) {
        console.error('Error populating company employees modal:', error.message);

        const modalContent = document.getElementById('modalBodyCompanyEmployeesDetails');
        if (modalContent) {
            modalContent.textContent = 'No employees available for this company.';
        }

    } finally {
        // Cacher le loader
        document.getElementById('loadingCompanyEmployeesDetails').classList.add('hidden');
    }
}

async function editCompany(companyId) {
    const formElement = document.getElementById('editCompanyForm');
    const loadingIndicator = document.getElementById('loadingEditCompany');
    const modalElement = document.getElementById('editCompanyModal');

    loadingIndicator.classList.remove('hidden');
    formElement.classList.add('hidden');

    const name = document.getElementById('editNameInput').value.trim();
    const address = document.getElementById('editAddressInput').value.trim();
    const contactInfo = document.getElementById('editContactInfoInput').value.trim();
    const siret = document.getElementById('editSiretInput').value.trim();
    const renewalDate = document.getElementById('editRenewalDateInput').value;
    const renewalStatus = document.getElementById('editRenewalStatusSelect').value;

    if (!name || !address || !contactInfo || !siret || !renewalDate || !renewalStatus) {
        alert('Tous les champs sont requis.');
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
        return;
    }

    const companyData = {
        name,
        address,
        contact_info: contactInfo,
        siret,
        renewal_date: renewalDate,
        renewal_status: renewalStatus
    };

    try {
        await updateCompany(companyId, companyData);
        alert("Société modifiée avec succès");
        modalElement.style.display = 'none';
        await populateCompanyTable();
    } catch (error) {
        console.error('Erreur lors de la modification de la société :', error);
        alert('Erreur lors de la modification de la société. Veuillez réessayer.');
    } finally {
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

async function addCompany() {
    const formElement = document.getElementById('addCompanyForm');
    const loadingIndicator = document.getElementById('loadingAddCompany');
    const modalElement = document.getElementById('addCompanyModal');

    loadingIndicator.classList.remove('hidden');
    formElement.classList.add('hidden');

    const name = document.getElementById('addNameInput').value.trim();
    const address = document.getElementById('addAddressInput').value.trim();
    const contactInfo = document.getElementById('addContactInfoInput').value.trim();
    const siret = document.getElementById('addSiretInput').value.trim();
    const renewalDate = document.getElementById('addRenewalDateInput').value;
    const renewalStatus = document.getElementById('addRenewalStatusSelect').value;

    if (!name || !address || !contactInfo || !siret || !renewalDate || !renewalStatus) {
        alert('Tous les champs sont requis.');
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
        return;
    }

    const companyData = {
        name,
        address,
        contact_info: contactInfo,
        siret,
        renewal_date: renewalDate,
        renewal_status: renewalStatus
    };

    try {
        await createCompany(companyData);
        alert("Société ajoutée avec succès");
        modalElement.style.display = 'none';
        await populateCompanyTable();
    } catch (error) {
        console.error('Erreur lors de l\'ajout de la société :', error);
        alert('Erreur lors de l\'ajout de la société. Veuillez réessayer.');
    } finally {
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    populateCompanyTable();
    document.getElementById('deleteCompanyButton').addEventListener('click', deleteCompanies);

    // Fenêtre de modification de la société
    const editCompanyModal = document.getElementById("editCompanyModal");
    const editCompanySpan = document.getElementById("closeEditCompanyModal");
    const editCompanyForm = document.getElementById("editCompanyForm");
    const editCompanyButton = document.getElementById("modifyCompanyButton");

    editCompanyButton.onclick = function () {
        const selectedCheckbox = document.querySelector('input[name="CompanySelection"]:checked');
        const selectedCompanyID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedCompanyID) {
            alert("Veuillez sélectionner une société.");
            return;
        }

        document.getElementById("editCompanyModal").style.display = "block";
        populateEditCompanyModalData(selectedCompanyID);
    }

    editCompanySpan.onclick = function () {
        editCompanyModal.style.display = "none";
    };

    editCompanyForm.onsubmit = async function (event) {
        event.preventDefault();

        const selectedCheckbox = document.querySelector('input[name="CompanySelection"]:checked');
        const selectedCompanyID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedCompanyID) {
            alert("Veuillez sélectionner une société.");
            return;
        }

        await editCompany(selectedCompanyID);
    };

    // Fenêtre d'ajout de la société
    const addCompanyModal = document.getElementById("addCompanyModal");
    const addCompanySpan = document.getElementById("closeAddCompanyModal");
    const addCompanyButton = document.getElementById("addCompanyButton");
    const addCompanyForm = document.getElementById("addCompanyForm");

    addCompanyButton.onclick = function () {
        addCompanyModal.style.display = "block";
    };

    addCompanySpan.onclick = function () {
        addCompanyModal.style.display = "none";
    };

    addCompanyForm.onsubmit = async function (event) {
        event.preventDefault();
        await addCompany();
    };

    // Fenêtre de vue des employés
    const employeesSpan = document.getElementById("closeEmployeesModal");
    const employeeModal = document.getElementById("companyEmployeesModal");

    console.log("employeemodal",employeeModal);
    console.log("employeeSpan",employeesSpan);

    employeesSpan.onclick = function () {
        employeeModal.style.display = 'none';
    }

});