import React from "react";
import DumpNombreTicket from "../components/DumpNombreTicket";

const Ticket = () => {
  return (
    <div className="Ticket">
      <h1>Tickets</h1>
      <DumpNombreTicket format="table" data="id,titre,categorie,date" />
    </div>
  );
};

export default Ticket;
