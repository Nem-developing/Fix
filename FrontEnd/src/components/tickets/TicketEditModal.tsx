import React, { useState } from "react";
import { updateTicket } from "../../services/apiService";
import { useNavigate } from "react-router-dom";
import useModalClose from "../../hooks/useModalClose";
import TicketForm from "../form/TicketForm";

interface Ticket {
  id: number;
  titre: string;
  categorie: string;
  date: string;
  [key: string]: any;
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
  const { modalContentRef, isClosing, handleCloseWithAnimation } =
    useModalClose({ onClose });

  const [edited, setEdited] = useState<Ticket>({ ...ticket });

  const handleChange = (key: string, value: string) => {
    setEdited((prev) => ({ ...prev, [key]: value }));
  };

  const handleSave = async () => {
    try {
      const updated = await updateTicket(ticket.id, edited);
      onSave?.(updated);
      handleCloseWithAnimation();
    } catch (err) {
      console.error("Erreur lors de la mise à jour du ticket :", err);
      alert("Échec de la mise à jour du ticket.");
    }
  };

  const handleViewTicket = (ticketId: number) => {
    navigate(`/ticket/${ticketId}`);
    handleCloseWithAnimation();
  };

  return (
    <div className="modal-overlay">
      <div
        className={`modal ${isClosing ? "closing" : ""}`}
        ref={modalContentRef}
      >
        <h2>Modification du ticket #{ticket.id}</h2>
        <TicketForm formData={edited} onFieldChange={handleChange} />
        <div className="buttons">
          <button className="view" onClick={() => handleViewTicket(ticket.id)}>
            Consulter
          </button>
          <button className="save" onClick={handleSave}>
            Sauvegarder
          </button>
          <button className="cancel" onClick={handleCloseWithAnimation}>
            Annuler
          </button>
        </div>
      </div>
    </div>
  );
};

export default TicketEditModal;
