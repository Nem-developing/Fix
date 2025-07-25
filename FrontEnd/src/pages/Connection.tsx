import React, { useState } from "react";

const Connection = () => {
  const [login, setLogin] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");

  const handleSubmit = (event) => {
    event.preventDefault();
  };

  return (
    <div className="Connection">
      <div className="connection-page-wrapper">
        <div className="connection-card">
          <h2 className="connection-title">Connexion</h2>
          <form onSubmit={handleSubmit} className="connection-form">
            <div className="form-group">
              <label htmlFor="login" className="form-label">
                Identifiant :
              </label>
              <input
                type="text"
                id="login"
                className="form-input"
                value={login}
                onChange={(e) => setLogin(e.target.value)}
                required
              />
            </div>
            <div className="form-group">
              <label htmlFor="password" className="form-label">
                Mot de passe :
              </label>
              <input
                type="password"
                id="password"
                className="form-input"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
            <button type="submit" className="connection-button">
              Se connecter
            </button>
          </form>
          {message && <p className="connection-message">{message}</p>}
        </div>
      </div>
    </div>
  );
};

export default Connection;
