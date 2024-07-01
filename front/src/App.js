import React from 'react';
import  SignIn  from './pages/signin/client-signin';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import './styles/global.css';


const App = () => {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<SignIn />} />
      </Routes>
    </Router>
  );
};

export default App;
