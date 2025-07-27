import React, { useState } from "react";
import useForm from "../hooks/useForm";
import InputField from "../components/utility/InputField";

interface ConnectionFormValues {
  login: string;
  password: string;
}

const Connection: React.FC = () => {
  const [message, setMessage] = useState("");

  const { values, handleChange, handleSubmit } = useForm<ConnectionFormValues>({
    initialValues: { login: "", password: "" },
    onSubmit: async (formValues) => {
      // TODO : Ajouter le service d'authentification
      console.log("Tentative de connexion avec :", formValues);
      setMessage("Connexion en cours...");

      try {
        // Simuler un appel API
        await new Promise((resolve) => setTimeout(resolve, 1000));

        if (
          formValues.login === "admin" &&
          formValues.password === "password"
        ) {
          setMessage("Connexion réussie ! Redirection...");
          // Logique de redirection ou de gestion de l'authentification réussie
        } else {
          setMessage("Identifiants incorrects.");
        }
      } catch (error) {
        console.error("Erreur de connexion :", error);
        setMessage("Une erreur est survenue lors de la connexion.");
      }
    },
  });

  return (
    <div className="Connection">
      <div className="connection-page-wrapper">
        <div className="connection-card">
          <h2 className="connection-title">Connexion</h2>
          <form onSubmit={handleSubmit} className="connection-form">
            <InputField
              id="login"
              label="Identifiant"
              type="text"
              value={values.login}
              onChange={handleChange}
              required
            />
            <InputField
              id="password"
              label="Mot de passe"
              type="password"
              value={values.password}
              onChange={handleChange}
              required
            />
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
