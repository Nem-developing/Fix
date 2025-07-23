import React, { useState, useRef, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { createTicket } from "../services/apiService";
import ConfettiExplosion from "../components/style/ConfettiExplosion";
import { useWindowSize } from "@react-hook/window-size";

const NewTicket = () => {
  const navigate = useNavigate();
  const [showSuccess, setShowSuccess] = useState(false);
  const [width, height] = useWindowSize();
  const successMessageRef = useRef<HTMLHeadingElement>(null);
  const [confettiY, setConfettiY] = useState(0.5);

  const [formData, setFormData] = useState({
    titre: "",
    categorie: "",
    description: "",
    urgence: 0,
  });

  const [categorieError, setCategorieError] = useState(false); // 🔴 État pour l'erreur de catégorie

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

    if (!formData.categorie) {
      setCategorieError(true); // 🔴 active l'erreur si vide
      return;
    } else {
      setCategorieError(false);
    }

    try {
      const result = await createTicket(formData);
      console.log("Ticket créé :", result);
      setShowSuccess(true);

      setTimeout(() => {
        if (successMessageRef.current) {
          const rect = successMessageRef.current.getBoundingClientRect();
          setConfettiY(rect.bottom / height + 0.02);
        }
      }, 50);

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

          <div className="categorie-wrapper">
            <label>Catégorie :</label>
            <div className="categorie-options">
              {[
                "Support technique",
                "Demande d'information",
                "Problème de compte",
                "Suggestion",
                "Bug",
                "Autre",
              ].map((cat) => (
                <button
                  type="button"
                  key={cat}
                  className={`categorie-button ${
                    formData.categorie === cat ? "selected" : ""
                  }`}
                  onClick={() => {
                    setFormData((prev) => ({ ...prev, categorie: cat }));
                    setCategorieError(false); // réinitialise l'erreur au clic
                  }}
                >
                  {cat}
                </button>
              ))}
            </div>
            {categorieError && (
              <p className="form-error">Veuillez sélectionner une catégorie.</p>
            )}
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
