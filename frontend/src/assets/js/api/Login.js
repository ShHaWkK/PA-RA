import {setJwtCookie, setJwtToken} from "./Api.js";

async function login(email, password) {
    const loginData = { email, password };
    const response = await fetch(apiEndpoint + '/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(loginData)
    });

    if (!response.ok) {
        if (response.status === 401) {
            throw new Error('Invalid email or password');
        } else if (response.status === 400) {
            throw new Error('Missing required fields');
        } else {
            throw new Error('Failed to login');
        }
    }

    let data= await response.json()

    if (data.token) {
        setJwtCookie(data.token);
        setJwtToken(data.token);
        window.location.href = '/Admin';
    }

    return await data;
}

export { login };