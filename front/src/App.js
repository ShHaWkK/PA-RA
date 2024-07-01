import React from 'react';
import { Route, Routes } from 'react-router-dom';
import Home from './pages/homepage/Home';
import SignIn from './pages/signin/client-signin';
import './App.css';

function App() {
  return (
    <div>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/signin" element={<SignIn />} />
        {/* Ajouter d'autres routes ici */}
      </Routes>
    </div>
  );
}

export default App;
