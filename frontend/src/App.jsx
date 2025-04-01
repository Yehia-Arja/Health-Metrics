import React from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import Login from './features/auth/login'
import Signup from './features/auth/signup'
import './App.css'

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/login" element={<Login />} />
                <Route path="/signup" element={<Signup />} />
            </Routes>
        </BrowserRouter>
    )
   
}

export default App
