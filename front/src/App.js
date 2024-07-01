import React from 'react';
import { Route, Routes } from 'react-router-dom';
import Home from './pages/homepage/Home';
import SignIn from './pages/signin/client-signin';
import SignUp from './pages/signup/SignUp'; 

function App() {
  return (
    <Routes>
      <Route path="/" element={<Home />} />
      <Route path="/signin" element={<SignIn />} />
      <Route path="/signup" element={<SignUp />} />
      {/* Ajouter d'autres routes ici */}
    </Routes>
  );
}

export default App;
