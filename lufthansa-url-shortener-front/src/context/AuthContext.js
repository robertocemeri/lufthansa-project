import { createContext, useState, useEffect } from "react";
import axiosInstance from "../api/axiosInstance";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(localStorage.getItem("token") || null);

  useEffect(() => {
    if (token) {
      axiosInstance
        .get("/auth/get-user")
        .then((res) => setUser(res.data.user))
        .catch(() => {
          setUser(null);
          localStorage.removeItem("token");
          window.location.href = "/login";
        });
    }
  }, [token]);

  const login = async (credentials) => {
    try {
      const { data } = await axiosInstance.post("/auth/login", credentials);
      localStorage.setItem("token", data.token);
      setToken(data.token);
      setUser(data.user);
      return true;
    } catch (error) {
      return false;
    }
  };

  const register = async (credentials) => {
    try {
      await axiosInstance.post("/auth/register", credentials);
      return true;
    } catch (error) {
      return false;
    }
  };

  const logout = () => {
    axiosInstance.post("/auth/logout").finally(() => {
      localStorage.removeItem("token");
      setUser(null);
      setToken(null);
    });
  };
  

  return (
    <AuthContext.Provider value={{ user, token, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export default AuthContext;
