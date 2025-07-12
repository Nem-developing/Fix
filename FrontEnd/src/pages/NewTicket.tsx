import React, { useState, useRef, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { createTicket } from "../services/apiService";
import ConfettiExplosion from "../components/ConfettiExplosion";
import { useWindowSize } from "@react-hook/window-size";

const NewTicket = () => {
  const navigate = useNavigate();
  const [showSuccess, setShowSuccess] = useState(false);
  const [width, height] = useWindowSize(); // Garde la taille de la fenêtre
  const successMessageRef = useRef<HTMLHeadingElement>(null); // Réf pour le h2 du message de succès
  const [confettiY, setConfettiY] = useState(0.5); // État pour la position Y des confettis

  const [formData, setFormData] = useState({
    titre: "",
    categorie: "",
    description: "",
    urgence: 0,
  });

  const handleChange = (
    e: React.ChangeEvent<
      HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
    >
  ) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: name === "urgence" ? parseInt(value) : value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      const result = await createTicket(formData);
      console.log("Ticket créé :", result);
      setShowSuccess(true);

      // Utilise un petit délai pour s'assurer que le DOM est mis à jour
      // et que le message de succès est visible avant de calculer sa position.
      // Le useRef doit pointer vers le 'h2' car c'est lui qui donne la position du texte.
      setTimeout(() => {
        if (successMessageRef.current) {
          const rect = successMessageRef.current.getBoundingClientRect();
          // Calcule la position Y relative, sous le texte du message de succès.
          setConfettiY(rect.bottom / height + 0.02);
        }
      }, 50); // Un léger délai de 50ms est souvent suffisant

      setTimeout(() => {
        setShowSuccess(false);
        navigate("/dashboard");
      }, 2500);
    } catch (error) {
      console.error("Erreur création ticket :", error);
      alert("Erreur lors de la création du ticket.");
    }
  };

  return (
    <div className="NewTicket">
      {showSuccess && (
        <>
          <div className="success-overlay-background"></div>
          <ConfettiExplosion trigger={showSuccess} yOffset={confettiY} />
          <div className="success-overlay-content">
            <h2 ref={successMessageRef}>🎉 Ticket créé !</h2>
          </div>
        </>
      )}

      <h1>Nouveau Ticket</h1>
      <div className="formdiv">
        <form onSubmit={handleSubmit}>
          <div>
            <label>Titre :</label>
            <input
              type="text"
              name="titre"
              value={formData.titre}
              onChange={handleChange}
              autoComplete="off"
              required
            />
          </div>
          <div>
            <label>Categorie :</label>
            <input
              type="text"
              name="categorie"
              value={formData.categorie}
              onChange={handleChange}
              autoComplete="off"
              required
            />
          </div>
          <div>
            <label>Description :</label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleChange}
              autoComplete="off"
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
