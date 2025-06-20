import React, { useState } from "react";
import { createTicket } from "../services/apiService";

const NewTicket = () => {
  // Format du ticket
  const [formData, setFormData] = useState({
    serveur: "",
    objet: "",
    description: "",
    urgence: 0,
  });

  // Vérifie le changement pour modifier seulement les données changé
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: name === "urgence" ? parseInt(value) : value,
    }));
  };

  // Envoie du ticket
  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const result = await createTicket(formData);
      console.log("Ticket créé :", result);
      alert("Ticket créé avec succès !");
      setFormData({
        serveur: "",
        objet: "",
        description: "",
        urgence: 0,
      });
    } catch (error) {
      console.error("Erreur création ticket :", error);
      alert("Erreur lors de la création du ticket.");
    }
  };

  return (
    <div className="NewTicket">
      <h1>Nouveau Ticket</h1>
      <div className="formdiv">
        <form onSubmit={handleSubmit}>
          <div>
            <label>Serveur :</label>
            <input
              type="text"
              name="serveur"
              value={formData.serveur}
              onChange={handleChange}
              required
            />
          </div>
          <div>
            <label>Objet :</label>
            <input
              type="text"
              name="objet"
              value={formData.objet}
              onChange={handleChange}
              required
            />
          </div>
          <div>
            <label>Description :</label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleChange}
              required
            />
          </div>
          <div>
            <label>Urgence :</label>
            <select
              name="urgence"
              value={formData.urgence}
              onChange={handleChange}
            >
              <option value={0}>Faible</option>
              <option value={1}>Moyenne</option>
              <option value={2}>Élevée</option>
            </select>
          </div>
          <button type="submit">Créer le ticket</button>
        </form>
      </div>
    </div>
  );
};

export default NewTicket;
