import { BrowserRouter, Route, Routes } from 'react-router-dom'
import Login from './features/auth/login'
import Signup from './features/auth/signup'
import Dashboard from './features/dashboard/dashboard'

import './App.css'

function App() {

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/signup" element={<Signup />} />
        <Route path="/dashboard" element={<Dashboard />} />
      </Routes>
    </BrowserRouter>
  )
}

export default App
