import useAuthLogic from "./useAuthLogic";
import Button from "../../components/Button"; 
import Input from "../../components/Input";
import { Link } from "react-router-dom";
import "./styles.css";

const Login = () => { 
    const { form, setForm, handleSignup, error, loading } = useAuthLogic();
    
    return (
        <div className="input-container">
            <h1>Login</h1>
            <label>Username</label>
            <Input
                type="text"
                value={form.username}
                onChange={(e) => setForm({...form,username: e.target.value})}
                placeholder="Username"
            />
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
            <p>Already have an account? <Link to="/login"className="link">Login now</Link></p>
            <Button text = {loading ? "Signing up..." : "Signup"} onClick={handleSignup}></Button>
            {error && <p className="error">{error}</p>}
        </div>
    );
};

export default Login;
