import axios from 'axios';

const config = {
  apiURL: process.env.REACT_APP_API_URL
};

console.log("apiURL", config.apiURL);

const instance = axios.create({
  baseURL: config.apiURL,
  headers: {
    'Content-Type': 'application/json',
  },
});

instance.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      console.log('Session is invalid');
      alert('Session is invalid');
    }
    return Promise.reject(error);
  }
);

export default instance;