// Fichier relatif à l'envoi de requêtes sécurisées grâce au JWT

let jwtToken = null;

function setJwtCookie(token, cookieName = 'jwt', expiresInDays = 1) {
    console.log("we are in setJwtCookie");
    const date = new Date();
    date.setTime(date.getTime() + (expiresInDays * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${cookieName}=${token};${expires};path=/;Secure;SameSite=Strict`;
}

function getCookie(cookieName) {
    const name = `${cookieName}=`;
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookies = decodedCookie.split(';');
    for (let cookie of cookies) {
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) === 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return null;
}

function deleteCookie(cookieName) {
    document.cookie = `${cookieName}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
}

function setJwtToken(token) {
    jwtToken = token;
}

async function fetchWithAuth(endpoint, options = {}) {
    if (!jwtToken) {
        jwtToken = getCookie('jwt');
        if (!jwtToken) {
            throw new Error('JWT token is not set.');
        }
    }

    const headers = {
        ...options.headers,
        'Authorization': `Bearer ${jwtToken}`
    };

    const fetchOptions = {
        ...options,
        headers
    };

    try {
        const response = await fetch(apiEndpoint, fetchOptions);
        if (!response.ok) {
            throw new Error(`Network error: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error fetching ${endpoint}:`, error.message);
        throw error;
    }
}

export { setJwtToken, fetchWithAuth, setJwtCookie, getCookie, deleteCookie };