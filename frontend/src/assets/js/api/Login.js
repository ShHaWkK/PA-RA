import {setJwtCookie,setCookie, setJwtToken} from "./Api.js";
import {getUserCompanies} from "./Users.js";

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
        } else if (response.status === 403) {
            throw new Error('Account not approved');
        } else {
            throw new Error('Failed to login');
        }
    }

    let data= await response.json()

    if (data.token) {
        setJwtCookie(data.token);
        setJwtToken(data.token);

        console.log(data.role);

        switch (data.role){
            case 'admin':
                window.location.href = '/Admin/Volunteers';
                break;
            case 'volunteer':
                window.location.href = '/Volunteer/Planning';
                break;
            case 'merchant':
                const company = await getUserCompanies(data.id);
                console.log("company",company);
                setCookie('company_id', company[0].id, 1);
                window.location.href = '/Merchant/Collections';
                break;
        }
    }
    setCookie('user_role', data.role, 1);
    setCookie('user_id', data.id, 1);

    return await data;
}

async function authenticate(jwtToken, role) {
    const response = await fetch(apiEndpoint + '/checkSession' + '/' + role, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${jwtToken}`,
            'Content-Type': 'application/json'
        }
    });

    console.log("jwt:",jwtToken)

    if (!response.ok) {
        if (response.status === 401) {
            throw new Error('Expired Session');
        } else if (response.status === 403) {
            throw new Error('Access denied');
        } else {
            throw new Error('Failed to authenticate');
        }
    }

    const data = await response.json();

    if (!data.valid) {
        throw new Error('Invalid response format');
    }

    return data;
}

export { login, authenticate };