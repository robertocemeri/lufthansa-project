import { useContext, useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import AuthContext from "../context/AuthContext";
import { Link } from "react-router-dom";

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const { login, token } = useContext(AuthContext);
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    setError("");

    e.preventDefault();
    const logged_in = await login({ email, password });
    if (logged_in) {
      navigate("/");
    }
    setError("Login failed");
  };

  useEffect(() => {
    if (token) {
      navigate("/");
    }
  }, [token, navigate]);

  return (
    <div className="flex items-center justify-center h-screen bg-gray-100">
      <form
        className="bg-white p-6 rounded-lg shadow-lg w-96"
        onSubmit={handleSubmit}
      >
        <h2 className="text-2xl font-bold mb-4 text-center">Login</h2>
        {error && <div className="text-red-500 text-center mb-4">{error}</div>}
        <input
          type="email"
          className="w-full p-2 border rounded mb-4"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
        <input
          type="password"
          className="w-full p-2 border rounded mb-4"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
        <button
          type="submit"
          className="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600"
        >
          Login
        </button>
        <div className="text-center mt-4">
          <Link to="/register" className="text-blue-500 hover:underline">
            Don't have an account? Register here
          </Link>
        </div>
      </form>
    </div>
  );
};

export default Login;
