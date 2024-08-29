import { registerMerchant } from "../api/Users.js";
import { getAllCompanies } from "../api/Companies.js";


function addMerchantSubmitEvent() {
    const form = document.getElementById('merchantForm');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        // Ajoutez un log pour vérifier le contenu de FormData
        for (const [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        const userData = {};
        formData.forEach((value, key) => {
            if (key !== 'business_license') {
                userData[key] = value;
            }
        });

        formData.append('json_data', JSON.stringify(userData));

        const fileInput = document.getElementById('business_license');
        const file = fileInput.files[0];
        if (file) {
            formData.append('file_data', file);
        }

        try {
            const result = await registerMerchant(formData);

            if (!result.ok) {
                let errorMessage = 'Échec de l\'inscription du marchand';
                if (result.status === 409) {
                    errorMessage = "Un utilisateur avec cet e-mail ou SIRET existe déjà";
                }
                alert(errorMessage);
            } else {
                alert('Marchand inscrit avec succès');
                window.location.reload();
            }
        } catch (error) {
            console.error('Error during form submission:', error.message);
            alert('Une erreur est survenue. Veuillez réessayer.');
        }
    });
}

async function populateCompanySelector() {
    try {
        const companies = await getAllCompanies();
        const companySelector = document.getElementById('company_selector');

        companies.forEach(company => {
            const option = document.createElement('option');
            option.value = company.id;
            option.textContent = company.name;
            companySelector.appendChild(option);
        });
    } catch (error) {
        console.error('Error populating company selector:', error.message);
    }
}

function addCompanyCheckboxEvent() {
    document.getElementById('new_company_checkbox').addEventListener('change', function() {
        const newCompanyFields = document.getElementById('new_company_fields');
        const companySelector = document.getElementById("company_selector_section");

        newCompanyFields.style.display = this.checked ? 'block' : 'none';
        companySelector.style.display = this.checked ? 'none' : 'block';

        const fields = ['company_name', 'siret', 'address', 'renewal_date'];
        fields.forEach(id => {
            document.getElementById(id).required = this.checked;
        });

        document.getElementById('company_selector').required = !this.checked;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    populateCompanySelector();
    addCompanyCheckboxEvent();
    addMerchantSubmitEvent();
});

export {addMerchantSubmitEvent};