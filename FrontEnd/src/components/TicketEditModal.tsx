import React, { useState } from "react";
import { updateTicket } from "../services/apiService";
import { useNavigate } from "react-router-dom";

interface Ticket {
  id: number;
  titre: string;
  categorie: string;
  date: string;
  [key: string]: any; // Pour supporter d'autres champs
}

interface TicketEditModalProps {
  ticket: Ticket;
  onClose: () => void;
  onSave?: (updatedTicket: Ticket) => void;
}

const TicketEditModal: React.FC<TicketEditModalProps> = ({
  ticket,
  onClose,
  onSave,
}) => {
  const navigate = useNavigate();
  const handleViewTicket = (ticketId: number) => {
    navigate(`/ticket/${ticketId}`);
  };

  const [edited, setEdited] = useState<Ticket>({ ...ticket });

  const handleChange = (key: string, value: string) => {
    setEdited((prev) => ({ ...prev, [key]: value }));
  };

  const handleSave = async () => {
    try {
      const updated = await updateTicket(ticket.id, edited);
      onSave?.(updated);
      onClose();
    } catch (err) {
      console.error("Erreur lors de la mise à jour du ticket :", err);
      alert("Échec de la mise à jour du ticket.");
    }
  };

  return (
    <div className="modal-overlay">
      <div className="modal">
        <h2>Modification du ticket #{ticket.id}</h2>

        <label>Titre :</label>
        <input
          type="text"
          value={edited.titre}
          onChange={(e) => handleChange("titre", e.target.value)}
        />

        <label>Catégorie :</label>
        <input
          type="text"
          value={edited.categorie}
          onChange={(e) => handleChange("categorie", e.target.value)}
        />

        <label>Date :</label>
        <input
          type="text"
          value={edited.date}
          onChange={(e) => handleChange("date", e.target.value)}
        />

        {/* Ajoute d'autres champs si besoin */}

        <div className="buttons">
          <button className="view" onClick={() => handleViewTicket(ticket.id)}>Consulter</button>
          <button className="save" onClick={handleSave}>
            Sauvegarder
          </button>
          <button className="cancel" onClick={onClose}>
            Annuler
          </button>
        </div>
      </div>
    </div>
  );
};

export default TicketEditModal;
