import useAuthLogic from "./useAuthLogic";
import Button from "../../components/Button"; 
import Input from "../../components/Input";
import { Link } from "react-router-dom";
import "./styles.css";

const Login = () => { 
    const { form, setForm, handleLogin, error, loading } = useAuthLogic();
  
    return (
        <div className="input-container">
            <h1>Login</h1>
            <label>Email</label>
            <Input
                type="email"
                value={form.email}
                onChange={(e) => setForm({...form,email: e.target.value})}
                placeholder="Email"
            />
            <label>Password</label>
            <Input
                type="password"
                value={form.password}
                onChange={(e) => setForm({...form,password: e.target.value})}
                placeholder="Password"
            />
            <p>Don't have an account? <Link to="/signup"className="link">signup now!</Link></p>
            <Button text={loading ? "Logging in..." : "L O G I N"} onClick={handleLogin}></Button>
            {error && <p className="error">{error}</p>}
        </div>
    );
};

export default Login;
