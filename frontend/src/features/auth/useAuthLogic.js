import { useState, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import { loginUser } from "./authSlice";
import { signupUser } from "./authSlice";
import { clearError } from "./authSlice";

const useAuthLogic = () => { 
    const [form, setForm] = useState({ email: "", password: "", username: "" });
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { user, error, loading } = useSelector((state) => state.auth);
    

    const handleLogin = (e) => {
        e.preventDefault();
        dispatch(loginUser( {email: form.email, password: form.password} ));
    };
    const handleSignup = (e) => {
        e.preventDefault();
        dispatch(signupUser( {email: form.email, password: form.password, username: form.username} ));
    };
    
    useEffect(() => {
        if (user) {
            navigate("/dashboard");
        }
    }, [user, navigate]);

    useEffect(() => {
      dispatch(clearError());
    }, [dispatch, form.email, form.password]);

    return {
        form,
        setForm,
        handleLogin,
        handleSignup,
        error,
        loading,
    };
}
export default useAuthLogic;