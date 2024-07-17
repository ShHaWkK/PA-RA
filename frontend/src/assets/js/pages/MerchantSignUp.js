import {registerMerchant} from "../api/Users.js";

function addMerchantSubmitEvent(){
    document.getElementById('registrationForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const userData = Object.fromEntries(formData.entries());
        console.log(formData);
        console.log(userData);
        const result = await registerMerchant(userData);

        if (!result.ok) {
            switch (result.status){
                case 409:
                    alert("User with this email or SIRET already exists");
                    break;
                default:
                    console.log('Failed to register merchant');
                    alert('Failed to register merchant');
                    break;
            }
        } else {
            console.log('Merchant registered successfully');
            alert('Merchant registered successfully');
        }
    });
}

document.addEventListener('DOMContentLoaded',
    function (){
        addMerchantSubmitEvent();
    });
