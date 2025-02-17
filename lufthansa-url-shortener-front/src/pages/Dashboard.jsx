import { useContext, useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import AuthContext from "../context/AuthContext";
import axiosInstance from "../api/axiosInstance";

const Dashboard = () => {
  const { user, logout } = useContext(AuthContext);
  const [urls, setUrls] = useState([]);
  const [longUrl, setLongUrl] = useState("");
  const [shortUrl, setShortUrl] = useState("");
  const [shortInput, setShortInput] = useState("");
  const [originalUrl, setOriginalUrl] = useState("");
  const [isOld, setIsOld] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [newExpiryTime, setNewExpiryTime] = useState("");

  const [activeTab, setActiveTab] = useState("shorten");
  const navigate = useNavigate();

  const handleShorten = async () => {
    try {
      setError("");
      setIsOld(false);
      setShortUrl("");
      setSuccess("");
      const response = await axiosInstance.post("/shorten", {
        original_url: longUrl,
      });
      setShortUrl(response.data.short_url);
      setIsOld(response.data.is_old || false);

      getAllUrls();
    } catch (err) {
      setError("Failed to shorten URL. Try again!");
    }
  };

  const handleRetrieve = async () => {
    try {
      setError("");
      setOriginalUrl("");
      const response = await axiosInstance.post(`/resolve`, {
        short_url: shortInput,
      });
      setOriginalUrl(response.data.original_url);
    } catch (err) {
      setError("Short URL not found.");
    }
  };

  const handleUpdateExpiryTime = async () => {
    try {
      setError("");
      setSuccess("");
      const response = await axiosInstance.put(`/update-expiry-time`, {
        expiry_time: newExpiryTime,
        short_url: shortUrl,
      });

      if (response.status === 200) {
        getAllUrls();
        setSuccess("Expiry time updated successfully.");
      }
    } catch (err) {
      setError("Failed to update expiry time. " + err.response.data.message);
    }
  };

  const getAllUrls = async () => {
    try {
      const response = await axiosInstance.get("/urls");
      setUrls(response.data.urls);
    } catch (err) {
      console.error("Error fetching URLs:", err);
    }
  };

  useEffect(() => {
    getAllUrls();
  }, []);

  const handleTabChange = (tab) => {
    setError("");
    setSuccess("");
    setShortUrl("");
    setOriginalUrl("");
    setShortInput("");
    setLongUrl("");
    setIsOld(false);
    setActiveTab(tab);
  }

  return (
    <div className="min-h-screen bg-gray-100 flex flex-col items-center justify-start pt-8 px-4">
      <div className="w-full max-w-3xl text-center mb-8">
        <h1 className="text-3xl font-semibold text-gray-800">
          Welcome, {user?.name}!
        </h1>
        <button
          className="mt-4 bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 transition"
          onClick={() => {
            logout();
            navigate("/login");
          }}
        >
          Logout
        </button>
      </div>

      <div className="max-w-3xl w-full bg-white shadow-xl rounded-lg p-8">
        <div className="flex justify-center mb-6">
          <button
            className={`px-4 py-2 rounded-t-md ${
              activeTab === "shorten"
                ? "bg-blue-500 text-white"
                : "bg-gray-200 text-gray-800"
            }`}
            onClick={() => handleTabChange("shorten")}
          >
            Shorten URL
          </button>
          <button
            className={`px-4 py-2 rounded-t-md ${
              activeTab === "retrieve"
                ? "bg-green-500 text-white"
                : "bg-gray-200 text-gray-800"
            }`}
            onClick={() => handleTabChange("retrieve")}
          >
            Retrieve URL
          </button>

          <button
            className={`px-4 py-2 rounded-t-md ${
              activeTab === "analytics"
                ? "bg-purple-500 text-white"
                : "bg-gray-200 text-gray-800"
            }`}
            onClick={() => handleTabChange("analytics")}
          >
            Analytics
          </button>
        </div>

        {activeTab === "shorten" && (
          <div>
            <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">
              URL Shortener
            </h2>
            <div className="mb-8">
              <input
                type="text"
                placeholder="Enter Long URL"
                className="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-3"
                value={longUrl}
                onChange={(e) => setLongUrl(e.target.value)}
              />
              <button
                onClick={handleShorten}
                className="w-full bg-blue-500 text-white py-3 rounded-md hover:bg-blue-600 transition"
              >
                Shorten URL
              </button>
              {shortUrl && (
                <p className="mt-4 text-lg text-green-600">
                  Short URL:{" "}
                  <a href={shortUrl} target="_blank" className="underline">
                    {shortUrl}
                  </a>
                  {isOld && <span className="text-red-600">(Old)</span>}
                </p>
              )}
            </div>
            {isOld && (
              <div className="flex justify-center items-center">
                <input
                  type="number"
                  placeholder="Enter the minutes you want url to expire in"
                  className="w-1/2 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  value={newExpiryTime}
                  onChange={(e) => setNewExpiryTime(parseInt(e.target.value))}
                />
                <button
                  onClick={handleUpdateExpiryTime}
                  className="w-1/2 bg-blue-500 text-white py-3 rounded-md hover:bg-blue-600 transition"
                >
                  Update Expiry Time
                </button>
              </div>
            )}
          </div>
        )}
        {activeTab === "retrieve" && (
          <div>
            <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">
              Retrieve Original URL
            </h2>
            <div className="mb-8">
              <input
                type="text"
                placeholder="Enter Short URL"
                className="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 mb-3"
                value={shortInput}
                onChange={(e) => setShortInput(e.target.value)}
              />
              <button
                onClick={handleRetrieve}
                className="w-full bg-green-500 text-white py-3 rounded-md hover:bg-green-600 transition"
              >
                Get Original URL
              </button>
              {originalUrl && (
                <p className="mt-4 text-lg text-blue-600">
                  Original URL:{" "}
                  <a href={originalUrl} target="_blank" className="underline">
                    {originalUrl}
                  </a>
                </p>
              )}
            </div>
          </div>
        )}

        {activeTab === "analytics" && (
          <div>
            <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">
              URL Analytics
            </h2>
            <div className="mb-8">
              {urls.length > 0 ? (
                <ul className="space-y-4">
                  {urls.map((url) => (
                    <li
                      key={url.id}
                      className="p-4 border border-gray-300 rounded-md"
                    >
                      <p className="text-lg text-gray-800">
                        Short URL:{" "}
                        <a
                          href={url.short_url}
                          target="_blank"
                          className="underline"
                        >
                          {url.short_url}
                        </a>
                      </p>
                      <p className="text-md text-gray-600">
                        Original URL: {url.original_url}
                      </p>
                      <p className="text-md text-gray-600">
                        Clicks: {url.access_count}
                      </p>
                      <p className="text-md text-gray-600">
                        Expire At: {new Date(url.expiry_time).toLocaleString()}
                      </p>
                    </li>
                  ))}
                </ul>
              ) : (
                <p className="text-gray-600">No URLs found.</p>
              )}
            </div>
          </div>
        )}

        {error && (
          <p className="text-center text-red-600 font-medium">{error}</p>
        )}

        {success && (
          <p className="text-center text-green-600 font-medium">{success}</p>
        )}
      </div>
    </div>
  );
};

export default Dashboard;
