// Formulaire lors de la modification d'un ticket

import React from "react";

interface TicketFormProps {
  formData: {
    titre: string;
    categorie: string;
    date: string;
    [key: string]: any;
  };
  onFieldChange: (key: string, value: string) => void;
}

const TicketForm: React.FC<TicketFormProps> = ({ formData, onFieldChange }) => {
  return (
    <>
      <label>Titre :</label>
      <input
        type="text"
        value={formData.titre}
        onChange={(e) => onFieldChange("titre", e.target.value)}
      />

      <label>Catégorie :</label>
      <input
        type="text"
        value={formData.categorie}
        onChange={(e) => onFieldChange("categorie", e.target.value)}
      />

      <label>Date :</label>
      <input
        type="text"
        value={formData.date}
        onChange={(e) => onFieldChange("date", e.target.value)}
      />
    </>
  );
};

export default TicketForm;
