import React, { useState } from "react";
import DumpNombreTicket from "../components/DumpNombreTicket";
import TicketEditModal from "../components/TicketEditModal";

const Ticket = () => {
  const [selectedTicket, setSelectedTicket] = useState<any | null>(null);

  return (
    <div className="Ticket">
      <h1>Tickets</h1>
      <DumpNombreTicket
        format="table"
        data="id,titre,categorie,date"
        onTicketClick={(ticket) => setSelectedTicket(ticket)}
      />

      {selectedTicket && (
        <TicketEditModal
          ticket={selectedTicket}
          onClose={() => setSelectedTicket(null)}
          onSave={(updated) => console.log("Nouveau ticket :", updated)}
        />
      )}
    </div>
  );
};

export default Ticket;
