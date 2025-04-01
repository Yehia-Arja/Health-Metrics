import React from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import Login from './features/auth/login'
import Signup from './features/auth/signup'
import Dashboard from './features/dashboard/dashboard'
import { useSelector } from 'react-redux'
import './App.css'

function App() {
    const {data} = useSelector((state) => state.dashboard)
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/login" element={<Login />} />
                <Route path="/signup" element={<Signup />} />
                <Route path="/dashboard" element={<Dashboard dailyData={{dates:data.daily.dates,steps:data.daily.steps}} weeklyData={{dates:data.weekly.dates,steps:data.weekly.steps}} />} />
            </Routes>
        </BrowserRouter>
    )
   
}

export default App
