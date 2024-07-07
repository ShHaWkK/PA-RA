document.getElementById('registrationForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const userData = Object.fromEntries(formData.entries());
    console.log(formData);
    console.log(userData);
        const result = await registerMerchant(userData);
        console.log('Merchant registered:', result);
});