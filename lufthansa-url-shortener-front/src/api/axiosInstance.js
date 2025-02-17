import axios from "axios";

const API_URL = "http://localhost:8000/api"; // Adjust based on your backend

const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
});

const refreshToken = async () => {
  try {
    const response = await axios.post(`${API_URL}/auth/refresh`, {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    const newToken = response.data.token;
    localStorage.setItem("token", newToken);
    return newToken;
  } catch (error) {
    console.error("Token refresh failed:", error);
    return null;
  }
};

// Request interceptor to attach the token to requests
axiosInstance.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

axiosInstance.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (error.response && error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true; // Mark request to avoid infinite loops

      const newToken = await refreshToken();
      console.log(newToken);
      
      if (newToken) {
        axiosInstance.defaults.headers.Authorization = `Bearer ${newToken}`;
        originalRequest.headers.Authorization = `Bearer ${newToken}`;
        return axiosInstance(originalRequest); // Retry the original request
      }

      // If refresh fails, remove token and redirect to login
      localStorage.removeItem("token");
      window.location.href = "/login";
    }

    return Promise.reject(error);
  }
);

export default axiosInstance;
