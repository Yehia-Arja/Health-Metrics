import "./styles.css";
const Input = ({ onChange, value, placeholder, type, className = "" }) => {
    return (
        <input
            type={type}
            value={value}
            placeholder={placeholder}
            onChange={onChange}
            className={`input ${className}`}
        />
    );
}
export default Input;