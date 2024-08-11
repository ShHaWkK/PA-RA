import { registerMerchant } from "../api/Users.js";

// Fonction pour ajouter un écouteur d'événement de soumission au formulaire des marchands
function addMerchantSubmitEvent() {
    // Supprime les écouteurs d'événements existants, s'il y en a, pour éviter les soumissions multiples
    const form = document.getElementById('merchantForm');
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);

    newForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const userData = Object.fromEntries(formData.entries());
        console.log(formData);
        console.log(userData);
        const result = await registerMerchant(userData);

        if (!result.ok) {
            switch (result.status) {
                case 409:
                    alert("Un utilisateur avec cet e-mail ou SIRET existe déjà");
                    break;
                default:
                    console.log('Échec de l\'inscription du marchand');
                    alert('Échec de l\'inscription du marchand');
                    break;
            }
        } else {
            console.log('Marchand inscrit avec succès');
            alert('Marchand inscrit avec succès');
        }
    });
}

// Configuration initiale lorsque le document est prêt
document.addEventListener('DOMContentLoaded', function() {
    addMerchantSubmitEvent();
});

export {addMerchantSubmitEvent}