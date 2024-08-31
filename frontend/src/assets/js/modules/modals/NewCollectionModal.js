import {getCompany} from "../../api/companies.js";
import {getProductByID} from "../../api/Products.js";

export async function populateCompaniesInModal(selectedCompanyId) {
    const modalBody = document.getElementById('modalBodyCompany');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("selected User ID", selectedCompanyId);

    document.getElementById('loadingCompanies').classList.remove('hidden');

    console.log("selectedCompanyId",selectedCompanyId)
    // Obtenir les entreprises de l'utilisateur
    var company = await getCompany(selectedCompanyId);
    console.log(company);

    // Vérifier si company est un tableau
    if (company) {
        // Ajouter chaque entreprise au corps de la modale
            const companyElement = document.createElement('div');
            companyElement.classList.add('company');

            const companyName = document.createElement('h3');
            companyName.textContent = company.name;

            const companyAddress = document.createElement('p');
            companyAddress.textContent = 'Address: ' + company.address;

            const companyContact = document.createElement('p');
            companyContact.textContent = 'Contact Info: ' + company.contact_info;

            const companySiret = document.createElement('p');
            companySiret.textContent = 'SIRET: ' + company.siret;

            companyElement.appendChild(companyName);
            companyElement.appendChild(companyAddress);
            companyElement.appendChild(companyContact);
            companyElement.appendChild(companySiret);

            modalBody.appendChild(companyElement);
    } else {
        // Afficher le message "No company found"
        const nocompanyMessage = document.createElement('p');
        nocompanyMessage.textContent = 'No company found.';
        modalBody.appendChild(nocompanyMessage);
    }
    document.getElementById('loadingCompanies').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
    const closeCompanyModal  = document.getElementById('closeUserCompaniesButton');
    const companyModal= document.getElementById('userCompaniesModal');

    closeCompanyModal.addEventListener('click',function (){
        companyModal.style.display = "none";
    });

    const closeProductModal  = document.getElementById('closeProductButton');
    const productModal= document.getElementById('productDetailModal');

    closeProductModal.addEventListener('click',function (){
        productModal.style.display = "none";
    });

    // Fenêtre modale de vue des disponibilités
    var availabilityModal = document.getElementById("volunteerAvailabilitiesModal");
    var availabilitySpan = document.getElementById("closeVolunteerAvailabilitiesButton");

    availabilitySpan.onclick = function() {
        availabilityModal.style.display = "none";
    }

    // Fenêtre modale de vue des compétences
    var skillModal = document.getElementById("volunteerSkillModal");
    var skillSpan = document.getElementById("closeVolunteerSkillButton");

    skillSpan.onclick = function() {
        skillModal.style.display = "none";
    }
});